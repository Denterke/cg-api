<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\ScheduleRepository")
 */
class Schedule
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
    * @var integer
    *
    * @ORM\Column(name="period", type="integer")
    */
   protected $period;

   /**
    * @var SchedulePart
    *
    * @ORM\ManyToOne(targetEntity="SchedulePart")
    * @ORM\JoinColumn(name="schedule_part_id", referencedColumnName="id", onDelete="CASCADE")
    */
   protected $schedule_part;

   /**
    * @var Auditory
    *
    * @ORM\ManyToOne(targetEntity="Auditory")
    * @ORM\JoinColumn(name="auditory_id", referencedColumnName="id")
    */
   protected $auditory;

   /**
    * @var Time
    *
    * @ORM\ManyToOne(targetEntity="Time")
    * @ORM\JoinColumn(name="time_id", referencedColumnName="id")
    */
   protected $time;

   /**
    * @var LessonType
    *
    * @ORM\ManyToOne(targetEntity="LessonType")
    * @ORM\JoinColumn(name="lesson_type_id", referencedColumnName="id")
    */
   protected $lesson_type;

   /**
    *@ORM\OneToMany(targetEntity="ScheduleRendered", mappedBy="schedule")
    */
   protected $schedule_rendered;

   /**
    * @var Semester
    *
    * @ORM\ManyToOne(targetEntity="Semester")
    * @ORM\JoinColumn(name="semester_id", referencedColumnName="id")
    */
   protected $semester;

   /**
    * @var integer
    * @ORM\Column(name="day", type="integer")
    */
   protected $day;


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
    * Set period
    *
    * @param integer $period
    * @return Schedule
    */
   public function setPeriod($period)
   {
      $this->period = $period;
      return $this;
   }

   /**
    * Get period
    *
    * @return integer
    */
   public function getPeriod()
   {
      return $this->period;
   }

   /**
    * Set schedule_part
    *
    * @param \Farpost\StoreBundle\Entity\SchedulePart $schedulePart
    * @return Schedule
    */
   public function setSchedulePart(\Farpost\StoreBundle\Entity\SchedulePart $schedulePart = null)
   {
      $this->schedule_part = $schedulePart;
      return $this;
   }

   /**
    * Get schedule_part
    *
    * @return \Farpost\StoreBundle\Entity\SchedulePart
    */
   public function getSchedulePart()
   {
      return $this->schedule_part;
   }

   /**
    * Set auditory
    *
    * @param \Farpost\StoreBundle\Entity\Auditory $auditory
    * @return Schedule
    */
   public function setAuditory(\Farpost\StoreBundle\Entity\Auditory $auditory = null)
   {
      $this->auditory = $auditory;
      return $this;
   }

   /**
    * Get auditory
    *
    * @return \Farpost\StoreBundle\Entity\Auditory
    */
   public function getAuditory()
   {
      return $this->auditory;
   }

   /**
    * Set time
    *
    * @param \Farpost\StoreBundle\Entity\Time $time
    * @return Schedule
    */
   public function setTime(\Farpost\StoreBundle\Entity\Time $time = null)
   {
      $this->time = $time;
      return $this;
   }

   /**
    * Get time
    *
    * @return \Farpost\StoreBundle\Entity\Time
    */
   public function getTime()
   {
      return $this->time;
   }

   /**
    * Set lesson_type
    *
    * @param \Farpost\StoreBundle\Entity\LessonType $lessonType
    * @return Schedule
    */
   public function setLessonType(\Farpost\StoreBundle\Entity\LessonType $lessonType = null)
   {
      $this->lesson_type = $lessonType;
      return $this;
   }

   /**
    * Get lesson_type
    *
    * @return \Farpost\StoreBundle\Entity\LessonType
    */
   public function getLessonType()
   {
      return $this->lesson_type;
   }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schedule_rendered = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add schedule_rendered
     *
     * @param \Farpost\StoreBundle\Entity\ScheduleRendered $scheduleRendered
     * @return Schedule
     */
    public function addScheduleRendered(\Farpost\StoreBundle\Entity\ScheduleRendered $scheduleRendered)
    {
        $this->schedule_rendered[] = $scheduleRendered;

        return $this;
    }

    /**
     * Remove schedule_rendered
     *
     * @param \Farpost\StoreBundle\Entity\ScheduleRendered $scheduleRendered
     */
    public function removeScheduleRendered(\Farpost\StoreBundle\Entity\ScheduleRendered $scheduleRendered)
    {
        $this->schedule_rendered->removeElement($scheduleRendered);
    }

    /**
     * Get schedule_rendered
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScheduleRendered()
    {
        return $this->schedule_rendered;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return Schedule
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set semester
     *
     * @param \Farpost\StoreBundle\Entity\Semester $semester
     * @return Schedule
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
}
