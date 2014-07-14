<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disciplines
 *
 * @ORM\Table(name="disciplines")
 * @ORM\Entity
 */
class Discipline
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
   protected $id;

   /**
    * @var string
    *
    * @ORM\Column(name="alias", type="string", length=255)
    */
   protected $alias;

   /**
    * @var DisciplineSection
    *
    * @ORM\ManyToOne(targetEntity="DisciplineSection")
    * @ORM\JoinColumn(name="discipline_section_id", referencedColumnName="id")
    */
   protected $discipline_section;


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
    * Set alias
    *
    * @param string $alias
    * @return Discipline
    */
   public function setAlias($alias)
   {
      $this->alias = $alias;
      return $this;
   }

   /**
    * Get alias
    *
    * @return string
    */
   public function getAlias()
   {
      return $this->alias;
   }

   /**
    * Set discipline_section
    *
    * @param \Farpost\StoreBundle\Entity\DisciplineSection $disciplineSection
    * @return Discipline
    */
   public function setDisciplineSection(\Farpost\StoreBundle\Entity\DisciplineSection $disciplineSection = null)
   {
      $this->discipline_section = $disciplineSection;
      return $this;
   }

   /**
    * Get discipline_section
    *
    * @return \Farpost\StoreBundle\Entity\DisciplineSection
    */
   public function getDisciplineSection()
   {
      return $this->discipline_section;
   }
}
