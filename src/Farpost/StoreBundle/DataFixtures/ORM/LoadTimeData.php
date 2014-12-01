<?php
namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\Time;

class LoadTimeData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $times = [
            [
                "alias" => "1 пара",
                "start_time" => "08:30:00",
                "end_time"   => "10:00:00"
            ],
            [
                "alias" => "2 пара",
                "start_time" => "10:10:00",
                "end_time"   => "11:40:00"
            ],
            [
                "alias" => "3 пара",
                "start_time" => "11:50:00",
                "end_time"   => "13:20:00"
            ],
            [
                "alias" => "4 пара",
                "start_time" => "13:30:00",
                "end_time"   => "15:00:00"
            ],
            [
                "alias" => "5 пара",
                "start_time" => "15:10:00",
                "end_time"   => "16:40:00"
            ],
            [
                "alias" => "6 пара",
                "start_time" => "16:50:00",
                "end_time"   => "18:20:00",
            ],
            [
                "alias" => "7 пара",
                "start_time" => "18:30:00",
                "end_time"   => "20:00:00"
            ],
            [
                "alias" => "8 пара",
                "start_time" => "20:10:00",
                "end_time"   => "21:40:00"
            ]
        ];
        foreach($times as &$time_t) {
            $time = $manager->getRepository('FarpostStoreBundle:Time')
                ->findOneBy(['alias' => $time_t['alias']]);
            if (is_null($time)) {
                $time = new Time();
                $start_time = new \DateTime();
                $start_time->setTimestamp(strtotime($time_t['start_time']));
                $end_time = new \DateTime();
                $end_time->setTimestamp(strtotime($time_t['end_time']));
                $time->setAlias($time_t['alias'])
                    ->setStartTime($start_time)
                    ->setEndTime($end_time);
                $manager->persist($time);
                $manager->flush();
            }
        }
    }
}