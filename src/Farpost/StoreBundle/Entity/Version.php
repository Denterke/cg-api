<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Versions
 *
 * @ORM\Table(name="versions")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\VersionRepository")
 */
class Version
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    */
   private $id;

   /**
    * @var date
    *
    * @ORM\Column(name="v_datetime", type="integer")
    */
   protected $v_datetime;

   /**
    * @var string
    *
    * @ORM\Column(name="base", type="string")
    */
   protected $base;

   /**
    * @var integer
    *
    * @ORM\Column(name="type", type="integer")
    */
   protected $type;
   //-20 - catalog
   //1 - level 1
   //2 - level 2 ...

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
     * Set v_datetime
     *
     * @param \DateTime $vDatetime
     * @return Version
     */
    public function setVDatetime($vDatetime)
    {
        $this->v_datetime = $vDatetime;

        return $this;
    }

    /**
     * Get v_datetime
     *
     * @return \DateTime 
     */
    public function getVDatetime()
    {
        return $this->v_datetime;
    }

    /**
     * Set base
     *
     * @param string $base
     * @return Version
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get base
     *
     * @return string 
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Version
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}
