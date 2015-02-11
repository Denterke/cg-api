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
}
