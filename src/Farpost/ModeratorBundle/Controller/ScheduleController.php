<?php

namespace Farpost\ModeratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScheduleController extends Controller
{
    public function indexAction()
    {
        return $this->render('FarpostModeratorBundle:Schedule:app.html.twig');
    }
}
