<?php

namespace Farpost\POIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FarpostPOIBundle:Default:index.html.twig', array('name' => $name));
    }
}
