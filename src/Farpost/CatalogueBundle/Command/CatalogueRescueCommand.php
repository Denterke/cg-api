<?php

namespace Farpost\CatalogueBundle\Command;

use Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge;
use Farpost\StoreBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogueRescueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('catalogue:rescue')
            ->setDescription('rescue catalog');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        gc_enable();
        $em = $this->getContainer()->get('doctrine')->getManager();
        $staticPath = $this->getContainer()->get('kernel')->getRootDir() . '/../web/static/';

        $versionRepository = $em->getRepository('FarpostStoreBundle:Version');
        $categoryObjectEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');
        $objectRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueObject');
        $categoryRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategory');

        $lastClientCatalogue = $versionRepository->getLastVersionOfType(Version::CATALOG_V2);

        $cataloguePath = $staticPath . $lastClientCatalogue->getBase();
        $sqliteBase = new \SQLite3($cataloguePath);

        $relations = $sqliteBase->query('SELECT _id, object_id, category_id FROM categories_objects');
        while ($row = $relations->fetchArray()) {
            $categoryObjectEdge = $categoryObjectEdgeRepository->findOneBy(['id' => $row['_id']]);
            if (!$categoryObjectEdge) {
                $categoryObjectEdge = new CatalogueCategoryObjectEdge();
            }
            $object = $objectRepository->findOneBy(['id' => $row['object_id']]);
            $category =$objectRepository->findOneBy(['id' => $row['category_id']]);
            if ($object && $category) {
                echo "<p>Rescue CategoryObjectEdge: ($row[object_id], $row[category_id])</p>";
                $categoryObjectEdge->setObject($object)
                    ->setCategory($category);
                $em->merge($categoryObjectEdge);
                $em->flush();
                $em->clear();
            } else {
                echo "<p>Entities was not found: object with id = $row[object_id] and category with id = $row[category_id]</p>";
            }
            unset($object);
            unset($category);
            unset($categoryObjectEdge);
            unset($row);
            gc_collect_cycles();
        }
        $sqliteBase->close();
    }
}
