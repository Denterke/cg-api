<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SchoolRepository
 */
class GroupRepository extends EntityRepository
{
    private function _prepareQB()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('g')
            ->distinct()
            ->from('FarpostStoreBundle:Group', 'g');
        return $qb;
    }

    private function _finalizeList(&$recs)
    {
        $result = [];
        foreach($recs as &$rec) {
            $elem = [
                'id' => $rec->getId(),
                'alias' => $rec->getAlias(),
                'department' => $rec->getStudySet()->getDepartment()->getAlias()
            ];
            array_push($result, $elem);
        }
        return $result;
    }

    public function getList($t)
    {
        $recs = $this->_prepareQB()
                    ->where('g.lastModified >= :time')
                    ->setParameter('time', $t)
                    ->getQuery()
                    ->getResult();
        return $this->_finalizeList($recs);
    }

    /**
     * Unknown method, should be removed
     * Added [1.0]
     * @param   $alias
     * @param   $study_set
     * @return Group
     */
    public function syncValue($alias, $study_set)
    {
        error_log("DEPRICATED METHOD GroupRepository->syncValue() CALLED");
        $qb = $this->_prepareQB();
        try {
            $group = $qb->where('g.alias = ?1')
                ->andWhere($qb->expr()->eq('g.study_set', '?2'))
                ->setParameter(1, $alias)
                ->setParameter(2, $study_set->getId())
                ->getQuery()
                ->getSingleResult();
            $qb2 = $this->_em->createQueryBuilder();
            $qb2->update('FarpostStoreBundle:LastModified', 'lm')
                ->set('lm.group_id', ':group_id2')
                ->where($qb2->expr()->eq('lm.table_name', ':s_table'))
                ->setParameter('s_table', 'schedule_rendered')
                ->setParameter('group_id2', $group->getId())
                ->andWhere(
                    $qb2->expr()->in(
                        'lm.record_id',
                        $this->_em->createQueryBuilder()->select('sr.id')
                            ->from('FarpostStoreBundle:ScheduleRendered', 'sr')
                            ->innerJoin('FarpostStoreBundle:Schedule',     's',  Join::WITH, 'sr.schedule = s.id')
                            ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
                            ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id')
                            ->innerJoin('FarpostStoreBundle:Semester',     'sm', Join::WITH, 's.semester = sm.id')
                            ->where($qb2->expr()->eq('sm.id', ':sem_id'))
                            ->andWhere($qb2->expr()->eq('g.id', ':group_id'))
                            ->getQuery()->getDQL()
                        )
                    )
                ->setParameter('sem_id', 1)
                ->setParameter('group_id', $group->getId())
                ->getQuery()
                ->getResult();
            $this->_em->createQueryBuilder()
                ->delete('FarpostStoreBundle:SchedulePart', 'sp')
                ->where($qb->expr()->eq('sp.group', '?1'))
                ->setParameter(1, $group->getId())
                ->getQuery()
                ->getResult();
        }
        catch (\Doctrine\ORM\NoResultException $e) {
            $group = new Group();
            $group->setAlias($alias)->setStudySet($study_set);
            $this->_em->persist($group);
            $this->_em->flush();
            return $group;
        }
        return $group;
    }

    public function getUpdate($last_time, $group_id)
    {
        $recs = $this->_em
            ->createQueryBuilder()
            ->select('g.id, g.alias as g_alias, d.alias as d_alias, lm.status')
            ->from('FarpostStoreBundle:Group', 'g')
            ->innerJoin('FarpostStoreBundle:StudySet', 'ss', Join::WITH, 'g.study_set = ss.id')
            ->innerJoin('FarpostStoreBundle:Department', 'd', Join::WITH, 'ss.department = d.id')
            ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = g.id')
            ->where('lm.table_name = :table_name')
            ->andWhere('lm.last_modified > :last_time')
            ->andWhere('g.id = :group_id')
            ->setParameter('table_name', 'groups')
            ->setParameter('group_id', $group_id)
            ->setParameter('last_time', $last_time)
            ->getQuery()
            ->getResult();
        return array_map(
            function($rec) {
                return [
                    "id"         => $rec['id'],
                    "alias"      => $rec['g_alias'],
                    "department" => $rec['d_alias'],
                    "status"     => $rec['status']
                ];
            }, 
            $recs
        );
    }
}