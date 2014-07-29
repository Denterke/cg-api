<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Buildings
 *
 * @ORM\Entity
 * @ORM\Table(name="buildings")
 */
class Building
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
    * @var string
    *
    * @ORM\Column(name="alias", type="string", length=255)
    */
   protected $alias;

   /**
    * @var string
    *
    * @ORM\Column(name="number", type="string", length=255)
    */
   protected $number;

   /**
    * @ORM\ManyToMany(targetEntity="BuildingType")
    * @ORM\JoinTable(name="buildings_types",
    * joinColumns={@ORM\JoinColumn(name="building_id", referencedColumnName="id")},
    * inverseJoinColumns={@ORM\JoinColumn(name="building_type_id", referencedColumnName="id")}
    * )
    */
   protected $building_types;

   public function __construct()
   {
      $this->building_types = new ArrayCollection();
   }

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
    * @return Building
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
    * Set number
    *
    * @param string $number
    * @return Building
    */
   public function setNumber($number)
   {
      $this->number = $number;
      return $this;
   }

   /**
    * Get number
    *
    * @return string
    */
   public function getNumber()
   {
      return $this->number;
   }

   /**
    * Add building_types
    *
    * @param \Farpost\StoreBundle\Entity\BuildingType $buildingTypes
    * @return Building
    */
   public function addBuildingType(\Farpost\StoreBundle\Entity\BuildingType $buildingTypes)
   {
      $this->building_types[] = $buildingTypes;
      return $this;
   }

   /**
    * Remove building_types
    *
    * @param \Farpost\StoreBundle\Entity\BuildingType $buildingTypes
    */
   public function removeBuildingType(\Farpost\StoreBundle\Entity\BuildingType $buildingTypes)
   {
      $this->building_types->removeElement($buildingTypes);
   }

   /**
    * Get building_types
    *
    * @return \Doctrine\Common\Collections\Collection
    */
   public function getBuildingTypes()
   {
      return $this->building_types;
   }
}
