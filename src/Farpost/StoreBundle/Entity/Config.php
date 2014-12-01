<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Config
 *
 * @ORM\Table(name="configs")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\ConfigRepository")
 */
class Config
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
    * @ORM\Column(name="param", type="string", length=255)
    */
   protected $param;

   /**
    * @var string
    *
    * @ORM\Column(name="value", type="string", length=255, nullable=true)
    */
   protected $value;

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
     * Set param
     *
     * @param string $param
     * @return Config
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get param
     *
     * @return string 
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Config
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
}
