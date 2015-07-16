<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 12:48
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Level
 * @package Farpost\MapsBundle\Entity
 *
 * @ORM\Table(name="map_levels")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\LevelRepository")
 */
class Level {

    const MAX_LEVEL_NUMBER = 12;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer")
     */
    protected $level;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    protected $alias;


    /**
     * Set id
     *
     * @param integer $id
     * @return Level
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set level
     *
     * @param integer $level
     * @return Level
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Level
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
