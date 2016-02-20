<?php
namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\Config;

class LoadDeployData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $param = $manager->getRepository('FarpostStoreBundle:Config')
            ->findOneBy(['param' => 'wipeTS']);
        if (is_null($param)) {
            $param = new Config();
            $param->setParam('wipeTS');
            $manager->persist($param);
            $manager->flush();
        }
        $dt = new \DateTime();
        $param->setValue($dt->getTimestamp());
        $manager->merge($param);
        $manager->flush();
    }
}