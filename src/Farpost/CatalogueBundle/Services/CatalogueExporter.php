<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 16/07/15
 * Time: 16:28
 */

namespace Farpost\CatalogueBundle\Services;

use Farpost\StoreBundle\Services\SQLiteManager;
use Farpost\StoreBundle\Entity\Version;

class CatalogueExporter {

    private $doctrine;
    private $sqliteManager;

    public function __construct($doctrine, SQLiteManager $sqliteManager)
    {
        $this->doctrine = $doctrine;
        $this->sqliteManager = $sqliteManager;
    }

    public function export()
    {
        $em = $this->doctrine->getManager();

        $version = $this->createVersion($em);

        list($sqliteDb, $dt, $sqliteDbName) = $this->sqliteManager->createDb();

        $tableMap = [
            [
                'bundle' => 'MapsBundle',
                'mapping' => [
                    'buildings' => 'Building',
                    'levels' => 'Level',
                    'node_types' => 'NodeType',
                    'nodes' => 'Node',
                    'path_segments' => 'Edge',
                    'path_segment_points' => 'EdgePoint'
                ]
            ],
            [
                'bundle' => 'CatalogueBundle',
                'mapping' => [
                    'categories' => 'CatalogueCategory',
                    'categories_tree' => 'CatalogueCategoryEdge',
                    'categories_objects' => 'CatalogueCategoryObjectEdge',
                    'objects' => 'CatalogueObject',
                    'objects_schedule' => 'CatalogueObjectSchedule'
                ]
            ]
        ];

        $batchSize = 20;
        foreach($tableMap as $bundleTableMap) {
            $classNamespace = "Farpost\\$bundleTableMap[bundle]\\Entity\\";
            $symfonyPrefix = "Farpost$bundleTableMap[bundle]";
            $mapping = $bundleTableMap['mapping'];
            foreach ($mapping as $table => $entityName) {
                $className = $classNamespace . $entityName;
                $fullEntityName = "$symfonyPrefix:$entityName";

                $annotations = $className::$sqliteAnnotations;
                $this->sqliteManager->createTable($annotations, $sqliteDb);

                $q = $em->createQuery("select a from $fullEntityName a");
                $it = $q->iterate();
                $i = 0;
                $records = [];
                foreach ($it as $row) {
                    $object = $row[0];
                    $record = [];
                    foreach($annotations['fields'] as $fieldInfo) {
                        $value = $object->$fieldInfo['getter']();
                        if ($fieldInfo['RK']) {
                            $value = $value ? $value->getId() : null;
                        }
                        $record[$fieldInfo['name']] = $value;
                    }
                    $records[] = $record;
                    if (++$i % $batchSize === 0) {
                        $this->sqliteManager->groupInsert($annotations, $records, $sqliteDb);
                        $em->clear();
                        $records = [];
                    }
                }
                $this->sqliteManager->groupInsert($annotations, $records, $sqliteDb);
                $em->clear();
            }
        }

        $version->setIsProcessing(false)
            ->setBase($sqliteDbName);
        $em->merge($version);
        $em->flush();
    }

    /**
     * @param $em
     * @return Version
     */
    private function createVersion(&$em)
    {
        $version = new Version();
        $dt = new \DateTime();
        $version->setType(Version::CATALOG_V2)
            ->setVDatetime($dt->getTimestamp())
            ->setBase('')
            ->setIsProcessing(true);
        $em->persist($version);
        $em->flush();
        return $version;
    }
}