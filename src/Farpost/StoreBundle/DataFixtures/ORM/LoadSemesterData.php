<?php
namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\Semester;
use Farpost\StoreBundle\Entity\Config;

class LoadSemesterData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $semester = $manager->getRepository('FarpostStoreBundle:Semester')
            ->findOneBy(['id' => 1]);
        if (is_null($semester)) {
            $semester = new Semester();
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('01.09.2014'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('31.12.2014'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2014-2015 (1)');
            $manager->persist($semester);
            $manager->flush();
        }
        $config = $manager->getRepository('FarpostStoreBundle:Config')
            ->findOneBy(['param' => 'current_semester']);
        if (is_null($config) || !($config->getValue())) {
            $config = new Config();
            $config->setParam('current_semester')->setValue(1);
            $manager->persist($config);
            $manager->flush();
        }
    }
}