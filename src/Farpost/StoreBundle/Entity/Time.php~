<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Times
 *
 * @ORM\Table(name="times")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\TimeRepository")
 */
class Time
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
    * @var time
    *
    * @ORM\Column(name="start_time", type="time")
    */
   protected $start_time;

   /**
    * @var time
    *
    * @ORM\Column(name="end_time", type="time")
    */
   protected $end_time;


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
    * @return Time
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
    * Set start_time
    *
    * @param time $startTime
    * @return Time
    */
   public function setStartTime(time $startTime)
   {
      $this->start_time = $startTime;
      return $this;
   }

   /**
    * Get start_time
    *
    * @return time
    */
   public function getStartTime()
   {
      return $this->start_time;
   }

   /**
    * Set end_time
    *
    * @param time $endTime
    * @return Time
    */
   public function setEndTime($endTime)
   {
      $this->end_time = $endTime;
      return $this;
   }

   /**
    * Get end_time
    *
    * @return time
    */
   public function getEndTime()
   {
      return $this->end_time;
   }
}
