<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *UserRepository
 */
class UserRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('u.id, u.first_name, u.last_name, u.middle_name')
         ->distinct()
         ->from('FarpostStoreBundle:User', 'u')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 'u.id = sp.professor')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id');
      return $qb;
   }

   public function getForGroup($group_id)
   {
      $qb = $this->_prepareQB();
      return $qb->where('g.id = :group_id')
                 ->setParameter('group_id', $group_id)
                 ->getQuery()
                 ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                 ->getArrayResult();
   }

   public function getUpdate($last_time, $group_id)
   {
      return $this->_prepareQB()
                  ->select('u.id, u.first_name, u.last_name, u.middle_name, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = u.id')
                  ->where('lm.table_name = :table_name')
                  ->andWhere('lm.last_modified > :time')
                  ->andWhere('g.id = :group_id')
                  ->setParameter('table_name', 'users')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->getArrayResult();
   }

   public function syncValue($full_name)
   {
      // echo $full_name;
      if (rtrim($full_name) == '') {
         $last = $first = $middle = "MISHA RAK";
      } else {
         list(
            $last,
            $first,
            $middle
         ) = explode(" ", $full_name);
      }
      $professor = $this->findOneBy([
         'first_name' => $first,
         'middle_name' => $middle,
         'last_name' => $last
      ]);
      if (!is_null($professor)) {
         return $professor;
      }
      $professor = new User();
      $professor->setFirstName($first)
                ->setLastName($last)
                ->setMiddleName($middle);
      $this->_em->persist($professor);
      $this->_em->flush();
      return $professor;
   }

   public function realizeFake(&$fakes)
   {
      $pdo = $this->_em->getConnection();
      $stmt = $pdo->prepare("SELECT id, first_name, last_name, middle_name FROM users;");
      $stmt->execute();
      $objs = [];
      while ($row = $stmt->fetch()) {
         $uniname = $row['last_name'] . " " . $row['first_name'] . " " . $row['middle_name'];
         $objs[$uniname] = $row;
      }
      $keys = array_keys($objs);
      $insStr = 
         "INSERT INTO
            users
            (first_name, last_name, middle_name)
          VALUES";
      $firstIns = true;
      $resRefs = [];
      for ($i = 0; $i < count($fakes); $i++) {
         $objIdx = array_search($fakes[$i], $keys);
         if ($objIdx === false) {
            $insStr .= $firstIns ? ' ' : ', ';
            $firstIns = false;
            if (!rtrim($fakes[$i])) {
               $last = $middle = $first = 'no';
            } else {
               list(
                  $last, 
                  $first,
                  $middle
               ) = explode(' ', rtrim($fakes[$i]));
            }
            $insStr .= "('$first', '$last', '$middle')";
            array_push($resRefs, $i);
         } else {
            $fakes[$i] = $objs[$fakes[$i]]['id'];
         }
      }
      if (!$firstIns) {
         $insStr .= " returning id";
         $stmt = $pdo->prepare($insStr);
         $stmt->execute();
         $ids = $stmt->fetchAll();
         for ($i = 0; $i < count($ids); $i++) {
            $fakes[$resRefs[$i]] = $ids[$i]['id'];
         }
      }
   }
}