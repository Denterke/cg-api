<?php

namespace Farpost\CatalogueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogueFixCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('catalogue:fix')
            ->setDescription('Drop bad category_object edges and bad category_category edges');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $categoryEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge');
        $categoryObjectEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');

        $badCategoryEdges = $categoryEdgeRepository->createQueryBuilder('ce')
            ->where('ce.parent IS NULL')
            ->orWhere('ce.child IS NULL')
            ->getQuery()
            ->getResult()
        ;
        foreach($badCategoryEdges as $badCategoryEdge) {
            $em->remove($badCategoryEdge);
        }
        $em->flush();

        $badCategoryObjectEdges = $categoryObjectEdgeRepository->createQueryBuilder('coe')
            ->where('coe.object IS NULL')
            ->orWhere('coe.category IS NULL')
            ->getQuery()
            ->getResult()
        ;

        foreach($badCategoryObjectEdges as $badCategoryObjectEdge) {
            $em->remove($badCategoryObjectEdge);
        }
        $em->flush();
    }
}
