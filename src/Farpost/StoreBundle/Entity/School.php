<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schools
 *
 * @ORM\Table(name="schools")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\SchoolRepository")
 */
class School
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
    * Set id
    *
    * @param integer $alias
    * @return Schools
    */
   public function setId($id)
   {
      $this->id = $id;
      return $this;
   }

   /**
    * @var string
    *
    * @ORM\Column(name="alias", type="string", length=255)
    */
   protected $alias;

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
    * @return Schools
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

   public function getName()
   {
      return $this->alias;
   }

   public function getData()
   {
      return [
         'id'    => $this->id,
         'alias' => $this->alias
      ];
   }

   public function __toString()
   {
     return $this->alias;
   }
}
