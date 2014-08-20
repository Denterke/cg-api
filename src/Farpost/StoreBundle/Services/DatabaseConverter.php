<?php
namespace Farpost\StoreBundle\Services;

class DatabaseConverter
{
   function AddDb($type, $dbname) {
      if ($type == -20) {
         $this->ConvertDb($dbname);
      } else {
         $this->AddPlan($dbname, $type);
      }
   }

   function getValue($src) {
      $left = strpos($src, '"');
      $right = strpos($src, '"', $left + 1);
      return mb_substr($src, $left + 1, strlen($src) - $left - 3);
   }

   function getConnection($dbname = "") {
      echo "Let's create some connection....\n";
      $path = dirname(__FILE__);
      $symfConfig = fopen($path . '/../../../../app/config/config.yml', 'r');
      if (!$symfConfig) {
         die("Problem with paths!\n");
      }
      echo "Symfony config found!\n";
      $str = fgets($symfConfig);
      while (fgets($symfConfig) != "doctrine:\n") { }
      $paramCount = 0;
      $params = [
         "dbname" => "",
         "user" => "",
         "password" => "",
         "port" => "",
         "host" => ""
      ];
      $connection_string = "";
      if ($dbname != '') {
         $params["dbname"] = $dbname;
      }
      while ($paramCount < count($params)) {
         $str = fgets($symfConfig);
         foreach ($params as $paramName => $paramVal) {
            if (strpos($str, $paramName)) {
               $val = $paramVal == "" ? $this->getValue($str) : $paramVal;
               $connection_string .= $paramName . " = " . $val . " ";
               $params[$paramName] = $val;
               $paramCount++;
            }
         }
      }
      echo "Your's connection string:\n".$connection_string . "\n";
      $dbcon = pg_connect($connection_string);
      if (!$dbcon) {
         die('Connection failed!');
      } else {
         echo "Connection succeed!\n";
      }
      // pg_close($dbcon);
      return [$dbcon, $params];
   }

