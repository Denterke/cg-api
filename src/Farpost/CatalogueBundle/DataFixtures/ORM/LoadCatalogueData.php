<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 17/07/15
 * Time: 12:33
 */

namespace Farpost\CatalogueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\CatalogueBundle\Entity\CatalogueCategory;

class LoadCatalogueData implements  FixtureInterface{
    public function load(ObjectManager $manager)
    {
        $category = $manager->getRepository('FarpostCatalogueBundle:CatalogueCategory')->findOneBy(['isRoot' => true]);
        if ($category) {
            return;
        }
        $category = new CatalogueCategory();
        $category->setName('Справочник')
            ->setIsRoot(true)
            ->setIsOrganization(false)
            ->setDescription('');
        $manager->persist($category);

        $manager->flush();
    }
}
