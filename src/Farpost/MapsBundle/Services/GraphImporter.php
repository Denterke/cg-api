<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 14:10
 */

namespace Farpost\MapsBundle\Services;

use Doctrine\ORM\EntityManager;

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

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
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
            $em->getRepository("FarpostMapsBundle:$mapsEntity")->copyFrom($backUpEm);
        }
    }

    public function normalize(EntityManager $em)
    {
//        $em->getRepository
    }

    public function import($filename)
    {
        $owner = "dev";
        $backUpDatabase = "back_up_catalog";

        system("/usr/bin/pg_restore --host=localhost -U $owner -c -O -d $backUpDatabase --schema=catalog $filename");

        $em = $this->doctrine->getManager();
        $backUpEm = $this->doctrine->getManager('back_up');

        $this->clearMaps($em);
        $this->copyToMaps($em, $backUpEm);
        $this->normalize($em);
        $this->count

    }
}