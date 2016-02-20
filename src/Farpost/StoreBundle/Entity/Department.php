<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departments
 *
 * @ORM\Table(name="departments")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\DepartmentRepository")
 */
class Department
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
    * @var School
    *
    * @ORM\ManyToOne(targetEntity="School")
    * @ORM\JoinColumn(name="school_id", referencedColumnName="id", onDelete="CASCADE")
    */
   protected $school;

   /**
    * @var StudyType
    *
    * @ORM\ManyToOne(targetEntity="StudyType")
    * @ORM\JoinColumn(name="study_type_id", referencedColumnName="id")
    */
   protected $study_type;

   /**
    * Set id
    *
    * @param integer $id
    * @return Departments
    */
   public function setId($id)
   {
      $this->id = $id;
      return $this;
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
     * @param \Farpost\StoreBundle\Entity\StudyType $studyType
     * @return Department
     */
    public function setStudyType(\Farpost\StoreBundle\Entity\StudyType $studyType = null)
    {
        $this->study_type = $studyType;

        return $this;
    }

    /**
     * Get study_type
     *
     * @return \Farpost\StoreBundle\Entity\StudyType
     */
    public function getStudyType()
    {
        return $this->study_type;
    }
}
