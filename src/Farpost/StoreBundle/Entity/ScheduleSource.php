<?php

namespace Farpost\StoreBundle\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Versions
 *
 * @ORM\Table(name="schedule_sources")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\ScheduleSourceRepository")
 */
class ScheduleSource
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    */
   private $id;

   /**
    * @var date
    *
    * @ORM\Column(name="v_datetime", type="integer")
    */
   protected $v_datetime;

   /**
    * @var string
    *
    * @ORM\Column(name="base", type="string")
    */
   protected $base;

   /**
    * @ORM\ManyToOne(targetEntity="Group")
    * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
    */
   protected $group;

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
     * Set v_datetime
     *
     * @param integer $vDatetime
     * @return ScheduleSource
     */
    public function setVDatetime($vDatetime)
    {
        $this->v_datetime = $vDatetime;

        return $this;
    }

    /**
     * Get v_datetime
     *
     * @return integer
     */
    public function getVDatetime()
    {
        return $this->v_datetime;
    }

    /**
     * Set base
     *
     * @param string $base
     * @return ScheduleSource
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    public function cpFile()
    {
        $newName = $this->group->getId() . '_' . $this->v_datetime;
        if (!copy($this->base, SSOURCE_DIR . "/$newName")) {
            throw new \Exception("Can not copy file {$this->base} to {$newName}");
        }
        $this->base = SSOURCE_DIR . "/$newName";
    }

    /**
     * Get base
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set group
     *
     * @param \Farpost\StoreBundle\Entity\Group $group
     * @return ScheduleSource
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
}
