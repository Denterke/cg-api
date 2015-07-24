<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;

/*
 *VersionRepository
 */
class VersionRepository extends EntityRepository
{
    //select * from versions v where (select count(*) from versions where type = v.type and v_datetime > v.v_datetime) < 1;
    private function _prepareQB()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('v')
            ->from('FarpostStoreBundle:Version', 'v');
        $subquery = $this->_em->createQueryBuilder()
            ->select('count(a.id)')
            ->from('FarpostStoreBundle:Version', 'a')
            ->where('a.type = v.type')
            ->andWhere('a.v_datetime > v.v_datetime')
        ;
        $qb->where('(' . $subquery->getDQL() . ') < 1')
            ->andWhere('v.isProcessing = false');
        return $qb;
    }

    private function _finalizeNames(&$recs)
    {
        $result = [];
        foreach($recs as &$rec) {
            if ($rec->isPlan()) {
                array_push($result, STATIC_DIR . "/" . $rec->getBase());
            }
        }
        return $result;
    }

    private function _finalize(&$recs, $hostname)
    {
        $result = [];
        $plans = [];
        foreach($recs as &$rec) {
            $dt = $rec['v_datetime'];
            $path = 'http://' . $hostname . '/update/' . $rec['base'];
            $type = $rec['type'];
            $elem = [
                'version' => $dt,
                'source'  => $path
            ];
            $key = null;
            switch ($type) {
                case Version::CATALOG:
                    $key = 'catalog';
                    break;
                case Version::CATALOG_V2:
                    $key = 'catalog_v2';
                    break;
                case Version::MAP:
                    $key = 'map';
                    break;
                case Version::ZIP_PLANS:
                    $key = 'plans_zip';
                    break;
            }
            if ($key) {
                $result[$key] = $elem;
                continue;
            }
            if (Version::isTypeLevel($type)) {
                $elem['level'] = $type;
                array_push($plans, $elem);
            }
        }
        if (count($plans) > 0) {
            $result['plans'] = $plans;
        }
        $recs = $result;
    }

    private function _finalizeWeb(&$recs)
    {
        $result = [];
        $versions = [
            Version::CATALOG => [
                'used' => 0,
                'class' => 'download'
            ],
            Version::MAP => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::ZIP_PLANS => [
                'used' => 0,
                'class' => 'download'
            ],
            Version::CATALOG_V2 => [
                'used' => 0,
                'class' => 'download'
            ],
            Version::LEVEL_0 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_1 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_2 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_3 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_4 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_5 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_6 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_7 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_8 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_9 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_10 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_11 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::LEVEL_12 => [
                'used' => 0,
                'class' => 'upload'
            ],
            Version::GRAPH_DUMP => [
                'used' => 0,
                'class' => 'download'
            ],
            Version::ZIP_LIKE_MAPS_VL => [
                'used' => 0,
                'class' => 'download'
            ]
        ];
        foreach ($recs as &$rec) {
            $dt = date('d-m-Y, G:i:s', $rec['v_datetime']);
            $versions[$rec['type']]['used'] = 1;
            $elem = [
                'version' => $dt,
                'type' => Version::typeToString($rec['type']),
                'type_id' => $rec['type'],
                'class' => $versions[$rec['type']]['class']
            ];
            array_push($result, $elem);
        }
        foreach($versions as $type => $settings) {
            if ($settings['used']) {
                continue;
            }
            $versions[$type]['used'] = 1;
            $elem = [
                'version' => 'Нет базы',
                'type' => Version::typeToString($type),
                'type_id' => $type,
                'class' => $versions[$type]['class']
            ];
            array_push($result, $elem);
        }
        return $result;
    }

    public function getBases($hostname)
    {
        $recs = $this->_prepareQB()->getQuery()->getArrayResult();
        $this->_finalize($recs, $hostname);
        return $recs;
    }

    public function getForWeb()
    {
        $recs = $this->_prepareQB()->getQuery()->getArrayResult();
        return $this->_finalizeWeb($recs);
    }

    public function getFileNames()
    {
        $recs = $this->_prepareQB()->getQuery()->getResult();
        return $this->_finalizeNames($recs);
    }

    public function getLastVersions()
    {
        return $this->_prepareQB()->getQuery()->getResult();
    }

    public function getLastVersionOfType($type)
    {
        return $this->_prepareQB()
            ->andWhere('v.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    public function getProcessingEntitiesCount($type)
    {
        return $this->_em->createQueryBuilder()
            ->select('count(v.id)')
            ->from('FarpostStoreBundle:Version', 'v')
            ->where('v.isProcessing = true')
            ->andWhere('v.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult()
        ;

    }

    public function getBasesLikeMapsVL($hostname)
    {
        $mapsZip = $this->_prepareQB()
            ->andWhere('v.type = :type')
            ->setParameter('type', Version::ZIP_LIKE_MAPS_VL)
            ->getQuery()
            ->getSingleResult()
        ;
        $result = [];
        if ($mapsZip) {
            $result[] = [
                'id' => 200, //hardcode
                'name' => 'Карта с уровнями и справочником',
                'version' => $mapsZip->getVersionNumber(),
                'url' => "http://$hostname/update/" . $mapsZip->getBase(),
                'size' => $mapsZip->getFileSize(),
                'checksum' => $mapsZip->getChecksum()
            ];
        }

        return $result;
    }
}