 <?php
   function setUpdating(&$conn, &$table_name)
   {
      $foo_name = $table_name . '_func_upd()';
      $qname = '\'' . $table_name . '\'';
      echo "creating function " . $foo_name . "\n";
      $trig_name = $table_name . '_trig_upd';
      $foo = "CREATE OR REPLACE FUNCTION  $foo_name RETURNS trigger AS
         \$BODY\$
         DECLARE
               c INTEGER;
         BEGIN
            IF (TG_OP = 'INSERT') THEN
               SELECT count(*) INTO c FROM last_modified
                  WHERE table_name=$qname and record_id = NEW.id;
               IF (c > 0) THEN
                  UPDATE last_modified SET last_modified = NOW(), status = 1
                     WHERE table_name = $qname AND record_id = NEW.id;
               ELSIF (c = 0) THEN
                  INSERT INTO last_modified(table_name, record_id, last_modified, status)
                     VALUES($qname, NEW.id, NOW(), 1);
               END IF;
               RETURN NEW;
            ELSIF (TG_OP = 'UPDATE') THEN
               UPDATE last_modified SET last_modified = NOW(), status = 1 WHERE
                  table_name = $qname AND record_id = NEW.id;
               RETURN NEW;
            ELSIF (TG_OP = 'DELETE') THEN
               UPDATE last_modified SET last_modified = NOW(), status = 3 WHERE
                  table_name = $qname AND record_id = OLD.id;
               RETURN NEW;
            END IF;
         END;
         \$BODY\$ LANGUAGE plpgsql";
      $result = pg_query($conn, $foo);
      if (!$result) {
         die("function creation failed\n");
      }
      echo "creating trigger " . $trig_name . "\n";
      $trigger = "DROP TRIGGER IF EXISTS $trig_name ON $table_name;
                 CREATE TRIGGER $trig_name AFTER INSERT OR UPDATE OR DELETE
                 ON $table_name FOR EACH row EXECUTE PROCEDURE $foo_name;";
      $result = pg_query($conn, $trigger);
      if (!$result) {
         die("trigger creation failed\n");
      }
      echo "trigger added on table " . $table_name . "\n";
   }

   function setSyncFunction(&$conn)
   {
      $foo =   "CREATE OR REPLACE FUNCTION groupInfoSync(school VARCHAR, st VARCHAR, spec VARCHAR, course VARCHAR, dep VARCHAR, g VARCHAR) RETURNS integer AS
               \$BODY\$
               DECLARE
                  s_id INTEGER;
                  st_id INTEGER;
                  spec_id INTEGER;
                  c_id INTEGER;
                  dep_id INTEGER;
                  g_id INTEGER;
                  ss_id INTEGER;
               BEGIN
                  s_id = (SELECT id FROM schools WHERE alias = \$1 LIMIT 1);
                  IF (s_id IS NULL) THEN 
                     INSERT INTO schools(alias) VALUES (\$1) returning id INTO s_id;
                  END IF;
                  --study_type
                  st_id = (SELECT id FROM study_types WHERE alias = \$2 LIMIT 1);
                  IF (st_id IS NULL) THEN 
                     INSERT INTO study_types(alias) VALUES (\$2) returning id INTO st_id;
                  END IF;
                  --specialization
                  spec_id = (SELECT id FROM specializations WHERE alias = \$3 LIMIT 1);
                  IF (spec_id IS NULL) THEN 
                     INSERT INTO specializations(alias) VALUES (\$3) returning id INTO spec_id;
                  END IF;
                  --course
                  c_id = (SELECT id FROM courses WHERE alias = \$4 LIMIT 1);
                  if (c_id IS NULL) THEN
                     INSERT INTO courses(alias) VALUES (\$4) returning id INTO c_id;
                  END IF;
                  --department
                  dep_id = (SELECT id FROM departments WHERE school_id = s_id AND study_type_id = st_id AND alias = \$5);
                  if (dep_id IS NULL) THEN
                     INSERT INTO departments(school_id, study_type_id, alias) VALUES (s_id, st_id, \$5) returning id INTO dep_id;
                  END IF;
                  --study_set
                  ss_id = (
                     SELECT 
                        ss.id
                     FROM
                        study_sets ss
                     INNER JOIN
                        department_sets ds
                     ON ds.study_set_id = ss.id
                     WHERE
                        ss.specialization_id = spec_id
                     AND
                        ss.course_id = c_id
                     AND
                        ds.department_id = dep_id
                     LIMIT 1);
                  if (ss_id IS NULL) THEN
                     INSERT INTO study_sets (specialization_id, course_id) VALUES (spec_id, c_id) returning id INTO ss_id;
                     INSERT INTO department_sets (study_set_id, department_id) VALUES (ss_id, dep_id);
                  END IF;
                  --group
                  g_id = (SELECT id FROM groups WHERE alias = \$6);
                  IF (g_id IS NULL) THEN
                     INSERT INTO groups(alias, study_set_id) VALUES(\$6, ss_id) returning id INTO g_id;
                     RETURN g_id;
                  END IF;
                  DELETE FROM
                     schedule_rendered sr
                  WHERE
                     sr.id 
                  IN
                     (SELECT
                        sr2.id
                      FROM
                        schedule_rendered sr2
                      INNER JOIN
                        schedule s
                      ON 
                        sr2.schedule_id = s.id
                      INNER JOIN
                        schedule_parts sp
                      ON
                        s.schedule_part_id = sp.id
                      WHERE
                        sp.group_id = g_id
                      AND
                        sr2.exec_date >= NOW()
                     );
                  RETURN g_id;
               END;
               \$BODY\$ LANGUAGE plpgsql";
      $result = pg_query($conn, $foo);
      if (!$result) {
         die("function creation failed\n");
      }
   }

   function setRenderScheduleFunction(&$conn)
   {
      $foo =   "CREATE OR REPLACE FUNCTION renderSchedule(s_id INTEGER) RETURNS void AS
               \$BODY\$
               DECLARE
                  vals TEXT;
                  t    RECORD;
               BEGIN
                  SELECT sm.time_start, sm.time_end, s.period INTO t
                        FROM semesters sm INNER JOIN schedule s ON sm.id = s.semester_id
                        WHERE s.id = s_id LIMIT 1;
                  INSERT INTO schedule_rendered (exec_date, schedule_id) 
                     SELECT a::date as exec_date, s_id as schedule_id
                        FROM generate_series(t.time_start::date, t.time_end::date, (t.period || ' day')::interval) s(a);
                  UPDATE schedule SET status = 1 WHERE id = s_id;
               END;
               \$BODY\$ LANGUAGE plpgsql";
      $result = pg_query($conn, $foo);
      if (!$result) {
         die("function creation failed\n");
      }
   }

   function setRenderAllScheduleFunction(&$conn)
   {
      $foo =   "CREATE OR REPLACE FUNCTION renderAllSchedule(cnt INTEGER) RETURNS void AS
               \$BODY\$
               DECLARE
                  vals TEXT;
                  r    RECORD;
                  ids  INTEGER[] := '{}';
               BEGIN
                  FOR r IN SELECT s.id, sm.time_start, sm.time_end, s.period 
                     FROM semesters sm INNER JOIN schedule s ON sm.id = s.semester_id
                        WHERE s.status = 0 LIMIT cnt
                  LOOP
                     INSERT INTO schedule_rendered (exec_date, schedule_id) 
                        SELECT a::date as exec_date, r.id as schedule_id
                           FROM generate_series(r.time_start::date, r.time_end::date, (r.period || ' day')::interval) s(a);  
                     ids = array_append(ids, r.id);   
                  END LOOP;
                  UPDATE schedule SET status = 1 WHERE (id = ANY (ids));
               END;
               \$BODY\$ LANGUAGE plpgsql";
      $result = pg_query($conn, $foo);
      if (!$result) {
         die("function creation failed\n");
      }
   }

   function initTriggers(&$conn)
   {
      $updating_table = [
         // 'departments',
         // 'schools',
         // 'study_types',
         // 'schedule',
         // 'schedule_parts',
         'schedule_rendered',
         'disciplines',
         // 'discipline_sections',
         // 'roles',
         'users',
         'groups',
         // 'study_sets',
         // 'courses',
         // 'specializations',
         'geoobjects',
         'times'
         // 'lesson_types',
         // 'auditory_types',
         // 'levels',
         // 'buildings',
         // 'building_types',
      ];
      foreach($updating_table as &$table) {
         setUpdating($conn, $table);
      }
   }

   function complexDelete(&$conn)
   {
      //sys part
      //WARNING: ORDER IS IMPORTANT!
      $arrays = [
         // 'schedule_rendered',
         'department_sets',
         'departments',
         'schools',
         'study_types',
         'schedule',
         'semesters',
         'schedule_parts',
         'disciplines',
         'discipline_sections',
         'users_roles',
         'roles',
         'users',
         'groups',
         'study_sets',
         'courses',
         'specializations',
         'geoobjects',
         'times',
         'lesson_types',
         'geoobject_types',
         'levels',
         // 'buildings_types',
         'buildings',
         'building_types',
         'last_modified'
      ];
      echo "let's delete all fcking entities\n";
      for ($i = 0; $i < count($arrays); $i++) {
         $result = pg_query($conn, "DELETE FROM " . $arrays[$i]);
         if (!$result) {
            die("delete failed on table " . $arrays[$i] . "\n");
         } else {
            echo "delete succeed on table " . $arrays[$i] . "\n";
         }
      }
      echo "delete succeed\n";
      echo "let's restart sequences\n";
      $result = pg_query($conn, "select c.relname from pg_class c WHERE c.relkind = 'S'");
      if (!$result) {
         die("sequences selection failed!\n");
      }
      while ($row = pg_fetch_row($result)) {
         $result2 = pg_query($conn, "ALTER SEQUENCE {$row[0]} RESTART WITH 1");
         if (!$result2) {
            die("sequence restarting failed on sequence " . $row[0] . "\n");
         }
      }
      echo "sequences restarted\n";
   }

   function simpleInsert(&$array, &$conn, $name) {
      echo "simple insertion into " . $name . "...\n";
      foreach($array as $key => &$value) {
         $result = pg_query($conn, "INSERT INTO {$name} (alias) VALUES ('{$key}') RETURNING id");
         if (!$result) {
            die("simple insert failed on table " . $name . "\n");
         }
         $insert_row = pg_fetch_row($result);
         $array[$key] = $insert_row[0];
      }
      echo "simple insert complited\n";
   };

   function advInsert(&$array, &$conn, $name, $fields) {
      echo "advansed insertion into " . $name . "...\n";
      for($i = 0; $i < count($array); $i++) {
         $id_num = -1;
         $field_string = "(";
         $value_string = "(";
         for ($j = 0; $j < count($fields); $j++) {
            $field_type = $fields[$j][1];
            $field_name = $fields[$j][0];
            switch ($field_type) {
               case "string":
                  $field_string .= $field_name;
                  $value_string .= "'" . $array[$i][$j] . "'";
                  break;
               case "time": case 'date':
                  $field_string .= $field_name;
                  $value_string .= "'" . $array[$i][$j] . "'";
                  break;
               case "idfield":
                  $id_num = $j;
                  break;
               case 'integer':
                  $field_string .= $field_name;
                  $value_string .= $array[$i][$j];
                  break;
            }
            if ($j < count($fields) - 1) {
               if ($id_num == $j) {
                  continue;
               }
               $field_string .= ",";
               $value_string .= ",";
            } else {
               $field_string .= ")";
               $value_string .= ")";
            }
         }
         $query = "INSERT INTO {$name} " . $field_string . " VALUES " . $value_string;
         if ($id_num != -1) {
            $query .= " RETURNING id;";
         }
         $result = pg_query($conn, $query);
         if (!$result) {
            die("advansed insert failed on table " . $name . "\n");
         }
         if ($id_num != -1) {
            $insert_row = pg_fetch_row($result);
            $array[$i][$id_num] = $insert_row[0];
         }
      }
      echo "advansed insert complited on table " . $name . "\n";
   }

   require_once("GetConnect.php");
   echo "Welcome to rubish generator!\n";

   $res = getConnection();
   $dbcon = $res[0];
   $params = $res[1];

   pg_query($dbcon, "SET datestyle='ISO, MDY'");
   complexDelete($dbcon); //tables && sequences && triggers
   initTriggers($dbcon);
   setSyncFunction($dbcon);
   setRenderScheduleFunction($dbcon);
   setRenderAllScheduleFunction($dbcon);
?>