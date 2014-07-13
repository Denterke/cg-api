<?php

namespace Farpost\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Farpost\TestBundle\Entity\SimpleForm;
use Symfony\Component\HttpFoundation\Request;
use Farpost\StoreBundle\Entity\School;

class DefaultController extends Controller
{
   public function indexAction(Request $request)
   {
      $session = $request->getSession();
      $num = $session->get('num');
      if (empty($num)) {
         $num = 0;
      }
      $num++;
      $session->set('num', $num);
      $tmp = "empty";
      $simpleForm = new SimpleForm();
      $form = $this->createFormBuilder($simpleForm)
         ->add('stype', 'text')
         ->add('save', 'submit')
         ->getForm();
      $form->handleRequest($request);

      if ($form->isValid()) {
         $tmp = $simpleForm->getStype();
         $em = $this->getDoctrine()->getManager();
         // $school = new School();
         // $school->setAlias($tmp);
         // $em->persist($school);
         // $em->flush();
         return $this->render(
            'FarpostTestBundle:Default:index.html.twig',
            ['num' => $num, 'tmp' => $tmp, 'form' => $form->createView()]
         );
      }
     return $this->render(
         'FarpostTestBundle:Default:index.html.twig',
         ['num' => $num, 'tmp' => $tmp, 'form' => $form->createView()]
      );
   }
}
