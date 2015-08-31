<?php

namespace Farpost\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_RichText;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;
use Farpost\StoreBundle\Services\ExcelGenerator;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FarpostStoreBundle:Default:index.html.twig', array('name' => $name));
    }

    public function generateExcelAction()
    {
        $request = Request::createFromGlobals();

        $opt_groups = array();
        $opts = $request->query->get('options');
        foreach($request->query->get('groups') as $key => $value){
            $opt_groups[$key] = array(
                // Временный фикс, пока нет поддержки на фронте
                "type" => "sub",//$opts[$key],
                "group" => $value
            );
        }
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="fefu_s.xls');
        $response->sendHeaders();

        $generator = new ExcelGenerator(__DIR__ . "/../Resources/schedule.xls", $this->getDoctrine());
        $generator->export($opt_groups, 'php://output');

        exit();
    }
}
