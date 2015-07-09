<?php

namespace Farpost\CatalogueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FarpostCatalogueBundle:Default:index.html.twig', array('name' => $name));
    }
}
