<?php

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Farpost\CatalogueBundle\Validator\Constraints as CatalogueAssert;

/**
 *
 * @ORM\Table(name="catalogue_object_schedule")
 * @ORM\Entity
 */

class CatalogueObjectSchedule
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
     * @ORM\Column(name="day_number", type="integer")
     * @CatalogueAssert\IsDayNumber
     */
    protected $dayNumber;

    /**
     * @var time
     *
     * @ORM\Column(name="startAt", type="time", nullable=true)
     */
    protected $startAt;

    /**
     * @var time
     *
     * @ORM\Column(name="endAt", type="time", nullable=true)
     */
    protected $endAt;

    /**
     * @var CatalogueObject
     * @ORM\ManyToOne(targetEntity="CatalogueObject", inversedBy="schedule")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $object;

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
     * Set dayNumber
     *
     * @param integer $dayNumber
     * @return CatalogueObjectSchedule
     */
    public function setDayNumber($dayNumber)
    {
        $this->dayNumber = $dayNumber;

        return $this;
    }

    /**
     * Get dayNumber
     *
     * @return integer 
     */
    public function getDayNumber()
    {
        return $this->dayNumber;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return CatalogueObjectSchedule
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return CatalogueObjectSchedule
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set object
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObject $object
     * @return CatalogueObjectSchedule
     */
    public function setObject(\Farpost\CatalogueBundle\Entity\CatalogueObject $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueObject 
     */
    public function getObject()
    {
        return $this->object;
    }
}
