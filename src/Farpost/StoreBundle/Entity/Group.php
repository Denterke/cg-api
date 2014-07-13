<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity
 */
class Group
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
   private $id;

   /**
    * @var string
    *
    * @ORM\Column(name="alias", type="string", length=255)
    */
   private $alias;

   /**
    * @var Department
    *
    * @ORM\ManyToOne(targetEntity="Department")
    * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
    */
   private $department;


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
    * @return Groups
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
     * Set department
     *
     * @param \Farpost\StoreBundle\Entity\Department $department
     * @return Group
     */
    public function setDepartment(\Farpost\StoreBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Farpost\StoreBundle\Entity\Department 
     */
    public function getDepartment()
    {
        return $this->department;
    }
}
