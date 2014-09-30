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

//    // echo "WARNING! Be sure, that you launch this script from root Symfony directory!\n";
//    // $path = system('pwd');
//    // $symfConfig = fopen($path . '/app/config/config.yml', 'r');
//    // $str = fgets($symfConfig);
//    // while (fgets($symfConfig) != "doctrine:\n") { }
//    // $paramCount = 0;
//    // $params = [
//       // "dbname"   => "",
//       // "user"     => "",
//       // "password" => ""
//    // ];
//    // while ($paramCount < 3) {
//       // $str = fgets($symfConfig);
//       // foreach ($params as $paramName => $paramValue) {
//          // if (strpos($str, $paramName)) {
//             // $params[$paramName] = getValue($str);
//             // $paramCount++;
//          // }
//       // }
//    // }
//    // $connection_string = "dbname = " . $params["dbname"] .
//                         // " host = " . $params[]
//                         // " port = 5432 " .
//                         // " user = " . $params["user"] .
//                         // " password = " . $params["password"];
//    // echo "connection string: \"" . $connection_string . "\"\n";
//    // $dbcon = pg_connect($connection_string);
//    // if (!$dbcon) {
//       // die('connection failed!');
//    // } else {
//       // echo "connection succeed!\n";
//    // }
   pg_query($dbcon, "SET datestyle='ISO, MDY'");
   complexDelete($dbcon); //tables && sequences && triggers
   initTriggers($dbcon);


//    //hard code part
//    $study_types = [
//       'Бакалавриат'  => 0,
//       'Магистратура' => 0,
//       'Специалитет'  => 0
//    ];

//    $schools = [
//       'Школа естественных наук' => 0,
//       'Школа гуманитарных наук' => 0,
//       'Школа региональных и международных исследований' => 0,
//       'Школа биомедицины'=> 0,
//       'Инженерная школа' => 0,
//       'Школа искусство, культуры и спорта' => 0,
//       'Школа педагогики' => 0,
//       'Школа экономики и менеджмента' => 0,
//       'Юридическая школа' => 0
//    ];

//    $courses = [
//       '1 курс' => 0,
//       '2 курс' => 0,
//       '3 курс' => 0,
//       '4 курс' => 0,
//       '5 курс' => 0
//    ];

//    $specializations = [
//       'Без профиля' => 0
//    ];

//    $roles = [
//       'Администратор' => 0,
//       'Пользователь'  => 0,
//       'Преподаватель' => 0
//    ];

//    $discipline_sections = [
//       'Без секции' => 0,
//       'Гуманитарная дисциплина' => 0,
//       'Техническая дисциплина'  => 0,
//       'Естественная дисциплина' => 0,
//       'Юридическая дисциплина'  => 0
//    ];

//    $building_types = [
//       'Учебный корпус'          => 0,
//       'Жилой корпус'            => 0,
//       'Административный корпус' => 0
//    ];

//    $lesson_types = [
//       'Лекция'                  => 0,
//       'Практика'                => 0,
//       'Потоковая лекция'        => 0,
//       'Лабораторная работа'     => 0
//    ];

//    $geoobject_types = [
//       'Учебная аудитория'          => 0,
//       'Административное помещение' => 0
//    ];

//    $times = [
//       ['1 пара', 0, '08:30', '10:00'],
//       ['2 пара', 0, '10:10', '11:40'],
//       ['3 пара', 0, '11:50', '13:20'],
//       ['4 пара', 0, '13:30', '15:00'],
//       ['5 пара', 0, '15:10', '16:40'],
//       ['6 пара', 0, '16:50', '18:20'],
//       ['7 пара', 0, '18:30', '19:00'],
//       ['8 пара', 0, '19:10', '20:40']
//    ];
//    $times_meta = [
//       ['alias', 'string'],
//       ['id','idfield'],
//       ['start_time', 'time'],
//       ['end_time', 'time']
//    ];

//    $semesters = [
//       [0, 'Тестовый семестр', '07.01.2014', '01.01.2015']
//    ];
//    $semesters_meta = [
//       ['id',         'idfield'],
//       ['alias',      'string'],
//       ['time_start', 'date'],
//       ['time_end',   'date']
//    ];

