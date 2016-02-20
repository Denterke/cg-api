<?php
namespace Farpost\StoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Farpost\StoreBundle\Entity\Version;

class SQLBooster
{
   private $doctrine;
   private $pdo;

   public function init()
   {
      $this->pdo = $this->doctrine->getManager('default')->getConnection();
   }

   public function syncGroupInfo($aliases)
   {
      // $this->pdo()
      //school study_type specialization course department group

   }


}
