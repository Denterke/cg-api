<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SailsSessions
 *
 * @ORM\Table(name="session")
 * @ORM\Entity
 */
class SailsSession
{
   /**
    * @var string
    *
    * @ORM\Column(name="sid", type="string")
    * @ORM\Id
    */
   protected $sid;

   /**
    * @var json
    *
    * @ORM\Column(name="sess", type="json_array")
    */
   protected $sess;

   /**
    * @var datetime
    *
    * @ORM\Column(name="expire", type="datetime")
    */
   protected $expire;

    /**
     * Set sid
     *
     * @param string $sid
     * @return SailsSession
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return string 
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set sess
     *
     * @param array $sess
     * @return SailsSession
     */
    public function setSess($sess)
    {
        $this->sess = $sess;

        return $this;
    }

    /**
     * Get sess
     *
     * @return array 
     */
    public function getSess()
    {
        return $this->sess;
    }

    /**
     * Set expire
     *
     * @param \DateTime $expire
     * @return SailsSession
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * Get expire
     *
     * @return \DateTime 
     */
    public function getExpire()
    {
        return $this->expire;
    }
}
