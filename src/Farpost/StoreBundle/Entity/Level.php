<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Levels
 *
 * @ORM\Table(name="levels")
 * @ORM\Entity
 */
class Level
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
    * @var Building
    *
    * @ORM\ManyToOne(targetEntity="Building")
    * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
    */
   protected $building;


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
    * @return Level
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
    * Set building
    *
    * @param \Farpost\StoreBundle\Entity\Building $building
    * @return Level
    */
   public function setBuilding(\Farpost\StoreBundle\Entity\Building $building = null)
   {
      $this->building = $building;
      return $this;
   }

   /**
    * Get building
    *
    * @return \Farpost\StoreBundle\Entity\Building
    */
   public function getBuilding()
   {
      return $this->building;
   }
}
