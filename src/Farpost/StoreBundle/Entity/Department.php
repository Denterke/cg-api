<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departments
 *
 * @ORM\Table(name="departments")
 * @ORM\Entity
 */
class Department
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
    * @var School
    *
    * @ORM\ManyToOne(targetEntity="School")
    * @ORM\JoinColumn(name="school_id", referencedColumnName="id")
    */
   private $school;

   /**
    * @var Study_type
    *
    * @ORM\ManyToOne(targetEntity="Study_type")
    * @ORM\JoinColumn(name="study_type_id", referencedColumnName="id")
    */
   private $study_type;


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
    * @return Departments
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
     * Set school
     *
     * @param \Farpost\StoreBundle\Entity\School $school
     * @return Department
     */
    public function setSchool(\Farpost\StoreBundle\Entity\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \Farpost\StoreBundle\Entity\School 
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set study_type
     *
     * @param \Farpost\StoreBundle\Entity\Study_type $studyType
     * @return Department
     */
    public function setStudyType(\Farpost\StoreBundle\Entity\Study_type $studyType = null)
    {
        $this->study_type = $studyType;

        return $this;
    }

    /**
     * Get study_type
     *
     * @return \Farpost\StoreBundle\Entity\Study_type 
     */
    public function getStudyType()
    {
        return $this->study_type;
    }
}
