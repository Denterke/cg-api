<?php
   require_once("GetConnect.php");

   function ConvertDb($infile) {
      $dbname_bu = "back_up_catalog";
      $res = getConnection($dbname_bu);
      $dbcon_bu = $res[0];
      $params = $res[1];
      $res = getConnection();
      $dbcon_base = $res[0];
      $pg_res = pg_query($dbcon_bu, "DROP SCHEMA IF EXISTS catalog CASCADE;") or die("Can't drop previous schema!\n");
      echo $infile . "\n";
      system("pg_restore --dbname='" . $dbname_bu . "' " . $infile);
      pg_query($dbcon_bu, "SET datestyle='ISO, MDY'");
      //create sqlite file
      $dt = new Datetime();
      $db_sqlite_sname = "catalog" . date('dmY_Gis', $dt->getTimestamp()) . ".db";
      $db_sqlite_name = dirname(__FILE__) . "/../web/static/" . $db_sqlite_sname;
      echo $db_sqlite_name . "\n";
      $db_sqlite = new SQLite3($db_sqlite_name);
      if (!$db_sqlite) {
         die("sqlite_creation_failed\n");
      }
      $db_sqlite->exec("pragma synchronous = off;");
      $db_sqlite->query(
                  "CREATE TABLE IF NOT EXISTS buildings (
                     id INTEGER PRIMARY KEY NOT NULL,
                     number VARCHAR,
                     alias VARCHAR,
                     lon DOUBLE,
                     lat DOUBLE
                  );"
      );
      $db_sqlite->query(
                  "CREATE TABLE IF NOT EXISTS object_types (
                     id INTEGER PRIMARY KEY NOT NULL,
                     alias VARCHAR,
                     display INTEGER
                  );"
      );
      $db_sqlite->query(
                  "CREATE TABLE IF NOT EXISTS node_types (
                     id INTEGER PRIMARY KEY NOT NULL,
                     alias VARCHAR
                  );"
      );
      $db_sqlite->query(
                  "CREATE TABLE IF NOT EXISTS objects (
                     id INTEGER PRIMARY KEY NOT NULL,
                     type_id INTEGER NOT NULL,
                     building_id INTEGER NOT NULL,
                     level INTEGER NOT NULL,
                     alias VARCHAR,
                     node_id INTEGER NOT NULL,
                     lat DOUBLE,
                     lon DOUBLE,
                     FOREIGN KEY(type_id) REFERENCES object_types(id),
                     FOREIGN KEY(building_id) REFERENCES buildings(id),
                     FOREIGN KEY(node_id) REFERENES node_types(id)
                  );"
      );
      $db_sqlite->query(
                  "CREATE TABLE IF NOT EXISTS path_segments (
                     id INTEGER PRIMARY KEY NOT NULL,
                     level INTEGER,
                     id_vertex_from INTEGER NOT NULL,
                     id_vertex_to INTEGER NOT NULL,
                     FOREIGN KEY(id_vertex_from) REFERENCES objects(id),
                     FOREIGN KEY(id_vertex_to) REFERENCES objects(id)
                  );"
      );
      $db_sqlite->query(
                  "CREATE TABLE IF NOT EXISTS path_segment_points (
                     id INTEGER PRIMARY KEY NOT NULL,
                     path_id INTEGER NOT NULL,
                     lon DOUBLE,
                     lat DOUBLE,
                     idx INTEGER,
                     FOREIGN KEY(path_id) REFERENCES path_segments(id)
                  );"
      );

      $db_sqlite->query('DELETE FROM path_segments');
      $db_sqlite->query('DELETE FROM objects');
      $db_sqlite->query('DELETE FROM buildings');
      $db_sqlite->query('DELETE FROM object_types');
      $db_sqlite->query('DELETE FROM node_types');
      $db_sqlite->query('DELETE FROM path_segment_points');

      // echo "buildings:\n";
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
      //********************************
      //********************************
      //**********OBJECT_TYPES**********
      //********************************
      //********************************
      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, alias, displayed
         FROM
            catalog.object_types;"
      );
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO
               object_types (id, alias, display)
            VALUES
               ($row[0], '$row[1]', $row[2])"
         );
         $subres = pg_query(
            $dbcon_base,
            "SELECT
               id, alias, displayed
            FROM
               geoobject_types
            WHERE id = $row[0];"
         );
         if (pg_num_rows($subres) == 0) {
            $subres = pg_query(
               $dbcon_base,
               "INSERT INTO
                  geoobject_types (id, alias, displayed)
               VALUES
                  ($row[0], '$row[1]', $row[2]);"
            );
         } else {
            $subres = pg_query(
               $dbcon_base,
               "UPDATE
                  geoobject_types
               SET
                  alias = '$row[1]', displayed = $row[2]
               WHERE
                  id = $row[0];"
            );
         }
      }

      //********************************
      //********************************
      //***********NODE_TYPES***********
      //********************************
      //********************************
      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, alias
         FROM
            catalog.node_types;"
      );
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO
               node_types (id, alias)
            VALUES
               ($row[0], '$row[1]')"
         );
         $subres = pg_query(
            $dbcon_base,
            "SELECT
               id, alias
            FROM
               node_types
            WHERE id = $row[0];"
         );
         if (pg_num_rows($subres) == 0) {
            $subres = pg_query(
               $dbcon_base,
               "INSERT INTO
                  node_types (id, alias)
               VALUES
                  ($row[0], '$row[1]');"
            );
         } else {
            $subres = pg_query(
               $dbcon_base,
               "UPDATE
                  node_types
               SET
                  alias = '$row[1]'
               WHERE
                  id = $row[0];"
            );
         }
      }

      // exit;
      //********************************
      //********************************
      //***********GEO_OBJECTS**********
      //********************************
      //********************************
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
         "DELETE FROM PATH_SEGMENT_POINTS psp;"
      );
      $result = pg_query(
         $dbcon_base,
         "DELETE FROM PATH_SEGMENTS ps;"
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
      foreach($obj_ids as &$elem) {
         array_push($ids, $elem['id']);
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
         if ($ids && in_array($row[0], $ids)) {
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

      //********************************
      //********************************
      //***********PATH_SEGMENTS********
      //********************************
      //********************************

      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, level, id_vertex_from, id_vertex_to
         FROM
            catalog.path_segments;"
      );
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO
               path_segments (id, level, id_vertex_from, id_vertex_to)
            VALUES
               ($row[0], $row[1], $row[2], $row[3])"
         );
         $subres = pg_query(
            $dbcon_base,
            "SELECT
               id
            FROM
               path_segments
            WHERE id = $row[0];"
         );
         if (pg_num_rows($subres) == 0) {
            $subres = pg_query(
               $dbcon_base,
               "INSERT INTO
                  path_segments (id, level, object_from_id, object_to_id)
               VALUES
                  ($row[0], $row[1], $row[2], $row[3]);"
            );
         } else {
            $subres = pg_query(
               $dbcon_base,
               "UPDATE
                  path_segments
               SET
                  level = $row[1], object_from_id = $row[2], object_to_id = $row[3]
               WHERE
                  id = $row[0];"
            );
         }
      }

      //********************************
      //********************************
      //*******PATH_SEGMENT_POINTS******
      //********************************
      //********************************

      $result = pg_query(
         $dbcon_bu,
         "SELECT
            id, path_id, lon, lat, idx
         FROM
            catalog.path_segment_points;"
      );
      if (!$result) {
         die("selection failed\n");
      }
      while ($row = pg_fetch_row($result)) {
         $db_sqlite->query(
            "INSERT INTO
               path_segment_points (id, path_id, lon, lat, idx)
            VALUES
               ($row[0], $row[1], $row[2], $row[3], $row[4])"
         );
         $subres = pg_query(
            $dbcon_base,
            "SELECT
               id
            FROM
               path_segment_points
            WHERE id = $row[0];"
         );
         if (pg_num_rows($subres) == 0) {
            $subres = pg_query(
               $dbcon_base,
               "INSERT INTO
                  path_segment_points (id, path_id, lon, lat, idx)
               VALUES
                  ($row[0], $row[1], $row[2], $row[3], $row[4]);"
            );
         } else {
            $subres = pg_query(
               $dbcon_base,
               "UPDATE
                  path_segment_points
               SET
                  path_id = $row[1], lon = $row[2], lat = $row[3], idx = $row[4]
               WHERE
                  id = $row[0];"
            );
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
      $dt = new DateTime();
      $timestamp = date('dmY_Gis', $dt->getTimestamp());
      $new_name = "plan_" . $level . "_" . $timestamp;
      $timestamp = $dt->getTimestamp();
      $path = dirname(__FILE__) . '/../web/static/';
      if (!copy($dbname, $path . $new_name)) {
         die("copy error\n");
      }
      $res = getConnection();
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




