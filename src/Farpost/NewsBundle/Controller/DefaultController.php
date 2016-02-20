<?php

namespace Farpost\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FarpostNewsBundle:Default:index.html.twig', array('name' => $name));
    }
}
