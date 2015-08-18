<?php
namespace Farpost\StoreBundle\Services;

class SQLiteManager {
    const AUDITORY_TYPE_ID = 6;

    public function createDb()
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

    private function toField($name, $type, $PK, $nullable, $RK)
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
    private function getTableCreationSQL($table_name, $fields)
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
                $refSql .= ", FOREIGN KEY($field_name) REFERENCES $field_rk(_id)";
            }
        }
        $resultSql = $promtSql . $fieldSql . $refSql . ");";
        // echo "<p>" . $resultSql . "</p>";
        return $resultSql;
    }

    private function getVirtualTableCreationSQL($name, $fields)
    {
        $fieldsSQL = join(',', $fields);
        return "CREATE VIRTUAL TABLE $name using fts4($fieldsSQL)";
    }

    public function createTable($table, $db) {
        $db->query($this->getTableCreationSQL($table['table'], $table['fields']));
        if (array_key_exists('virtual_table', $table)) {
            $virtualName = $table['virtual_table'];
            $virtualFields = [];
            foreach($table['fields'] as $field) {
                if (array_key_exists('virtual', $field) && $field['virtual'] && !$field['PK']) {
                    $virtualFields[] = $field['name'];
                }
            }
            $db->query($this->getVirtualTableCreationSQL($virtualName, $virtualFields));
        }
    }

    public function createTables($tables, $db)
    {
        foreach ($tables as &$table) {
            $this->createTable($table, $db);
        }
    }

    public function getCatalogv2Tables()
    {
        return [
            [
                'table'  => 'buildings',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('number', 'VARCHAR', false, true, ''),
                    $this->toField('alias', 'VARCHAR', false, true, ''),
                    $this->toField('lon', 'DOUBLE', false, true, ''),
                    $this->toField('lat', 'DOUBLE', false, true, '')
                ]
            ],
            [
                'table' => 'node_types',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('alias', 'VARCHAR', false, true, '')
                ]
            ],
            [
                'table' => 'levels',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('level', 'INTEGER', false, false, ''),
                    $this->toField('alias', 'VARCHAR', false, false, '')
                ]
            ],
            [
                'table' => 'nodes',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('building_id', 'INTEGER', false, false, 'buildings'),
                    $this->toField('level_id', 'INTEGER', false, false, 'levels'),
                    $this->toField('alias', 'VARCHAR', false, true, ''),
                    $this->toField('lat', 'DOUBLE', false, true, ''),
                    $this->toField('lon', 'DOUBLE', false, true, ''),
                    $this->toField('type_id', 'INTEGER', false, true, 'node_types')
                ]
            ],
            [
                'table' => 'path_segments',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('level_id', 'INTEGER', false, true, 'levels'),
                    $this->toField('node_from_id', 'INTEGER', false, false, 'nodes'),
                    $this->toField('node_to_id', 'INTEGER', false, false, 'nodes'),
                    $this->toField('weight', 'DOUBLE', false, false, ''),
                    $this->toField('distance', 'DOUBLE', false, false, '')
                ]
            ],
            [
                'table' => 'path_segment_points',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('path_id', 'INTEGER', false, false, 'path_segments'),
                    $this->toField('lon', 'DOUBLE', false, false, ''),
                    $this->toField('lat', 'DOUBLE', false, false, ''),
                    $this->toField('seq', 'INTEGER', false, false, '')
                ]
            ],
            [
                'table' => 'categories',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('name', 'VARCHAR', false, false, ''),
                    $this->toField('is_organization', 'BOOLEAN', false, false, ''),
                    $this->toField('description', 'VARCHAR', false, true, ''),
                    $this->toField('logo_standard', 'VARCHAR', false, true, ''),
                    $this->toField('logo_thumbnail', 'VARCHAR', false, true, ''),
                    $this->toField('phone', 'VARCHAR', false, true, ''),
                    $this->toField('site', 'VARCHAR', false, true, ''),
                ]
            ],
            [
                'table' => 'categories_tree',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('child_id', 'INTEGER', false, false, 'categories'),
                    $this->toField('parent_id', 'INTEGER', false, false, 'categories')
                ]
            ],
            [
                'table' => 'objects',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('name', 'VARCHAR', false, false, ''),
                    $this->toField('description', 'VARCHAR', false, true, ''),
                    $this->toField('logo_standard', 'VARCHAR', false, true, ''),
                    $this->toField('logo_thumbnail', 'VARCHAR', false, true, ''),
                    $this->toField('phone', 'VARCHAR', false, true, ''),
                    $this->toField('site', 'VARCHAR', false, true, ''),
                    $this->toField('node_id', 'INTEGER', false, true, 'nodes')
                ]
            ],
            [
                'table' => 'categories_objects',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('object_id', 'INTEGER', false, false, 'objects'),
                    $this->toField('category_id', 'INTEGER', false, false, 'categories')
                ]
            ],
            [
                'table' => 'objects_schedule',
                'fields' => [
                    $this->toField('_id', 'INTEGER', true, false, ''),
                    $this->toField('day_number', 'INTEGER', false, false, ''),
                    $this->toField('start_at', 'VARCHAR', false, true, ''),
                    $this->toField('end_at', 'VARCHAR', false, true, ''),
                    $this->toField('object_id', 'INTEGER', false, false, 'objects')
                ]
            ]
        ];
    }

    public function getCatalogv1Tables()
    {
        return [
            [
                'table'  => 'buildings',
                'fields' => [
                    $this->toField('id', 'INTEGER', true, false, ''),
                    $this->toField('number', 'VARCHAR', false, true, ''),
                    $this->toField('alias', 'VARCHAR', false, true, ''),
                    $this->toField('lon', 'DOUBLE', false, true, ''),
                    $this->toField('lat', 'DOUBLE', false, true, '')
                ]
            ],
            [
                'table' => 'object_types',
                'fields' => [
                    $this->toField('id', 'INTEGER', true, false, ''),
                    $this->toField('alias', 'VARCHAR', false, true, ''),
                    $this->toField('display', 'INTEGER', false, true, '')
                ]
            ],
            [
                'table' => 'node_types',
                'fields' => [
                    $this->toField('id', 'INTEGER', true, false, ''),
                    $this->toField('alias', 'VARCHAR', false, true, '')
                ]
            ],
            [
                'table' => 'objects',
                'fields' => [
                    $this->toField('id', 'INTEGER', true, false, ''),
                    $this->toField('type_id', 'INTEGER', false, true, 'object_types'),
                    $this->toField('building_id', 'INTEGER', false, false, 'buildings'),
                    $this->toField('level', 'INTEGER', false, false, ''),
                    $this->toField('alias', 'VARCHAR', false, true, ''),
                    $this->toField('node_id', 'INTEGER', false, true, 'node_types'),
                    $this->toField('lat', 'DOUBLE', false, true, ''),
                    $this->toField('lon', 'DOUBLE', false, true, ''),
                    $this->toField('unialias', 'VARCHAR', false, true, '')
                ]
            ],
            [
                'table' => 'path_segments',
                'fields' => [
                    $this->toField('id', 'INTEGER', true, false, ''),
                    $this->toField('level', 'INTEGER', false, true, ''),
                    $this->toField('id_vertex_from', 'INTEGER', false, true, 'objects'),
                    $this->toField('id_vertex_to', 'INTEGER', false, true, 'objects')
                ]
            ],
            [
                'table' => 'path_segment_points',
                'fields' => [
                    $this->toField('id', 'INTEGER', true, false, ''),
                    $this->toField('path_id', 'INTEGER', false, false, 'path_segments'),
                    $this->toField('lon', 'DOUBLE', false, true, ''),
                    $this->toField('lat', 'DOUBLE', false, true, ''),
                    $this->toField('idx', 'INTEGER', false, true, '')
                ]
            ]
        ];
    }

    public function clearTables($tables, $db)
    {
        foreach ($tables as &$table) {
            try {
                $db->query("DELETE FROM " . $table['table']);
            }
            catch (\Exception $e) {
                $message = $e->getMessage();
                throw new \Exception('SQLite \Exception! SQLiteClearTables:' . $message);
            }
        }
    }

    private function getInsertSQL($table)
    {
        $promtSQL = "INSERT INTO $table[table]";
        $fieldsSQL = "";
        $valsSQL = "";
        $first = true;
        foreach ($table['fields'] as &$field) {
            $fieldsSQL .= $first ? "" : ", ";
            $fieldsSQL .= $field['name'];
            $valsSQL .= $first ? "" : ", ";
            $valsSQL .= ":" . $field['name'];
            $first = false;
        }
        return "$promtSQL ($fieldsSQL) VALUES ($valsSQL);";
    }

    private function beginTransaction($db)
    {
        return $db->exec('BEGIN TRANSACTION');
    }

    private function commit($db)
    {
        return $db->exec('COMMIT');
    }

    public function groupInsert($table, $items, $db)
    {
        if (!$this->beginTransaction($db)) {
            return false;
        }
        $stmt = $db->prepare($this->getInsertSQL($table));

        $virtualTable = null;
        if (array_key_exists('virtual_table', $table)) {
            $virtualTable = [
                'table' => $table['virtual_table'],
                'fields' => [],
            ];
            foreach($table['fields'] as $field) {
                if (array_key_exists('virtual', $field) && $field['virtual']) {
                    if ($field['PK']) {
                        $field['name'] = 'docid';
                    }
                    $virtualTable['fields'][] = $field;
                }
            }
            $virtualStmt = $db->prepare($this->getInsertSQL($virtualTable));
        }

        foreach($items as &$item) {
            $this->insert($table, $item, $stmt, $db);
            $stmt->execute();
            if ($virtualTable) {
                $item['virtual']['docid'] = $item['virtual']['_id'];
                unset($item['virtual']['_id']);
                $this->insert($virtualTable, $item['virtual'], $virtualStmt, $db, true);;
                $virtualStmt->execute();
            }
        }
        $this->commit($db);
    }

    private function insert($table, $record, &$stmt, $db, $toLower = false)
    {
        foreach($record as $field => $value) {
            if ($toLower) {
                $value = mb_strtolower($value, 'UTF-8');
            }
            $stmt->bindValue(":" . $field, $value);
        }
    }

}