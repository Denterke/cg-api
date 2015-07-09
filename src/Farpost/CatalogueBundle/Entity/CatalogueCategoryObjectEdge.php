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
 * @ORM\Table(name="catalogue_categories_objects")
 * @ORM\Entity
 */
class CatalogueCategoryObjectEdge {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var CatalogueObject
     *
     * @ORM\ManyToOne(targetEntity="CatalogueObject", inversedBy="categories")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $object;

    /**
     * @var CatalogueCategory
     *
     * @ORM\ManyToOne(targetEntity="CatalogueCategory", inversedBy="objects")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

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
     * Set object
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObject $object
     * @return CatalogueCategoryObjectEdge
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

    /**
     * Set category
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategory $category
     * @return CatalogueCategoryObjectEdge
     */
    public function setCategory(\Farpost\CatalogueBundle\Entity\CatalogueCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
