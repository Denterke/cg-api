<?php

namespace Farpost\CatalogueBundle\Command;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogueWarnCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('catalogue:warn')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $categoryEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge');
        $categoryObjectEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');

        $criteria = new Criteria();
        $criteria
            ->orWhere($criteria->expr()->isNull('parent_id'))
            ->orWhere($criteria->expr()->isNull('child_id'))
        ;
        $badCategoryEdges = $categoryEdgeRepository->createQueryBuilder('ce')
//            ->select('ce')
//            ->from('FarpostCatalogueBundle:CatalogueCategoryEdge', 'ce')
            ->where('ce.parent IS NULL')
            ->orWhere('ce.child IS NULL')
            ->getQuery()
            ->getResult()
        ;
//        findBy([
//            'or' => [
//                [
//                    'parent_id' => null
//                ],
//                [
//                    'child_id' => null
//                ]
//            ]
//        ]);
        //Я знаю, что это плохо и непременно исправлю этот говнокод как только решу проблему со справочником.
        //Но очень горит прямо сейчас вывести на экран все проблемы с сущностями
        echo "<h1>Bad categoryedges</h1>";
        foreach($badCategoryEdges as $badCategoryEdge) {
            $info = $badCategoryEdge->getId() . "/";
            $info .= ($badCategoryEdge->getChild() ? $badCategoryEdge->getChild()->getId() : 'null') . "/";
            $info .= ($badCategoryEdge->getParent() ? $badCategoryEdge->getParent()->getId() : 'null') . "/";
            echo "<p>$info</p>";
        }

        $badCategoryObjectEdges = $categoryObjectEdgeRepository->createQueryBuilder('coe')
            ->where('coe.object IS NULL')
            ->orWhere('coe.category IS NULL')
            ->getQuery()
            ->getResult()
        ;

        echo "<h1>Bad categoryobjectedges</h1>";
        foreach($badCategoryObjectEdges as $badCategoryObjectEdge) {
            $info = $badCategoryObjectEdge->getId() . "/";
            $info .= ($badCategoryObjectEdge->getCategory() ? $badCategoryObjectEdge->getCategory()->getId() : 'null') . "/";
            $info .= ($badCategoryObjectEdge->getObject() ? $badCategoryObjectEdge->getObject()->getId() : 'null') . "/";
            echo "<p>$info</p>";
        }
    }
}
