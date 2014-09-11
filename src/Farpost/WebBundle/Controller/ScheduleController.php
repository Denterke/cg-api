<?php

namespace Farpost\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Farpost\StoreBundle\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
   public function scheduleAction()
   {
      $request = Request::createFromGlobals();
      // if (!$request->query->has('group')) return new Response('Not found', 404);
      $items = $this->getDoctrine()->getManager()
                    ->getRepository('FarpostStoreBundle:Schedule')
                    // ->getForGroupWeb($request->query->getInt('group', 0));
                    ->getForGroupWeb(2);
      $nums = $this->getDoctrine()->getManager()
                   ->getRepository('FarpostStoreBundle:Schedule')
                   ->getNumsForGroupWeb(2);
      $dows = [
         ['', 'Понедельник', 'Вторник'],
         ['', 'Среда', 'Четверг'],
         ['', 'Пятница', 'Суббота']
      ];
      return $this->render(
         'FarpostWebBundle:Default:schedule.html.twig',
         ['items' => $items, 'nums' => $nums, 'dows' => $dows]
      );
   }
}
