<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 17/08/15
 * Time: 17:31
 */

namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\ReportType;

class LoadReportTypeData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $types = ['Экзамен', 'Зачет'];
        foreach($types as $typeName) {
            $type = $manager->getRepository('FarpostStoreBundle:ReportType')
                ->findOneBy(['alias' => $typeName]);
            if (is_null($type)) {
                $type = new ReportType();
                $type->setAlias($typeName);
                $manager->persist($type);
                $manager->flush();
            }
        }
    }

}