<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Buildings
 *
 * @ORM\Table(name="buildings")
 * @ORM\Entity
 */
class Building
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
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @ORM\ManyToMany(targetEntity="Building_type")
     * @ORM\JoinTable(name="buildings_types",
     * joinColumns={@ORM\JoinColumn(name="building_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="building_type_id", referencedColumnName="id")}
     * )
     */
    private $building_types;

    public function __construct()
    {
        $this->building_types = new ArrayCollection();
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
     * Set alias
     *
     * @param string $alias
     * @return Building
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
     * Set number
     *
     * @param string $number
     * @return Building
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Add building_types
     *
     * @param \Farpost\StoreBundle\Entity\Building_type $buildingTypes
     * @return Building
     */
    public function addBuildingType(\Farpost\StoreBundle\Entity\Building_type $buildingTypes)
    {
        $this->building_types[] = $buildingTypes;

        return $this;
    }

    /**
     * Remove building_types
     *
     * @param \Farpost\StoreBundle\Entity\Building_type $buildingTypes
     */
    public function removeBuildingType(\Farpost\StoreBundle\Entity\Building_type $buildingTypes)
    {
        $this->building_types->removeElement($buildingTypes);
    }

    /**
     * Get building_types
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuildingTypes()
    {
        return $this->building_types;
    }
}
