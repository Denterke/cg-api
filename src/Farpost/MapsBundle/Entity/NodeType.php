<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 17:25
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NodeTypes
 *
 * @ORM\Table(name="map_node_types")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\NodeTypeRepository")
 */
class NodeType
{

    static public $sqliteAnnotations = [
        'table'  => 'node_types',
        'fields' => [
            [
                'name' =>'_id',
                'type' => 'INTEGER',
                'PK' => true,
                'RK' => '',
                'nullable' => false,
                'getter' => 'getId'
            ],
            [
                'name' => 'alias',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getAlias'
            ],
        ]
    ];

    const STAIR = 11;
    const ELEVATOR = 13;
    const ESCALATOR = 21;
    const STAIR_WEIGHT = 5000;
    const ELEVATOR_WEIGHT = 1000;
    const ESCALATOR_WEIGHT = 7000;
    const FRACTION_SHIFT = 10000;

    static public $WEIGHTS = [
        self::STAIR => self::STAIR_WEIGHT,
        self::ELEVATOR => self::ELEVATOR_WEIGHT,
        self::ESCALATOR => self::ESCALATOR_WEIGHT
    ];


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    protected $alias;

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
     * @return NodeType
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
     * Set id
     *
     * @param integer $id
     * @return NodeType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
