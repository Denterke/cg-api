<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 09/07/15
 * Time: 14:36
 */

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CatalogueCategoryEdge
 * @package Farpost\CatalogueBundle\Entity
 *
 * @ORM\Table(name="catalogue_category_tree")
 * @ORM\Entity
 */
class CatalogueCategoryEdge {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var CatalogueCategory
     *
     * @ORM\ManyToOne(targetEntity="CatalogueCategory", inversedBy="children")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     */
    protected $child;

    /**
     * @var CatalogueCategory
     *
     * @ORM\ManyToOne(targetEntity="CatalogueCategory", inversedBy="parents")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

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
     * Set child
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategory $child
     * @return CatalogueCategoryEdge
     */
    public function setChild(\Farpost\CatalogueBundle\Entity\CatalogueCategory $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueCategory 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set parent
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategory $parent
     * @return CatalogueCategoryEdge
     */
    public function setParent(\Farpost\CatalogueBundle\Entity\CatalogueCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueCategory 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
