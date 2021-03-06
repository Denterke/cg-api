<?php
namespace Farpost\StoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Farpost\StoreBundle\Entity\Version;

class DatabaseConverter
{
   private $doctrine;
   private $sqlite_manager;
   private $entity_dispatcher;
   private $schedule_manager;

   private function SoftExit($msg)
   {
      echo "<p>ERROR:$msg</p>";
      exit;
   }

   private function zipifyLast($timestamp, $em)
   {
      $files = $em->getRepository('FarpostStoreBundle:Version')->getFileNames();
      $filename = "plans_zip_{$timestamp}.zip";
      $filepath = STATIC_DIR . "/$filename";
      $zip = new \ZipArchive();
      if ($zip->open($filepath, \ZipArchive::CREATE) !== TRUE) {
         throw new Exception('Возникли проблемы при загрузке файлов.');
      }
      $idx = 0;
      foreach ($files as $file) {
         $idx++;
         $zip->addFile($file, $idx . '.mbtiles');
      }
      $zip->close();
      return $filename;
   }

   public function __construct($doctrine, $sqlite_manager, $entity_dispatcher, $schedule_manager)
   {
      $this->doctrine = $doctrine;
      $this->sqlite_manager = $sqlite_manager;
      $this->entity_dispatcher = $entity_dispatcher;
      $this->schedule_manager = $schedule_manager;
   }

   public function AddDb($type, $dbname)
   {
      switch ($type) {
         case Version::CATALOG:
            $this->ConvertDb($dbname);
            break;
         default:
            $this->AddPlan($dbname, $type);
            break;
      }
   }

   private function ConvertDb($infile) {
      // echo $infile;
      // $dt = new \Datetime();
      // error_log("cdb start " . $dt->getTimestamp());
      $em_bu = $this->doctrine->getManager('back_up');
      $em_ba = $this->doctrine->getManager('default');

      $dbname_bu = "back_up_catalog";
      $owner = "back_up_catalog";   
      echo "<p>$infile</p>";
      $pg_err_num = 0;
      $pg_log_file = __DIR__ . '/../../../../web/uploads/documents/tmp_log.txt';
      // system("/usr/bin/psql -c -d $dbname_bu -c 'CREATE SCHEMA IF NOT EXISTS catalog;'");
      system("/usr/bin/pg_restore --host=localhost -U $owner -c -O -d $dbname_bu --schema=catalog $infile > $pg_log_file 2>&1", $pg_err_num);
      $pg_log = file_get_contents($pg_log_file);
      if ($pg_err_num) {
         echo $pg_err_num;
         echo $pg_log;
         // throw new \Exception("pg_restore failed!");
      }
      //create sqlite file
      try {
         list($db_sqlite, $dt, $db_sqlite_name) = $this->sqlite_manager->createDb();
         $tables = $this->sqlite_manager->getCatalogv1Tables();
         $this->sqlite_manager->createTables($tables, $db_sqlite);
         $this->sqlite_manager->clearTables($tables, $db_sqlite);
      }
      catch (\Exception $e) {
         // echo $e->message;
         $this->SoftExit($e->getMessage());
      }
      echo "i create sqlite file!";
      //$this->schedule_manager->rawClear();
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
            $this->sqlite_manager->groupInsert($table, $bu_items, $db_sqlite);
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
      $version->setVDateTime($timestamp)->setBase($db_sqlite_name)->setType(Version::CATALOG);
      $em_ba->persist($version);
      $em_ba->flush();
      // $dt = new \Datetime();
      // error_log("cdb end " . $dt->getTimestamp());
   }

   function AddPlan($dbname, $level)
   {
      if (!file_exists($dbname)) {
         die("input error: no such file\n");
      }
      $dt = new \DateTime();
      $timestamp = date('dmY_Gis', $dt->getTimestamp());
      $new_name = ($level == Version::MAP ? "map_" : "plan_{$level}_") . $timestamp . '.mbtiles';
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
      $zipName = $this->zipifyLast($timestamp, $em);
      $version = new Version();
      $version->setVDateTime($timestamp)->setBase($zipName)->setType(Version::ZIP_PLANS);
      $em->persist($version);
      $em->flush();
   }

}
