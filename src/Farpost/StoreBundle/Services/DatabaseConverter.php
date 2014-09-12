<?php
namespace Farpost\StoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Farpost\StoreBundle\Entity\Version;

class DatabaseConverter
{
   private $doctrine;
   private $sqlite_manager;
   private $entity_dispatcher;

   private function SoftExit($msg)
   {
      echo "<p>ERROR:$msg</p>";
      exit;
   }

   public function __construct($doctrine, $sqlite_manager, $entity_dispatcher)
   {
      $this->doctrine = $doctrine;
      $this->sqlite_manager = $sqlite_manager;
      $this->entity_dispatcher = $entity_dispatcher;
   }

   public function AddDb($type, $dbname)
   {
      if ($type == -20) {
         $this->ConvertDb($dbname);
      } else {
         $this->AddPlan($dbname, $type);
      }
   }

   private function ConvertDb($infile) {
      // echo $infile;
      $em_bu = $this->doctrine->getManager('back_up');
      $em_ba = $this->doctrine->getManager('default');

      $dbname_bu = "back_up_catalog";
      echo "<p>$infile</p>";
      $pg_restore_log = '';
      system("pg_restore --clean --dbname='$dbname_bu' $infile -all > pg_r_log.txt 2>&1
", $pg_err_num);
      if ($pg_err_num) {
         $pg_log = file_get_contents("pg_r_log.txt");
         echo "<p>pg_log: $pg_log</p>";
         throw new \Exception("pg_restore failed!");
      }


      //create sqlite file
      try {
         list($db_sqlite, $dt, $db_sqlite_name) = $this->sqlite_manager->CreateDb();
         $tables = $this->sqlite_manager->Tables();
         $this->sqlite_manager->CreateTables($tables, $db_sqlite);
         $this->sqlite_manager->ClearTables($tables, $db_sqlite);
      }
      catch (\Exception $e) {
         // echo $e->message;
         $this->SoftExit($e->message);
      }
      echo "i create sqlite file!";

      foreach($tables as &$table) {
         echo "<p>now table name is $table[table]</p>";
         $bu_items = $em_bu->getRepository(
            $this->entity_dispatcher->tableToEntity(
               $table['table'],
               true,
               true
            )
         )->getRawResults();
         try {
            $this->sqlite_manager->GroupInsert($table, $bu_items, $db_sqlite);
         }
         catch (\Exception $e) {
            // echo $e->message;
            $this->SoftExit($e->getMessage());
         }
         try {
            $base_entity = $this->entity_dispatcher->tableToEntity(
               $table['table'],
               false,
               true
            );
            if (!$base_entity) {
               continue;
            }
            $em_ba->getRepository($base_entity)->synchronizeWith($bu_items);
         }
         catch (\Exception $e) {
            $this->SoftExit($e->getMessage() . " table = $table[table]");
         }
      }

      $db_sqlite->close();
      $timestamp = $dt->getTimestamp();
      $version = new Version();
      $version->setVDateTime($timestamp)->setBase($db_sqlite_name)->setType(-20);
      $em_ba->persist($version);
      $em_ba->flush();
   }

   function AddPlan($dbname, $level)
   {
      if (!file_exists($dbname)) {
         die("input error: no such file\n");
      }
      $dt = new \DateTime();
      $timestamp = date('dmY_Gis', $dt->getTimestamp());
      $new_name = "plan_" . $level . "_" . $timestamp;
      $timestamp = $dt->getTimestamp();
      $path = dirname(__FILE__) . '/../../../../web/static/';
      if (!copy($dbname, $path . $new_name)) {
         die("copy error\n");
      }
      $em = $this->doctrine->getManager('default');
      $version = new Version();
      $version->setVDateTime($timestamp)->setBase($new_name)->setType($level);
      $em->persist($version);
      $em->flush();
   }


}
