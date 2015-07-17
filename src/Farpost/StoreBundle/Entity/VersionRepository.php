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
        $used = [
            Version::CATALOG => 0,
            Version::MAP => 0,
            Version::ZIP_PLANS => 0,
            Version::CATALOG_V2 => 0,
            Version::LEVEL_0 => 0,
            Version::LEVEL_1 => 0,
            Version::LEVEL_2 => 0,
            Version::LEVEL_3 => 0,
            Version::LEVEL_4 => 0,
            Version::LEVEL_5 => 0,
            Version::LEVEL_6 => 0,
            Version::LEVEL_7 => 0,
            Version::LEVEL_8 => 0,
            Version::LEVEL_9 => 0,
            Version::LEVEL_10 => 0,
            Version::LEVEL_11 => 0,
            Version::LEVEL_12 => 0,
            Version::GRAPH_DUMP => 0
        ];
        foreach ($recs as &$rec) {
            $dt = date('d-m-Y, G:i:s', $rec['v_datetime']);
            $used[$rec['type']] = 1;
            $elem = [
                'version' => $dt,
                'type' => Version::typeToString($rec['type']),
                'type_id' => $rec['type']
            ];
            array_push($result, $elem);
        }
        foreach($used as $type => $isUsed) {
            if ($isUsed) {
                continue;
            }
            $used[$type] = 1;
            $elem = [
                'version' => 'Нет базы',
                'type' => Version::typeToString($type),
                'type_id' => $type
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
}