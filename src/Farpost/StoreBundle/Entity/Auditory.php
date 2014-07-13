<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auditories
 *
 * @ORM\Table(name="auditories")
 * @ORM\Entity
 */
class Auditory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @var Auditory_type
     *
     * @ORM\ManyToOne(targetEntity="Auditory_type")
     * @ORM\JoinColumn(name="auditory_type_id", referencedColumnName="id")
     */
    private $auditory_type;

    /**
     * @var Building
     *
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     */
    private $building;

    /**
     * @var Level
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     */
    private $level;


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
     * @return Auditory
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
     * Set auditory_type
     *
     * @param \Farpost\StoreBundle\Entity\Auditory_type $auditoryType
     * @return Auditory
     */
    public function setAuditoryType(\Farpost\StoreBundle\Entity\Auditory_type $auditoryType = null)
    {
        $this->auditory_type = $auditoryType;

        return $this;
    }

    /**
     * Get auditory_type
     *
     * @return \Farpost\StoreBundle\Entity\Auditory_type
     */
    public function getAuditoryType()
    {
        return $this->auditory_type;
    }

    /**
     * Set building
     *
     * @param \Farpost\StoreBundle\Entity\Building $building
     * @return Auditory
     */
    public function setBuilding(\Farpost\StoreBundle\Entity\Building $building = null)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return \Farpost\StoreBundle\Entity\Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set level
     *
     * @param \Farpost\StoreBundle\Entity\Level $level
     * @return Auditory
     */
    public function setLevel(\Farpost\StoreBundle\Entity\Level $level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \Farpost\StoreBundle\Entity\Level
     */
    public function getLevel()
    {
        return $this->level;
    }
}
