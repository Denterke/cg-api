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
use Farpost\StoreBundle\Services\VersionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CatalogueExporter {

    private $doctrine;
    private $sqliteManager;
    private $versionManager;
    private $container;

    public function __construct($doctrine, SQLiteManager $sqliteManager, VersionManager $versionManager, ContainerInterface $container)
    {
        $this->doctrine = $doctrine;
        $this->sqliteManager = $sqliteManager;
        $this->versionManager = $versionManager;
        $this->container = $container;
    }

    public function export()
    {
        $em = $this->doctrine->getManager();

        list($sqliteDb, $dt, $sqliteDbName) = $this->sqliteManager->createDb();
        $version = $this->createVersion($em, $sqliteDbName);

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
                    'objects_schedule' => 'CatalogueObjectSchedule',
                    'objects_images' => 'CatalogueObjectMedia',
                    'categories_images' => 'CatalogueCategoryMedia'
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
                    $record = [
                        'virtual' => []
                    ];
                    foreach($annotations['fields'] as $fieldInfo) {
                        if (array_key_exists('injections', $fieldInfo)) {
                            $injections = [];
                            foreach($fieldInfo['injections'] as $serviceName) {
                                echo $serviceName . "\n";
                                $injections[] = $this->container->get($serviceName);
                            }
//                            echo count($injections);
                            $value = $object->$fieldInfo['getter']($injections);
                        } else {
                            $value = $object->$fieldInfo['getter']();
                        }
                        if ($fieldInfo['RK']) {
                            $value = $value ? $value->getId() : null;
                        }
                        if (array_key_exists('virtual', $fieldInfo) && $fieldInfo['virtual']) {
                            if (array_key_exists('virtual_getter', $fieldInfo)) {
                                $record['virtual'][$fieldInfo['name']] = $object->$fieldInfo['virtual_getter']();
                            } else {
                                $record['virtual'][$fieldInfo['name']] = $value;
                            }
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
            ->setBaseWithAttributes($sqliteDbName);
        $em->merge($version);
        $em->flush();

        $this->versionManager->zipLikeMapsVL();
    }

    /**
     * @param $em
     * @param $base
     * @return Version
     */
    private function createVersion(&$em, $base)
    {
        $version = new Version();
        $dt = new \DateTime();
        $version->setType(Version::CATALOG_V2)
            ->setVDatetime($dt->getTimestamp())
            ->setBase($base)
            ->setIsProcessing(true);
        $em->persist($version);
        $em->flush();
        return $version;
    }
}