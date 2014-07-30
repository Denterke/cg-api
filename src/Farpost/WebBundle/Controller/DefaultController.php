<?php

namespace Farpost\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FarpostWebBundle:Admin:login.html.twig');
    }
}
