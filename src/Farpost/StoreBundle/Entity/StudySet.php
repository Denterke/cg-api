<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * StudySets
 *
 * @ORM\Table(name="study_sets")
 * @ORM\Entity
 */
class StudySet
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
   * @var Specialization
   *
   * @ORM\ManyToOne(targetEntity="Specialization")
   * @ORM\JoinColumn(name="specialization_id", referencedColumnName="id", onDelete="CASCADE")
   */
   protected $specialization;

   /**
   * @var Course
   *
   * @ORM\ManyToOne(targetEntity="Course")
   * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
   */
   protected $course;

   /**
    * @ORM\ManyToMany(targetEntity="Department")
    * @ORM\JoinTable(name="department_sets",
    * joinColumns={@ORM\JoinColumn(name="study_set_id", referencedColumnName="id", onDelete="CASCADE")},
    * inverseJoinColumns={@ORM\JoinColumn(name="department_id", referencedColumnName="id", onDelete="CASCADE")}
    * )
    */
   protected $departments;

   public function __construct()
   {
    $this->departments = new ArrayCollection();
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
    * Set specialization
    *
    * @param \Farpost\StoreBundle\Entity\Specialization $specialization
    * @return StudySet
    */
   public function setSpecialization(\Farpost\StoreBundle\Entity\Specialization $specialization = null)
   {
      $this->specialization = $specialization;
      return $this;
   }

   /**
    * Get specialization
    *
    * @return \Farpost\StoreBundle\Entity\Specialization
    */
   public function getSpecialization()
   {
      return $this->specialization;
   }

   /**
    * Set course
    *
    * @param \Farpost\StoreBundle\Entity\Course $course
    * @return StudySet
    */
   public function setCourse(\Farpost\StoreBundle\Entity\Course $course = null)
   {
      $this->course = $course;
      return $this;
   }

   /**
    * Get course
    *
    * @return \Farpost\StoreBundle\Entity\Course
    */
   public function getCourse()
   {
      return $this->course;
   }

    /**
     * Add departments
     *
     * @param \Farpost\StoreBundle\Entity\Department $departments
     * @return StudySet
     */
    public function addDepartment(\Farpost\StoreBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \Farpost\StoreBundle\Entity\Department $departments
     */
    public function removeDepartment(\Farpost\StoreBundle\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepartments()
    {
        return $this->departments;
    }
}
