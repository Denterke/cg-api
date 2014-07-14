<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity
 */
class Schedule
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
    * @var date
    *
    * @ORM\Column(name="time_start", type="date")
    */
   protected $time_start;

   /**
    * @var date
    *
    * @ORM\Column(name="time_end", type="date")
    */
   protected $time_end;

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
    * @ORM\JoinColumn(name="schedule_part_id", referencedColumnName="id")
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
    * Get id
    *
    * @return integer
    */
   public function getId()
   {
       return $this->id;
   }

   /**
    * Set time_start
    *
    * @param date $timeStart
    * @return Schedule
    */
   public function setTimeStart(date $timeStart)
   {
      $this->time_start = $timeStart;
      return $this;
   }

   /**
    * Get time_start
    *
    * @return date,
    */
   public function getTimeStart()
   {
      return $this->time_start;
   }

   /**
    * Set time_end
    *
    * @param date $timeEnd
    * @return Schedule
    */
   public function setTimeEnd(date $timeEnd)
   {
      $this->time_end = $timeEnd;
      return $this;
   }

   /**
    * Get time_end
    *
    * @return \date,
    */
   public function getTimeEnd()
   {
      return $this->time_end;
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
}
