<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SchedulePartRepository
 */
class SchedulePartRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder()
                 ->select('sp')
                 ->from('FarpostStoreBundle:SchedulePart', 'sp');
      return $qb;
   }

   public function syncValue($user, $discipline, $group)
   {
      $qb = $this->_prepareQb();
      try {
         $schedule_part = $qb->where($qb->expr()->eq('sp.professor', '?1'))
                             ->andWhere($qb->expr()->eq('sp.discipline', '?2'))
                             ->andWhere($qb->expr()->eq('sp.group', '?3'))
                             ->setParameter(1, $user->getId())
                             ->setParameter(2, $discipline->getId())
                             ->setParameter(3, $group->getId())
                             ->getQuery()
                             ->getSingleResult();
      }
      catch (\Exception $e) {
         $schedule_part = new SchedulePart();
         $schedule_part->setProfessor($user)
                       ->setGroup($group)
                       ->setDiscipline($discipline);
         $this->_em->persist($schedule_part);
         $this->_em->flush();
         return $schedule_part;
      }
      return $schedule_part;
   }

   public function realizeFake(&$fakes)
   {
      // print_r($fakes);
      // exit;
      $pdo = $this->_em->getConnection();
      $stmt = $pdo->prepare("SELECT * FROM schedule_parts;");
      $objs = [];
      while ($row = $stmt->fetch()) {
         $objs[
            "{$row['professor_id']} {$row['group_id']} {$row['discipline_id']} {$row['semester_id']}"
         ] = $row;
      }
      $keys = array_keys($objs);
      $insStr = 
         "INSERT INTO
            schedule_parts
            (professor_id, group_id, discipline_id, semester_id)
          VALUES";
      $firstIns = true;
      $resRefs = [];
      for ($i = 0; $i < count($fakes); $i++) {
         $fake_key = "{$fakes[$i]['user']} {$fakes[$i]['group']} {$fakes[$i]['disc']} {$fakes[$i]['semester']}";
         $objIdx = array_search($fake_key, $keys);
         if ($objIdx === false) {
            $insStr .= $firstIns ? ' ' : ', ';
            $firstIns = false;
            try {
               $insStr .= "('{$fakes[$i]['user']}', '{$fakes[$i]['group']}', '{$fakes[$i]['disc']}', {$fakes[$i]['semester']})";
            } catch (\Exception $e) {
               print_r($fakes[$i]);
               throw $e;
            }
            array_push($resRefs, $i);
         } else {
            $fakes[$i] = $objs[$fakes[$i]]['id'];
         }
      }
      if (!$firstIns) {
         $insStr .= " returning id";
         // echo $insStr;
         // exit;
         $stmt = $pdo->prepare($insStr);
         $stmt->execute();
         $ids = $stmt->fetchAll();
         // print_r($ids);
         // exit;
         for ($i = 0; $i < count($ids); $i++) {
            $fakes[$resRefs[$i]] = $ids[$i]['id'];
         }
      }
   }
}