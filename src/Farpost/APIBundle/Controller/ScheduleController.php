<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;
use Doctrine\ORM\Query\Expr\Join;

class ScheduleController extends Controller
{
   public function getSchedule()
   {
      $response = new Response();
      return $response;
   }
}
