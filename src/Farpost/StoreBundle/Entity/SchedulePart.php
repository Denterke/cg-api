<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScheduleParts
 *
 * @ORM\Table(name="schedule_parts")
 * @ORM\Entity
 */
class SchedulePart
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    */
   protected $id;

   /**
    * @var User
    *
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="professor_id", referencedColumnName="id")
    */
   protected $professor;

   /**
    * @var Group
    *
    * @ORM\ManyToOne(targetEntity="Group")
    * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
    */
   protected $group;

   /**
    * @var Discipline
    *
    * @ORM\ManyToOne(targetEntity="Discipline")
    * @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
    */
   protected $discipline;


   /**
    * Get id
    *
    * @return integer
    */
   public function getId()
   {
      return $this->id;
   }

   /**
    * Set professor
    *
    * @param \Farpost\StoreBundle\Entity\User $professor
    * @return SchedulePart
    */
   public function setProfessor(\Farpost\StoreBundle\Entity\User $professor = null)
   {
      $this->professor = $professor;
      return $this;
   }

   /**
    * Get professor
    *
    * @return \Farpost\StoreBundle\Entity\User
    */
   public function getProfessor()
   {
      return $this->professor;
   }

   /**
    * Set group
    *
    * @param \Farpost\StoreBundle\Entity\Group $group
    * @return SchedulePart
    */
   public function setGroup(\Farpost\StoreBundle\Entity\Group $group = null)
   {
      $this->group = $group;
      return $this;
   }

   /**
    * Get group
    *
    * @return \Farpost\StoreBundle\Entity\Group
    */
   public function getGroup()
   {
      return $this->group;
   }

   /**
    * Set discipline
    *
    * @param \Farpost\StoreBundle\Entity\Discipline $discipline
    * @return SchedulePart
    */
   public function setDiscipline(\Farpost\StoreBundle\Entity\Discipline $discipline = null)
   {
      $this->discipline = $discipline;
      return $this;
   }

   /**
    * Get discipline
    *
    * @return \Farpost\StoreBundle\Entity\Discipline
    */
   public function getDiscipline()
   {
      return $this->discipline;
   }
}