//    simpleInsert($study_types, $dbcon, 'study_types');
//    simpleInsert($schools, $dbcon, 'schools');
//    simpleInsert($courses, $dbcon, 'courses');
//    simpleInsert($specializations, $dbcon, 'specializations');
//    simpleInsert($roles, $dbcon, 'roles');
//    simpleInsert($discipline_sections, $dbcon, 'discipline_sections');
//    simpleInsert($building_types, $dbcon, 'building_types');
//    simpleInsert($lesson_types, $dbcon, 'lesson_types');
//    simpleInsert($geoobject_types, $dbcon, 'geoobjects_types');
//    advInsert($times, $dbcon, 'times', $times_meta);
//    advInsert($semesters, $dbcon, 'semesters', $semesters_meta);
//    //rubbish part
//    $users = [
//       [0, 'Павел', 'Петрович', 'Петров', 'no_salt', 'no_pass', 'login0'],
//       [0, 'Иван', 'Иванович', 'Иванов', 'no_salt', 'no_pass', 'login1'],
//       [0, 'Сергей', 'Сергеевич', 'Сергеев', 'no_salt', 'no_pass', 'login2'],
//       [0, 'Александр', 'Сергеевич', 'Кленин', 'no_salt', 'no_pass', 'login3']
//    ];
//    $users_meta = [
//       ['id',          'idfield'],
//       ['first_name',  'string'],
//       ['middle_name', 'string'],
//       ['last_name',   'string'],
//       ['salt',        'string'],
//       ['pass_md5',    'string'],
//       ['login',       'string']
//    ];
//    advInsert($users, $dbcon, 'users', $users_meta);

//    $departments = [
//       [0, 'Прикладная математика и информатика', $schools['Школа естественных наук'], $study_types['Бакалавриат']],
//       [0, 'Строительство', $schools['Инженерная школа'], $study_types['Бакалавриат']],
//       [0, 'Химия и медицина', $schools['Школа биомедицины'], $study_types['Бакалавриат']]
//    ];
//    $departments_meta = [
//       ['id',            'idfield'],
//       ['alias',         'string' ],
//       ['school_id',     'integer'],
//       ['study_type_id', 'integer']
//    ];
//    advInsert($departments, $dbcon, 'departments', $departments_meta);

//    $buildings = [
//       [0, '24 корпус', 'Корпус A'],
//       [0, '20 корпус', 'Корпус D']
//    ];
//    $buildings_meta = [
//       ['id',     'idfield'],
//       ['number', 'string' ],
//       ['alias',  'string' ]
//    ];
//    advInsert($buildings, $dbcon, 'buildings', $buildings_meta);

//    $levels = [
//       [0, '1 уровень', $buildings[1][0]],
//       [0, '2 уровень', $buildings[1][0]],
//       [0, '3 уровень', $buildings[1][0]],
//       [0, '4 уровень', $buildings[1][0]]
//    ];
//    $levels_meta = [
//       ['id',          'idfield'],
//       ['alias',       'string' ],
//       ['building_id', 'integer']
//    ];
//    advInsert($levels, $dbcon, 'levels', $levels_meta);


//    $buildings_types = [
//       [$buildings[1][0], $building_types['Учебный корпус']]
//    ];
//    $buildings_types_meta = [
//       ['building_id',      'integer'],
//       ['building_type_id', 'integer']
//    ];
//    advInsert($buildings_types, $dbcon, 'buildings_types', $buildings_types_meta);

//    $auditories = [];
//    for ($i = 938; $i < 946; $i++) {
//       $auditories[$i - 938] = [0, $i, $buildings[1][0], $levels[3][0], $auditory_types['Учебная аудитория']];
//    }
//    $auditories_meta = [
//       ['id',               'idfield'],
//       ['alias',            'string' ],
//       ['building_id',      'integer'],
//       ['level_id',         'integer'],
//       ['auditory_type_id', 'integer']
//    ];
//    advInsert($auditories, $dbcon, 'auditories', $auditories_meta);

//    $disciplines = [
//       [0, 'Мат. анализ',                             $discipline_sections['Естественная дисциплина']],
//       [0, 'Технология программирования',             $discipline_sections['Техническая дисциплина']],
//       [0, 'Менеджмент',                              $discipline_sections['Гуманитарная дисциплина']],
//       [0, 'Разработка WEB-приложения',               $discipline_sections['Техническая дисциплина']],
//       [0, 'Культуроведение',                         $discipline_sections['Гуманитарная дисциплина']],
//       [0, 'Биология эукариот',                       $discipline_sections['Естественная дисциплина']],
//       [0, 'Проектирование небоскребов',              $discipline_sections['Техническая дисциплина']],
//       [0, 'Комплексный анализ',                      $discipline_sections['Естественная дисциплина']],
//       [0, 'Русский язык',                            $discipline_sections['Гуманитарная дисциплина']],
//       [0, 'Основы алгоритмов',                       $discipline_sections['Техническая дисциплина']],
//       [0, 'Навыки культурного общения с заказчиком', $discipline_sections['Техническая дисциплина']],
//       [0, 'Введение в программирование баз данных',  $discipline_sections['Техническая дисциплина']]
//    ];
//    //e6AJL 9| 3TOT |73H3|73 U DOKTPUHY BMECTE C HUM..
//    $dicsiplines_meta = [
//       ['id',                    'idfield'],
//       ['alias',                 'string' ],
//       ['discipline_section_id', 'integer']
//    ];
//    advInsert($disciplines, $dbcon, 'disciplines', $dicsiplines_meta);

