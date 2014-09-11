<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScheduleRendered
 *
 * @ORM\Table(name="schedule_rendered")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\ScheduleRenderedRepository")
 */
class ScheduleRendered
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
     * @var integer,
     *
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="schedule_rendered")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $schedule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="exec_date", type="datetime")
     */
    private $exec_date;


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
     * Set exec_date
     *
     * @param \DateTime $execDate
     * @return ScheduleRendered
     */
    public function setExecDate(\DateTime $execDate)
    {
        $this->exec_date = $execDate;

        return $this;
    }

    /**
     * Get exec_date
     *
     * @return \DateTime
     */
    public function getExecDate()
    {
        return $this->exec_date;
    }

    /**
     * Set schedule
     *
     * @param \Farpost\StoreBundle\Entity\Schedule $schedule
     * @return ScheduleRendered
     */
    public function setSchedule(\Farpost\StoreBundle\Entity\Schedule $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \Farpost\StoreBundle\Entity\Schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
}
