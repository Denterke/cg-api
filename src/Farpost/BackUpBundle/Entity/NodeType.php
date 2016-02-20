<?php

namespace Farpost\BackUpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NodeTypes
 *
 * @ORM\Table(name="catalog.node_types")
 * @ORM\Entity(repositoryClass="Farpost\BackUpBundle\Entity\NodeTypeRepository")
 */
class NodeType
{
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
    * @ORM\Column(name="alias", type="string", length=255)
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