//    $study_sets = [
//       [0, $specializations['Без профиля'], $courses['1 курс']],
//       [0, $specializations['Без профиля'], $courses['2 курс']],
//       [0, $specializations['Без профиля'], $courses['1 курс']],
//       [0, $specializations['Без профиля'], $courses['1 курс']]
//    ];
//    $study_sets_meta = [
//       ['id',                'idfield'],
//       ['specialization_id', 'integer'],
//       ['course_id',         'integer']
//    ];
//    advInsert($study_sets, $dbcon, 'study_sets', $study_sets_meta);

//    $department_sets = [
//       [$study_sets[0][0], $departments[0][0]],
//       [$study_sets[1][0], $departments[0][0]],
//       [$study_sets[2][0], $departments[1][0]],
//       [$study_sets[3][0], $departments[2][0]]
//    ];
//    $department_sets_meta = [
//       ['study_set_id',  'integer'],
//       ['department_id', 'integer']
//    ];
//    advInsert($department_sets, $dbcon, 'department_sets', $department_sets_meta);

//    $groups = [
//       [0, 'Б8103а', $study_sets[0][0]],
//       [0, 'Б7103а', $study_sets[2][0]],
//       [0, 'Б6103а', $study_sets[3][0]]
//    ];
//    $groups_meta = [
//       ['id',           'idfield'],
//       ['alias',        'string' ],
//       ['study_set_id', 'integer']
//    ];
//    advInsert($groups, $dbcon, 'groups', $groups_meta);

//    $schedule_parts = [
//       [0, $groups[0][0], $disciplines[0][0], $users[0][0]],
//       [0, $groups[0][0], $disciplines[1][0], $users[1][0]],
//       [0, $groups[0][0], $disciplines[3][0], $users[1][0]],
//       [0, $groups[0][0], $disciplines[9][0], $users[3][0]],
//       [0, $groups[0][0], $disciplines[11][0], $users[3][0]],
//       [0, $groups[0][0], $disciplines[8][0], $users[2][0]],
//       [0, $groups[0][0], $disciplines[10][0], $users[2][0]],
//       [0, $groups[0][0], $disciplines[7][0], $users[0][0]],
//       [0, $groups[1][0], $disciplines[0][0], $users[0][0]],
//       [0, $groups[2][0], $disciplines[5][0], $users[2][0]]
//    ];
//    $schedule_parts_meta = [
//       ['id',                 'idfield'],
//       ['group_id',           'integer'],
//       ['discipline_id',      'integer'],
//       ['professor_id',       'integer']
//    ];
//    advInsert($schedule_parts, $dbcon, 'schedule_parts', $schedule_parts_meta);

