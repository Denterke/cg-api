<?php
namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\Semester;
use Farpost\StoreBundle\Entity\Config;

class LoadSemesterData implements FixtureInterface
{
    public static $CURRENT_SEMESTER = 4;
    public function load(ObjectManager $manager)
    {
        $semester = $manager->getRepository('FarpostStoreBundle:Semester')
            ->findOneBy(['id' => 1]);
        if (is_null($semester)) {
            $semester = new Semester();
            $semester->id = 1;
            $start_time = new \DateTime();
            $semester->id = 1;
            $start_time->setTimestamp(strtotime('01.09.2014'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('31.12.2014'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2014-2015 (1)');
            $manager->persist($semester);
            $manager->flush();
        }

        $semester = $manager->getRepository('FarpostStoreBundle:Semester')
            ->findOneBy(['id' => 2]);
        if (is_null($semester)) {
            echo "is_null";
            $semester = new Semester();
            $semester->id = 2;
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('09.02.2015'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('15.06.2015'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2014-2015 (2)');
            $manager->persist($semester);
            $manager->flush();
        } else {
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('09.02.2015'    ));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('15.06.2015'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2014-2015 (2)');
            $manager->merge($semester);
            $manager->flush();
        }

        $semester = $manager->getRepository('FarpostStoreBundle:Semester')
            ->findOneBy(['id' => 3]);
        if (is_null($semester)) {
            $semester = new Semester();
            $semester->id = 3;
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('01.09.2015'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('31.12.2015'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2015-2016 (1)');
            $manager->persist($semester);
            $manager->flush();
        } else {
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('01.09.2015'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('31.12.2015'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2015-2016 (1)');
            $manager->merge($semester);
            $manager->flush();
        }

        $semester = $manager->getRepository('FarpostStoreBundle:Semester')
            ->findOneBy(['id' => 4]);
        if (is_null($semester)) {
            $semester = new Semester();
            $semester->id = 4;
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('09.02.2016'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('15.06.2016'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2015-2016 (2)');
            $manager->persist($semester);
            $manager->flush();
        } else {
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime('09.02.2016'));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime('15.06.2016'));
            $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('2015-2016 (2)');
            $manager->merge($semester);
            $manager->flush();
        }

        $q = $manager->createQueryBuilder()
            ->update('FarpostStoreBundle:SchedulePart', 's')
            ->set('s.lessonType', "NULL")
            ->where('s.semester < ?1')
            ->setParameter(1, 3)
            ->getQuery();
        $q->execute();
        $config = $manager->getRepository('FarpostStoreBundle:Config')
            ->findOneBy(['param' => 'current_semester']);
        if (is_null($config) || !($config->getValue())) {
            $config = new Config();
            $config->setParam('current_semester')->setValue(self::$CURRENT_SEMESTER);
            $manager->persist($config);
            $manager->flush();
        } else {
            $config->setParam('current_semester')->setValue(self::$CURRENT_SEMESTER);
            $manager->merge($config);
            $manager->flush();
        }
    }
}