<?php
namespace Farpost\StoreBundle\Services;

class SQLiteManager {

   public function CreateDb()
   {
      $dt = new \Datetime();
      $db_sqlite_sname = "catalog" . date('dmY_Gis', $dt->getTimestamp()) . ".db";
      $db_sqlite_name = dirname(__FILE__) . "/../../../../web/static/" . $db_sqlite_sname;
      $db_sqlite = new \SQLite3($db_sqlite_name);
      if (!$db_sqlite) {
         throw new \Exception("No database created!");
      }
      $db_sqlite->exec("pragma synchronous = off;");
      return [$db_sqlite, $dt, $db_sqlite_sname];
   }

   private function ToField($name, $type, $PK, $nullable, $RK)
   {
      $template = [
         'name' => $name,
         'type' => $type,
         'PK'   => $PK,
         'nullable' => $nullable,
         'RK' => $RK
      ];
      return $template;
   }

   //returns sql for table creation
   private function CreateTable($table_name, $fields)
   {
      // echo "CreateTable $table_name";
      $promtSql = "CREATE TABLE IF NOT EXISTS $table_name (";
      $fieldSql = "";
      $refSql = "";
      $first_f = true; //for fields
      foreach ($fields as $field) {
         if ($first_f) {
            $first_f = false;
         } else {
            $fieldSql .= ", ";
         }
         $fieldSql .= $field['name'] . " " .
                      $field['type'] .
                      ($field['PK'] ? " PRIMARY KEY" : "" ) .
                      ($field['nullable'] ? "" : " NOT NULL");
         if ($field['RK']) {
            $field_name = $field['name'];
            $field_rk = $field['RK'];
            $refSql .= ", FOREIGN KEY($field_name) REFERENCES $field_rk(id)";
         }
      }
      $resultSql = $promtSql . $fieldSql . $refSql . ");";
      // echo "<p>" . $resultSql . "</p>";
      return $resultSql;
   }

   public function CreateTables($tables, $db)
   {
      foreach ($tables as &$table) {
         try {
            $db->query($this->CreateTable($table['table'], $table['fields']));
         }
         catch (\Exception $e) {
            throw new \Exception('SQLite \Exception! SQLiteCreateTables:' . $e->message);
         }
      }
   }

   public function Tables()
   {
      return [
         [
            'table'  => 'buildings',
            'fields' => [
               $this->ToField('id', 'INTEGER', true, false, ''),
               $this->ToField('number', 'VARCHAR', false, true, ''),
               $this->ToField('alias', 'VARCHAR', false, true, ''),
               $this->ToField('lon', 'DOUBLE', false, true, ''),
               $this->ToField('lat', 'DOUBLE', false, true, '')
            ]
         ],
         [
            'table' => 'object_types',
            'fields' => [
               $this->ToField('id', 'INTEGER', true, false, ''),
               $this->ToField('alias', 'VARCHAR', false, true, ''),
               $this->ToField('display', 'INTEGER', false, true, '')
            ]
         ],
         [
            'table' => 'node_types',
            'fields' => [
               $this->ToField('id', 'INTEGER', true, false, ''),
               $this->ToField('alias', 'VARCHAR', false, true, '')
            ]
         ],
         [
            'table' => 'objects',
            'fields' => [
               $this->ToField('id', 'INTEGER', true, false, ''),
               $this->ToField('type_id', 'INTEGER', false, true, 'object_types'),
               $this->ToField('building_id', 'INTEGER', false, false, 'buildings'),
               $this->ToField('level', 'INTEGER', false, false, ''),
               $this->ToField('alias', 'VARCHAR', false, true, ''),
               $this->ToField('node_id', 'INTEGER', false, true, 'node_types'),
               $this->ToField('lat', 'DOUBLE', false, true, ''),
               $this->ToField('lon', 'DOUBLE', false, true, ''),
               $this->ToField('unialias', 'VARCHAR', false, true, '')
            ]
         ],
         [
            'table' => 'path_segments',
            'fields' => [
               $this->ToField('id', 'INTEGER', true, false, ''),
               $this->ToField('level', 'INTEGER', false, true, ''),
               $this->ToField('id_vertex_from', 'INTEGER', false, true, 'objects'),
               $this->ToField('id_vertex_to', 'INTEGER', false, true, 'objects')
            ]
         ],
         [
            'table' => 'path_segment_points',
            'fields' => [
               $this->ToField('id', 'INTEGER', true, false, ''),
               $this->ToField('path_id', 'INTEGER', false, false, 'path_segments'),
               $this->ToField('lon', 'DOUBLE', false, true, ''),
               $this->ToField('lat', 'DOUBLE', false, true, ''),
               $this->ToField('idx', 'INTEGER', false, true, '')
            ]
         ]
      ];
   }

   public function ClearTables($tables, $db)
   {
      foreach ($tables as &$table) {
         try {
            $db->query("DELETE FROM " . $table['table']);
         }
         catch (\Exception $e) {
            throw new \Exception('SQLite \Exception! SQLiteClearTables:' . $e->message);
         }
      }
   }


   public function GroupInsert($table, $items, $db)
   {
      // echo "<p>Table name = $table[table]</p>";
      // echo "<p>Items count = " . count($items) . "</p>";
      foreach($items as &$item) {
         // echo "<p>Items before insert in table $table[table]</p>";
         // $results = $db->query("select * from $table[table]");
         // while ($row = $results->fetchArray()) {
            // echo "<p>" . json_encode($row) . "</p>";
         // }
         // echo "<p>Inserted_Item_id = $item[id]</p>";
         $this->Insert($table, $item, $db);
      }
   }

   private function Insert($table, $record, $db)
   {
      $promtSql = "INSERT INTO $table[table]";
      $first = true;
      $valSql = "";
      $fieldSql = "";
      if ($table['table'] == 'objects') {
         $record['type_id'] = $record['type_id'] ? $record['type_id'] : 0;
         $record['node_id'] = $record['node_id'] ? $record['node_id'] : 2;
         $record['unialias'] = $record['alias'];
      }
      foreach($table['fields'] as &$field) {
         // echo "<p>" . json_encode($record) . "</p>";
         $name = $field['name'];
         $val = array_key_exists($name, $record) ? $record[$name] : '';
         $nullable = $field['nullable'];
         $is_varchar = $field['type'] == 'VARCHAR';
         if ('' === $val && !$nullable) {
            throw new \Exception(
               "In table $table[table] column $name could not be NULL\nFields:" . json_encode($record));
         }
         if ($val !== '' && $val !== null) {
            if ($first) {
               $first = false;
            } else {
               $valSql .= ", ";
               $fieldSql .= ", ";
            }
            $valSql .= $is_varchar ? "'$val'" : $val;
            $fieldSql .= $name;
         }
      }
      $resultSql = "$promtSql ($fieldSql) VALUES ($valSql);";
      try {
         $db->query($resultSql);
      }
      catch (\Exception $e) {
         throw new \Exception("{$e->getMessage()}\n$resultSql");
      }
   }

}