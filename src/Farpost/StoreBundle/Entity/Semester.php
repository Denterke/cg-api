<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Semester
 *
 * @ORM\Table(name="semesters")
 * @ORM\Entity
 */
class Semester
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    */
   public $id;

   /**
    * @var string
    *
    * @ORM\Column(name="alias", type="string")
    */
   protected $alias;


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
    * @param date
    * @return Semester
    */
   public function setTimeStart(\DateTime $timeStart)
   {
      $this->time_start = $timeStart;
      return $this;
   }

   /**
    * Get time_start
    *
    * @return date
    */
   public function getTimeStart()
   {
      return $this->time_start;
   }

   /**
    * Set time_end
    *
    * @param date
    * @return Semester
    */
   public function setTimeEnd(\DateTime $timeEnd)
   {
      $this->time_end = $timeEnd;
      return $this;
   }

   /**
    * Get time_end
    *
    * @return date
    */
   public function getTimeEnd()
   {
      return $this->time_end;
   }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Semester
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
