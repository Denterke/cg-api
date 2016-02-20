<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * LessonTypeRepository
 */
class LessonTypeRepository extends EntityRepository
{
   public function syncValue($alias)
   {
      $lesson_type = $this->findOneBy(['alias' => $alias]);
      if (!is_null($lesson_type)) {
         return $lesson_type;
      }
      $lesson_type = new LessonType();
      $lesson_type->setAlias($alias);
      $this->_em->persist($lesson_type);
      $this->_em->flush();
      return $lesson_type;
   }

   public function realizeFake(&$fakes)
   {
      $pdo = $this->_em->getConnection();
      $stmt = $pdo->prepare("SELECT id, alias FROM lesson_types;");
      $stmt->execute();
      $objs = [];
      while ($row = $stmt->fetch()) {
         $objs[$row['alias']] = $row;
      }
      $keys = array_keys($objs);
      $insStr = 
         "INSERT INTO
            lesson_types
            (alias)
          VALUES";
      $firstIns = true;
      $resRefs = [];
      for ($i = 0; $i < count($fakes); $i++) {
         $objIdx = array_search($fakes[$i], $keys);
         if ($objIdx === false) {
            $insStr .= $firstIns ? ' ' : ', ';
            $firstIns = false;
            $insStr .= "('{$fakes[$i]}')";
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