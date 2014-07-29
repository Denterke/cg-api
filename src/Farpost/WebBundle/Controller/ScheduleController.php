<?php

namespace Farpost\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Farpost\StoreBundle\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
   public function indexAction()
   {
      $request = Request::createFromGlobals();
      // if (!$request->query->has('group')) return new Response('Not found', 404);
      $items = $this->getDoctrine()->getManager()
                    ->getRepository('FarpostStoreBundle:Schedule')
                    // ->getForGroup($request->query->getInt('group', 0));
                    ->getForGroup(1);
      // $items = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
      return $this->render('FarpostWebBundle:Default:schedule.html.twig', ['items' => $items]);
   }
}