//    $schedule = [
//       [0, $schedule_parts[0][0], $auditories[0][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 1, 14],
//       [0, $schedule_parts[1][0], $auditories[1][0], $times[1][1], $lesson_types['Лабораторная работа'], $semesters[0][0], 1, 14],
//       [0, $schedule_parts[2][0], $auditories[0][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 1, 14],
//       [0, $schedule_parts[3][0], $auditories[2][0], $times[3][1], $lesson_types['Практика'],            $semesters[0][0], 1, 14],
//       [0, $schedule_parts[4][0], $auditories[7][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 2, 14],
//       [0, $schedule_parts[5][0], $auditories[1][0], $times[1][1], $lesson_types['Лекция'],              $semesters[0][0], 2, 14],
//       [0, $schedule_parts[6][0], $auditories[0][0], $times[2][1], $lesson_types['Практика'],            $semesters[0][0], 2, 14],
//       [0, $schedule_parts[7][0], $auditories[0][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 2, 14],
//       [0, $schedule_parts[2][0], $auditories[6][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 3, 14],
//       [0, $schedule_parts[5][0], $auditories[1][0], $times[1][1], $lesson_types['Лабораторная работа'], $semesters[0][0], 3, 14],
//       [0, $schedule_parts[4][0], $auditories[6][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 3, 14],
//       [0, $schedule_parts[7][0], $auditories[7][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 3, 14],
//       [0, $schedule_parts[1][0], $auditories[2][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 4, 14],
//       [0, $schedule_parts[3][0], $auditories[3][0], $times[1][1], $lesson_types['Практика'],            $semesters[0][0], 4, 14],
//       [0, $schedule_parts[4][0], $auditories[5][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 4, 14],
//       [0, $schedule_parts[6][0], $auditories[6][0], $times[3][1], $lesson_types['Лабораторная работа'], $semesters[0][0], 4, 14],
//       [0, $schedule_parts[6][0], $auditories[1][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 5, 14],
//       [0, $schedule_parts[2][0], $auditories[1][0], $times[1][1], $lesson_types['Практика'],            $semesters[0][0], 5, 14],
//       [0, $schedule_parts[4][0], $auditories[2][0], $times[2][1], $lesson_types['Практика'],            $semesters[0][0], 5, 14],
//       [0, $schedule_parts[3][0], $auditories[3][0], $times[3][1], $lesson_types['Практика'],            $semesters[0][0], 5, 14],
//       [0, $schedule_parts[6][0], $auditories[4][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 6, 14],
//       [0, $schedule_parts[1][0], $auditories[7][0], $times[1][1], $lesson_types['Лекция'],              $semesters[0][0], 6, 14],
//       [0, $schedule_parts[7][0], $auditories[5][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 6, 14],
//       [0, $schedule_parts[0][0], $auditories[6][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 6, 14],
//       [0, $schedule_parts[2][0], $auditories[1][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 8, 14],
//       [0, $schedule_parts[3][0], $auditories[2][0], $times[1][1], $lesson_types['Практика'],            $semesters[0][0], 8, 14],
//       [0, $schedule_parts[5][0], $auditories[1][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 8, 14],
//       [0, $schedule_parts[3][0], $auditories[0][0], $times[3][1], $lesson_types['Практика'],            $semesters[0][0], 8, 14],
//       [0, $schedule_parts[4][0], $auditories[6][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 9, 14],
//       [0, $schedule_parts[6][0], $auditories[5][0], $times[1][1], $lesson_types['Практика'],            $semesters[0][0], 9, 14],
//       [0, $schedule_parts[5][0], $auditories[4][0], $times[2][1], $lesson_types['Лабораторная работа'], $semesters[0][0], 9, 14],
//       [0, $schedule_parts[7][0], $auditories[0][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 9, 14],
//       [0, $schedule_parts[2][0], $auditories[2][0], $times[0][1], $lesson_types['Практика'],            $semesters[0][0], 10, 14],
//       [0, $schedule_parts[1][0], $auditories[1][0], $times[1][1], $lesson_types['Лекция'],              $semesters[0][0], 10, 14],
//       [0, $schedule_parts[0][0], $auditories[0][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 10, 14],
//       [0, $schedule_parts[6][0], $auditories[6][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 10, 14],
//       [0, $schedule_parts[6][0], $auditories[5][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 11, 14],
//       [0, $schedule_parts[6][0], $auditories[2][0], $times[1][1], $lesson_types['Практика'],            $semesters[0][0], 11, 14],
//       [0, $schedule_parts[4][0], $auditories[0][0], $times[2][1], $lesson_types['Лекция'],              $semesters[0][0], 11, 14],
//       [0, $schedule_parts[2][0], $auditories[0][0], $times[3][1], $lesson_types['Практика'],            $semesters[0][0], 11, 14],
//       [0, $schedule_parts[3][0], $auditories[5][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 12, 14],
//       [0, $schedule_parts[1][0], $auditories[6][0], $times[1][1], $lesson_types['Лабораторная работа'], $semesters[0][0], 12, 14],
//       [0, $schedule_parts[5][0], $auditories[1][0], $times[2][1], $lesson_types['Практика'],            $semesters[0][0], 12, 14],
//       [0, $schedule_parts[6][0], $auditories[2][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 12, 14],
//       [0, $schedule_parts[2][0], $auditories[3][0], $times[0][1], $lesson_types['Лекция'],              $semesters[0][0], 13, 14],
//       [0, $schedule_parts[3][0], $auditories[7][0], $times[1][1], $lesson_types['Практика'],            $semesters[0][0], 13, 14],
//       [0, $schedule_parts[4][0], $auditories[6][0], $times[2][1], $lesson_types['Лабораторная работа'], $semesters[0][0], 13, 14],
//       [0, $schedule_parts[7][0], $auditories[0][0], $times[3][1], $lesson_types['Лекция'],              $semesters[0][0], 13, 14]
//    ];
//    $schedule_meta = [
//       ['id',               'idfield'],
//       ['schedule_part_id', 'integer'],
//       ['auditory_id',      'integer'],
//       ['time_id',          'integer'],
//       ['lesson_type_id',   'integer'],
//       ['semester_id',      'integer'],
//       ['day',              'integer'],
//       ['period',           'integer']
//    ];
//    advInsert($schedule, $dbcon, 'schedule', $schedule_meta);
// ?>