<?php
namespace Farpost\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{
    public function ssConvertAction()
    {
        return new JsonResponse([
            'count' => $this->get('session')->get('ss_count'),
            'current' => $this->get('session')->get('ss_current')
        ]);
    }
}
