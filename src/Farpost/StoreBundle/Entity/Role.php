<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Roles
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity
 */
class Role
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
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    protected $alias;
 
     /**
      * @ORM\OneToMany(targetEntity="Policy", mappedBy="role")
      */
    protected $policies;

    public function __construct()
    {
        $this->policies = new ArrayCollection();
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
     * @return Role
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
     * Add policies
     *
     * @param \Farpost\StoreBundle\Entity\Policy $policies
     * @return Role
     */
    public function addPolicy(\Farpost\StoreBundle\Entity\Policy $policies)
    {
        $this->policies[] = $policies;

        return $this;
    }

    /**
     * Remove policies
     *
     * @param \Farpost\StoreBundle\Entity\Policy $policies
     */
    public function removePolicy(\Farpost\StoreBundle\Entity\Policy $policies)
    {
        $this->policies->removeElement($policies);
    }

    /**
     * Get policies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPolicies()
    {
        return $this->policies;
    }
}
