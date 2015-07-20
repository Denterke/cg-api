<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 14:10
 */

namespace Farpost\MapsBundle\Services;

use Doctrine\ORM\EntityManager;
use Farpost\StoreBundle\Entity\Version;

class GraphImporter
{

    private static $mapsEntites = [
        'Building',
        'NodeType',
        'Node',
        'Edge',
        'EdgePoint'
    ];

    private $doctrine;

    private $owner;

    public function __construct($doctrine, $owner)
    {
        $this->doctrine = $doctrine;
        $this->owner = $owner;
    }

    public function clearMaps($em)
    {
        for ($i = count(self::$mapsEntites) - 1; $i >= 0; $i--) {
            $mapsEntity = self::$mapsEntites[$i];
            $q = $em->createQuery("delete from FarpostMapsBundle:$mapsEntity a");
            $q->execute();
        }
    }

    public function copyToMaps($em, $backUpEm)
    {
        $em->getRepository("FarpostMapsBundle:Level")->generate();
        foreach(self::$mapsEntites as $mapsEntity) {
            echo "$mapsEntity\n";
            echo number_format(memory_get_usage()) . "\n";
            $em->getRepository("FarpostMapsBundle:$mapsEntity")->copyFrom($backUpEm);
        }
    }

    public function finalize(EntityManager $em)
    {
        $em->getRepository('FarpostMapsBundle:Edge')->normalize();
    }

    public function import($filename)
    {
        $em = $this->doctrine->getManager();
        $backUpEm = $this->doctrine->getManager('back_up');

        $dt = new \DateTime();
        $version = new Version();
        $version->setType(Version::GRAPH_DUMP)
            ->setVDatetime($dt->getTimestamp())
            ->setBase(basename($filename))
            ->setIsProcessing(true)
        ;

        $em->persist($version);
        $em->flush();

        $backUpDatabase = "back_up_catalog";
        system("/usr/bin/pg_restore --host=localhost -U {$this->owner} -c -O -d $backUpDatabase --schema=catalog $filename");

        echo number_format(memory_get_usage()) . "\n";
        $this->clearMaps($em);
        echo number_format(memory_get_usage()) . "\n";
        $this->copyToMaps($em, $backUpEm);
        echo number_format(memory_get_usage()) . "\n";
        $this->finalize($em);
        echo number_format(memory_get_usage()) . "\n";

        $version->setIsProcessing(false);
        $em->merge($version);
        $em->flush();
    }
}