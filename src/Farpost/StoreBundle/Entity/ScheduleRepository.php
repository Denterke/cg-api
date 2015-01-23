<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Farpost\StoreBundle\Entity\Schedule;
use Farpost\StoreBundle\Entity\Group;
use Farpost\StoreBundle\Entity\LessonType;

/*
 *ScheduleRepository
 */
class ScheduleRepository extends EntityRepository
{

    /**
     * Converts any positive NUMber to Day Of Week
     * Added: [1.0]
     * @param $num - positive integer
     * @return string
     */
    private function _numToDOW($num)
    {
        $days = [
            "Понедельник",
            "Вторник",
            "Среда",
            "Четверг",
            "Пятница",
            "Суббота",
            "Воскресенье"
        ];
        $num %= 7;
        if ($num == 0) {
            return "Воскресенье";
        }
        return $days[$num - 1];
    }

    /**
     * Creates simple query builder with query for this repo
     * Added: [1.0]
     * @return QueryBuilder
     */
    private function _prepareQB()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->distinct()
            ->from('FarpostStoreBundle:Schedule', 's')
            ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
            ->innerJoin('FarpostStoreBundle:Time',         't',  Join::WITH, 's.time = t.id')
            ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id')
            ->innerJoin('FarpostStoreBundle:Semester',     'sm', Join::WITH, 's.semester = sm.id');
        return $qb;
    }

    /**
     * Converts doctrine entities schedule array to php array, with some issues
     * Added: [1.0]
     * @param  Doctrine::ArrayCollection &$recs
     * @return Array
     */
    private function _finalizeWeb(&$recs)
    {
        $result = [];
        $last_day = 0;
        $last_num = 0;
        $spd = []; //schedule per day
        foreach ($recs as &$elem) {
            $current_day = $elem->getDay();
            if ($last_day != $current_day && $last_day != 0) {
            // $spd["day"] = $this->_numToDOW($last_day);
                $result[$this->_numToDOW($last_day)] = $spd;
                $spd = [];
            }
            $last_day = $current_day;
            $schedule_elem = [
                "Тип занятия"  => $elem->getLessonType()  ->getAlias(),
                "Дисциплина"   => $elem->getSchedulePart()->getDiscipline() -> getAlias(),
                "Аудитория"    => $elem->getAuditory()    ->getAlias(),
                "Профессор"    => $elem->getSchedulePart()->getProfessor()  -> getUniName(),
            ];
            $spd[$elem->getTime()->getAlias()] = $schedule_elem;
        }
        if (!empty($spd)) {
            $result[$this->_numToDOW($last_day)] = $spd;
            $spd = [];
        }
        return $result;
    }

    /**
     * Simple convertation to php array
     * Added: [1.0]
     * @param  Doctrine::ArrayCollection $recs
     * @return Array
     */
    private function _finalize(&$recs)
    {
        $result = [];
        foreach ($recs as &$elem) {
            $schedule_elem = [
                "Группа"         => $elem->getSchedulePart()->getGroup()     ->getAlias(),
                "Тип занятия"    => $elem->getLessonType()  ->getAlias(),
                "Дисциплина"     => $elem->getSchedulePart()->getDiscipline()->getAlias(),
                "Пара"           => $elem->getTime()        ->getAlias(),
                "Аудитория"      => $elem->getAuditory()    ->getAlias(),
                "Профессор"      => $elem->getSchedulePart()->getProfessor() ->getId(),
                "id"             => $elem->getId()
            ];
            array_push($result, $schedule_elem);
        }
        return $result;
    }

    /**
     * Gets schedule templates for group_id
     * Added: [1.0]
     * @param  integer $group_id
     * @return Array
     */
    public function getForGroup($group_id)
    {
        $recs = $this->_prepareQB()
                    ->where('g.id = :group_id')
                    ->andWhere('sm.id = 1')
                    ->orderBy('s.day', 'ASC')
                    ->setParameter('group_id', $group_id)
                    ->getQuery()
                    ->getResult();
        $result =  $this->_finalize($recs);
        return $result;
    }

    /**
     * Gets web representation for group_id
     * Added: [1.0]
     * @param  integer $group_id
     * @return Array
     */
    public function getForGroupWeb($group_id)
    {
        $recs = $this->_prepareQB()
                    ->where('g.id = :group_id')
                    ->andWhere('sm.id = 1')
                    ->orderBy('s.day', 'ASC')
                    ->setParameter('group_id', $group_id)
                    ->getQuery()
                    ->getResult();
        $result = $this->_finalizeWeb($recs);
        return $result;
    }


    /**
     * Gets all updates for this repo for group_id since last_time
     * @param  datetime $last_time
     * @param  integer $group_id
     * @return Array
     */
    // public function getUpdate($last_time, $group_id)
    // {
    //     $recs = $this->_prepareQB()
    //                 ->select('sr, lm.status')
    //                 ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = s.id')
    //                 ->where('lm.table_name = :table_name')
    //                 ->andWhere('lm.last_modified > :time')
    //                 ->andWhere('g.id = :group_id')
    //                 ->andWhere('s.time_start <= CURRENT_DATE()')
    //                 ->andWhere('s.time_end >= CURRENT_DATE()')
    //                 ->setParameter('table_name', 'schedule_rendered')
    //                 ->setParameter('time', $last_time)
    //                 ->setParameter('group_id', $group_id)
    //                 ->getQuery()
    //                 ->getResult();
    //     $recs = $this->_finalizeUpdate($recs);
    //     return $recs;
    // }

    /**
     * Gets all time aliases for specified group_id
     * Added: [1.0]
     * @param  integer $group_id
     * @return Array
     */
    public function getNumsForGroupWeb($group_id)
    {
        $recs = $this->_prepareQB()
                    ->select('t')
                    ->where('g.id = :group_id')
                    ->orderBy('t.start_time', 'ASC')
                    ->setParameter('group_id', $group_id)
                    ->getQuery()
                    ->getResult();
        return array_map(
            function($rec) {
                return $rec->getAlias();
            },  
            $recs
        );
    }

    /**
     * Get all schedule rendered items for specified group
     * Added: [2.0]
     * @param  integer $gId group_id
     * @return Array
     */
    public function getScheduleRendered($gId, &$lastId = null, $status = 0)
    {
        $group = $this->_em->getRepository('FarpostStoreBundle:Group')->findOneById($gId);
        if (!$group) {
            return [];
        }
        $id = $group->getSRFirstId();
        $recs = $this->_prepareQB()
                     ->where('g.id = :gId')
                     ->orderBy('s.id', 'ASC')
                     ->setParameter('gId', $gId)
                     ->getQuery()
                     ->getResult();
        $i = 0;
        $result = [];
        $count = 0; //all schedule rendered count for $gId group
        foreach ($recs as &$schedule) {
            $current_time = clone $schedule->getSemester()->getTimeStart();
            $dow = $schedule->getDay();
            $current_dow = date("N", $current_time->getTimestamp());
            $period = $schedule->getPeriod();
            $end_time = clone $schedule->getSemester()->getTimeEnd();
            if ($dow < $current_dow) {
               $dow += $period;
               $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
            }
            while ($dow != $current_dow) {
               $current_dow++;
               $current_time = $current_time->add(new \DateInterval('P' . 1 . 'D'));
            }
            while ($current_time <= $end_time) {
                if ($count >= Group::MAX_SCH_RENDERED_COUNT) {
                    break;
                }
                $scheduleProcessed = [
                    "group_id"       => $gId,
                    "lesson_type_id" => $schedule->getLessonType() ? $schedule->getLessonType()->getId() : LessonType::$NOTHING_TYPE_ID,
                    "discipline_id"  => $schedule->getSchedulePart()->getDiscipline()->getId(),
                    "time_id"        => $schedule->getTime()->getId(),
                    "auditory_id"    => ($schedule->getAuditory() ? $schedule->getAuditory()->getId() : null),
                    "professor_id"   => $schedule->getSchedulePart()->getProfessor()->getId(),
                    "status"         => $status,
                    "id"             => $id,
                    "date"           => $current_time->getTimestamp() + 60 * 60 * 12
                ];
                $id++;
                $count++;
                array_push($result, $scheduleProcessed);
                $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
            }
        }
        if ($group->getMaxCount() == null || $group->getMaxCount() < $count) {
            $group->setMaxCount($count);
            $this->_em->merge($group);
            $this->_em->flush();
        }
        if ($lastId !== null) {
            $lastId = $id - 1;
        }
        return $result;
    }

    /**
     * returns updates for schedule rendered
     * Added: [2.0]
     * @param  timestamp $last_time
     * @param  integer $group_id
     * @return Array
     */
    public function getUpdate($last_time, $group_id)
    {
        $last_time = $last_time->getTimestamp();
        $group = $this->_em->getRepository('FarpostStoreBundle:Group')->findOneById($group_id);
        if (!$group) {
            return [];
        }
        if ($last_time > $group->getLastModified()) {
            return [];
        }
        $lastExistingId = 0;
        $result = $this->getScheduleRendered($group_id, $lastExistingId, 1);
        $lastAvailableId = $group->getMaxCount() + $group->getSRFirstId() - 1;
        for ($id = $lastExistingId; $id < $lastAvailableId; $id++) {
            $updateElem = [
                "group_id"       => $group_id,
                "lesson_type_id" => 0,
                "discipline_id"  => 0,
                "time_id"        => 0,
                "auditory_id"    => null,
                "professor_id"   => 0,
                "status"         => 3,
                "id"             => $id,
                "date"           => 0
            ];
            array_push($result, $updateElem);
        }
        return $result;
    }
}