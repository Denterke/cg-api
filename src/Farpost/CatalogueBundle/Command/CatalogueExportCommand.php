<?php

namespace Farpost\CatalogueBundle\Command;

use Farpost\StoreBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogueExportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('catalogue:export')
            ->setDescription('Creates new .sqlite catalog version')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $sqliteManager = $this->getContainer()->get('sqlite_manager');
        $versionRepository = $em->getRepository('FarpostStoreBundle:Version');

        $version = new Version();
        $dt = new \DateTime();
        $version->setType(Version::CATALOG_V2)
            ->setVDatetime($dt->getTimestamp())
            ->setBase('')
            ->setIsProcessing(true)
        ;
        $em->persist($version);
        $em->flush();

        try {
            list($sqliteDb, $dt, $sqliteDbName) = $sqliteManager->createDb();
            $sqliteTablesInfo = $sqliteManager->getCatalogv2Tables();
            $sqliteManager->createTables($sqliteTablesInfo, $sqliteDb);
            $sqliteManager->clearTables($sqliteTablesInfo, $sqliteDb);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            $output->writeln("<error>Some error occured on sqlite db creation step. Message: $message</error>");
            return;
        }

        //Тут записывается граф карты

        $catalogueTableMap = [
            'categories' => 'CatalogueCategory',
            'categories_tree' => 'CatalogueCategoryEdge',
            'categories_objects' => 'CatalogueCategoryObjectEdge',
            'objects' => 'CatalogueObject',
            'objects_schedule' => 'CatalogueObjectSchedule'
        ];

        foreach($catalogueTableMap as $table => $entityName) {
            $fullEntityName = "FarpostCatalogueBundle:$entityName";
            $items = $em->getRepository($fullEntityName)->getCatalogItems();
            try {
                $sqliteManager->groupInsert($sqliteTablesInfo[$table], $items);
            }
            catch (\Exception $e) {
                $message = $e->getMessage();
                $output->writeln("<error>Some error occured on sqlite insert $table step. Message: $message</error>");
            }
        }
    }
}
