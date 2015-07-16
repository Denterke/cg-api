<?php

namespace Farpost\CatalogueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExportController extends Controller
{
    public function exportAction()
    {
        shell_exec("../app/console catalogue:export > /dev/null 2>/dev/null &");
        sleep(1);
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }
}