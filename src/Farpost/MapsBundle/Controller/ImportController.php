<?php

namespace Farpost\MapsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImportController extends Controller
{
    const GRAPH_DUMPS_DIR = 'uploads/graph_dumps/';

    public function graphAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $file = $request->files->get('file');
            if ($file) {
                $newFileName = uniqid('', true);
                $newPathname = join('/', [self::GRAPH_DUMPS_DIR, $newFileName]);
                $file->move(self::GRAPH_DUMPS_DIR, $newFileName);
                shell_exec("../app/console graph:import $newPathname > /dev/null 2>/dev/null &");
            }
        }
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }
}