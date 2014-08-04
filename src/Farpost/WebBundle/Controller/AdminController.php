<?php

namespace Farpost\WebBundle\Controller;

use Farpost\WebBundle\Form\SchoolType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{

   private function _getSchoolRepository()
   {
      return $this->getDoctrine()->getRepository('FarpostStoreBundle:School');
   }

   public function deleteAction($id)
   {
      echo $id;
      return $this->redirect($this->generateUrl('admin_index'));
   }

   public function indexAction()
   {
      return $this->render('FarpostWebBundle:Admin:login.html.twig');
   }

   public function scheduleAction()
   {
      return $this->render('FarpostWebBundle:Admin:schedule.html.twig');
   }

   public function schoolsAction(Request $request)
   {
      $form = $this->createForm(new SchoolType(), null);
      $form->handleRequest($request);
      if ($form->isValid()) {
         $em = $this->getDoctrine()->getManager();
         $em->merge($form->getData());
         $em->flush();
         return $this->redirect($this->generateUrl('admin_structure'));
      }
      return $this->render('FarpostWebBundle:Admin:schools.html.twig', [
         'schools' => $this->_getSchoolRepository()->findBy([], ['alias' => 'asc']),
         'school_edit_form' => $form->createView()
      ]);
   }

   public function studyAction()
   {
      return $this->render('FarpostWebBundle:Admin:study.html.twig');
   }
}
