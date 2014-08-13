<?php
   require_once("GetConnect.php");

   function ConvertDb($infile) {
      $dbname = "back_up_catalog";
      $res = getConnection($dbname);
      $dbcon = $res[0];
      $params = $res[1];
      $pg_res = pg_query($dbcon, "DROP SCHEMA IF EXISTS catalog CASCADE;") or die("Can't drop previous schema!\n");
      echo $infile . "\n";
      system("pg_restore --dbname='" . $dbname . "' " . $infile);
      pg_query($dbcon, "SET datestyle='ISO, MDY'");
      //create sqlite file
      $dt = new Datetime();
      $db_sqlite_sname = $dt->getTimestamp() . ".db";
      $db_sqlite_name = dirname(__FILE__) . "/../web/static/" . $db_sqlite_sname;
      echo $db_sqlite_name . "\n";
      $db_sqlite = new SQLite3($db_sqlite_name);
      if (!$db_sqlite) {
         die("sqlite_creation_failed\n");
      }
      $db_sqlite->query(
                  'CREATE TABLE IF NOT EXISTS buildings ( ' .
                     'id INTEGER PRIMARY KEY NOT NULL, ' .
                     'number INTEGER, ' .
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

      $result = pg_query($dbcon, 'SELECT id, number, alias, lat, lon FROM catalog.buildings;');
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query('INSERT INTO BUILDINGS (id, number, alias, lat, lon) VALUES (' . $row[0] . ", '" . $row[1] . "', '" . $row[2] . "', " . $row[3] . ", " . $row[4] . ");");
      }
      $result = pg_query($dbcon, 'SELECT id, alias FROM catalog.object_types;');
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query("INSERT INTO object_types (id, alias) VALUES (" . $row[0] . ", '" . $row[1] . "');");
      }
      $result = pg_query($dbcon, 'SELECT id, type_id, building_id, level, alias, lat, lon FROM catalog.objects;');
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO objects(id, type_id, building_id, level, alias, lat, lon) VALUES ( " .
            $row[0] . ", " . $row[1] . ", " . $row[2] . ", " . $row[3] . ", '" . $row[4] . "', " . $row[5] . ", " . $row[6] . ");"
         );
      }
      $db_sqlite->close();
      pg_close($dbcon);
      $res = getConnection("");
      $dbcon = $res[0];
      $result = pg_query($dbcon, "INSERT INTO VERSIONS (v_datetime, base, type) VALUES (" . $dt->getTimestamp() . ", '$db_sqlite_sname', -20);");
      if (!$result) {
         echo "Insertion failed!\n";
      }
      pg_close($dbcon);
   }

   function AddPlan($dbname, $level)
   {
      if (!file_exists($dbname)) {
         die("input error: no such file\n");
      }
      $dt = new DateTime();
      $new_name = "plan_" . $level . "_" .$dt->getTimestamp();
      $path = dirname(__FILE__) . '/../web/static/';
      if (!copy($dbname, $path . $new_name)) {
         die("copy error\n");
      }
      $res = getConnection("");
      $dbcon = $res[0];
      $result = pg_query($dbcon, "INSERT INTO VERSIONS (v_datetime, base, type) VALUES (" . $dt->getTimestamp() . ", '$new_name', $level);");
      if (!$result) {
         echo "Insertion failed!\n";
      }
      pg_close($dbcon);
   }




