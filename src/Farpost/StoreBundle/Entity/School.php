<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schools
 *
 * @ORM\Table(name="schools")
 * @ORM\Entity
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
}
