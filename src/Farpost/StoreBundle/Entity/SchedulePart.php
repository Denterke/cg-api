<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScheduleParts
 *
 * @ORM\Table(name="schedule_parts")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\SchedulePartRepository")
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
     * @var integer
     *
     * @ORM\Column(name="hours", type="integer", nullable=true)
     */
    protected $hours;

    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     * @ORM\JoinColumn(name="semester_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $semester;

    /**
     * @var StudyType
     *
     * @ORM\ManyToOne(targetEntity="LessonType")
     * @ORM\JoinColumn(name="lesson_type_id", referencedColumnName="id", nullable=true)
     */
    protected $lessonType;
    /**
     * @var ReportType
     *
     * @ORM\ManyToOne(targetEntity="ReportType")
     * @ORM\JoinColumn(name="report_type_id", referencedColumnName="id", nullable=true)
     */
    protected $reportType;

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
     * Set hours
     *
     * @param integer $hours
     * @return SchedulePart
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer 
     */
    public function getHours()
    {
        return $this->hours;
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

    /**
     * Set semester
     *
     * @param \Farpost\StoreBundle\Entity\Semester $semester
     * @return SchedulePart
     */
    public function setSemester(\Farpost\StoreBundle\Entity\Semester $semester = null)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * Get semester
     *
     * @return \Farpost\StoreBundle\Entity\Semester 
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * Set lessonType
     *
     * @param \Farpost\StoreBundle\Entity\LessonType $lessonType
     * @return SchedulePart
     */
    public function setLessonType(\Farpost\StoreBundle\Entity\LessonType $lessonType = null)
    {
        $this->lessonType = $lessonType;

        return $this;
    }

    /**
     * Get lessonType
     *
     * @return \Farpost\StoreBundle\Entity\LessonType 
     */
    public function getLessonType()
    {
        return $this->lessonType;
    }
<<<<<<< HEAD

    /**
<<<<<<< HEAD
     * Set ReportType
     *
     * @param \Farpost\StoreBundle\Entity\ReportType $reportType
=======
     * Set reportType
     *
     * @param \Farpost\StoreBundle\Entity\LessonType $reportType
>>>>>>> Добавил ученую степень и вид отчетсности
     * @return SchedulePart
     */
    public function setReportType(\Farpost\StoreBundle\Entity\ReportType $reportType = null)
    {
<<<<<<< HEAD
        $this->ReportType = $reportType;
=======
        $this->reportType = $reportType;
>>>>>>> Добавил ученую степень и вид отчетсности

        return $this;
    }

    /**
<<<<<<< HEAD
     * Get ReportType
     *
     * @return \Farpost\StoreBundle\Entity\ReportType 
     */
    public function getReportType()
    {
        return $this->ReportType;
=======
     * Get reportType
     *
     * @return \Farpost\StoreBundle\Entity\ReportType
     */
    public function getReportType()
    {
        return $this->reportType;
>>>>>>> Добавил ученую степень и вид отчетсности
    }
=======
>>>>>>> Revert "Добавил ученую степень и вид отчетсности"
}