   function ConvertDb($infile) {
      $dbname_bu = "back_up_catalog";
      $res = $this->getConnection($dbname_bu);
      $dbcon_bu = $res[0];
      $params = $res[1];
      $res = $this->getConnection();
      $dbcon_base = $res[0];
      $pg_res = pg_query($dbcon_bu, "DROP SCHEMA IF EXISTS catalog CASCADE;") or die("Can't drop previous schema!\n");
      echo $infile . "\n";
      system("pg_restore --dbname='" . $dbname_bu . "' " . $infile);
      pg_query($dbcon_bu, "SET datestyle='ISO, MDY'");
      //create sqlite file
      $dt = new \Datetime();
      $db_sqlite_sname = "catalog" . date('dmY_Gis', $dt->getTimestamp()) . ".db";
      $db_sqlite_name = dirname(__FILE__) . "/../../../../web/static/" . $db_sqlite_sname;
      echo $db_sqlite_name . "\n";
      $db_sqlite = new \SQLite3($db_sqlite_name);
      if (!$db_sqlite) {
         die("sqlite_creation_failed\n");
      }
      $db_sqlite->exec("pragma synchronous = off;");
      $db_sqlite->query(
                  'CREATE TABLE IF NOT EXISTS buildings ( ' .
                     'id INTEGER PRIMARY KEY NOT NULL, ' .
                     'number VARCHAR, ' .
                     'alias VARCHAR, '.
                     'lon DOUBLE, ' .
                     'lat DOUBLE ' .
                  ');'
      );
      $db_sqlite->query(
                  'CREATE TABLE IF NOT EXISTS object_types ( ' .
                     'id INTEGER PRIMARY KEY NOT NULL, ' .
                     'alias VARCHAR ' .
                  ');'
      );
      $db_sqlite->query(
                  'CREATE TABLE IF NOT EXISTS objects ( ' .
                     'id INTEGER PRIMARY KEY NOT NULL, ' .
                     'type_id INTEGER NOT NULL, ' .
                     'building_id INTEGER NOT NULL, ' .
                     'level INTEGER NOT NULL, ' .
                     'alias VARCHAR, ' .
                     'lat DOUBLE, ' .
                     'lon DOUBLE, ' .
                     'FOREIGN KEY(type_id) REFERENCES object_types(id), ' .
                     'FOREIGN KEY(building_id) REFERENCES buildings(id) ' .
                  ');'
      );

      $db_sqlite->query('DELETE FROM objects');
      $db_sqlite->query('DELETE FROM buildings');
      $db_sqlite->query('DELETE FROM object_types');
      echo "buildings:\n";
      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, number, alias, lat, lon
         FROM
            catalog.buildings;
         ");
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO
               BUILDINGS (id, number, alias, lat, lon)
            VALUES
               ($row[0], '$row[1]', '$row[2]', $row[3], $row[4]);"
         );
         $subres = pg_query(
            $dbcon_base,
            "SELECT
               id, number, alias, lat, lon
            FROM
               buildings
            WHERE
               id = $row[0]"
         );
         if (pg_num_rows($subres) == 0) {
            $subres = pg_query(
               $dbcon_base,
               "INSERT INTO
                  BUILDINGS (id, number, alias, lat, lon)
               VALUES
                  ($row[0], '$row[1]', '$row[2]', $row[3], $row[4])"
            );
         } else {
            $subres = pg_query(
               $dbcon_base,
               "UPDATE
                  BUILDINGS
               SET
                  number = '$row[1]',
                  alias = '$row[2]',
                  lat = $row[3],
                  lon = $row[4]
               WHERE
                  id = $row[0];"
            );
         }
      }
      //object_types
      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, alias
         FROM
            catalog.object_types;"
      );
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO
               object_types (id, alias)
            VALUES
               ($row[0], '$row[1]')"
         );
         $subres = pg_query(
            $dbcon_base,
            "SELECT
               id, alias
            FROM
               geoobject_types
            WHERE id = $row[0];"
         );
         if (pg_num_rows($subres) == 0) {
            $subres = pg_query(
               $dbcon_base,
               "INSERT INTO
                  geoobject_types (id, alias)
               VALUES
                  ($row[0], '$row[1]');"
            );
         } else {
            $subres = pg_query(
               $dbcon_base,
               "UPDATE
                  geoobject_types
               SET
                  alias = '$row[1]'
               WHERE
                  id = $row[0];"
            );
         }
      }
      // exit;
      //geoobjects
      $result = pg_query(
         $dbcon_base,
         "DELETE FROM
            SCHEDULE s
         WHERE s.auditory_id in (
            SELECT
               go.id
            FROM
               GEOOBJECTS go
            WHERE
               go.cataloged = 0 AND
               go.geoobject_type_id = 6
         );"
      );
      $result = pg_query(
         $dbcon_base,
         "DELETE FROM
            GEOOBJECTS go
         WHERE
            go.cataloged = 0
         ;"
      );
      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, type_id, building_id, level, alias, lat, lon
         FROM
            catalog.objects;"
      );
      if (!$result) {
         die("selection failed\n");
      }
      $subres = pg_query(
         $dbcon_base,
         "SELECT
            go.id
         FROM
            GEOOBJECTS go;"
      );
      $ids = [];
      $obj_ids = pg_fetch_all($subres);
      var_dump($obj_ids);
      if ($obj_ids) {
         foreach($obj_ids as $elem) {
            array_push($ids, $elem['id']);
         }
      }
      unset($obj_ids);
      $query = "";
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            'INSERT INTO objects(id, type_id, building_id, level, alias, lat, lon) VALUES ( '.
            $row[0] . ", " .
            $row[1] . ", " .
            $row[2] . ", " .
            $row[3] . ", '" .
            $row[4] . "', " .
            $row[5] . ", " .
            $row[6] . ");"
         );
         if ($ids && count($ids) > 0 && in_array($row[0], $ids)) {
            $query .=
               "UPDATE
                  GEOOBJECTS
               SET
                  geoobject_type_id = $row[1],
                  building_id = $row[2],
                  level = $row[3],
                  alias = '$row[4]',
                  lat = $row[5],
                  lon = $row[6],
                  cataloged = 1
               WHERE
                  id = $row[0];";
         } else {
            $query .=
               "INSERT INTO
                  GEOOBJECTS(id, geoobject_type_id, building_id, level, alias, lat, lon, cataloged)
               VALUES
                  ($row[0], $row[1], $row[2], $row[3], '$row[4]', $row[5], $row[6], 1);";
         }
      }
      pg_query($dbcon_base, $query);
      $db_sqlite->close();
      pg_close($dbcon_bu);
      //versions
      $timestamp = $dt->getTimestamp();
      $result = pg_query(
         $dbcon_base,
         "INSERT INTO
            VERSIONS(v_datetime, base, type)
         VALUES
            ($timestamp, '$db_sqlite_sname', -20);"
      );
      if (!$result) {
         echo "Insertion failed!\n";
      }
      pg_close($dbcon_base);
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
      $res = $this->getConnection();
      $dbcon = $res[0];
      $result = pg_query(
         $dbcon,
         "INSERT INTO
            VERSIONS (v_datetime, base, type)
         VALUES
            ($timestamp, '$new_name', $level);"
      );
      if (!$result) {
         echo "Insertion failed!\n";
      }
      pg_close($dbcon);
   }


}
