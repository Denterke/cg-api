<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


use Farpost\StoreBundle\Entity\Time;
use Farpost\StoreBundle\Entity\Semester;
use Farpost\StoreBundle\Entity\Role;
use Farpost\StoreBundle\Entity\User;

class SystemController extends APIV1Controller
{
    /**
     * Returns current server timestamp
     * Added: [1.0]
     * Required: [Client 1.0]
     * @return Response
     */
    public function getTimeAction()
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        return $response->setStatusCode(200)->setContent(json_encode(
            ['timestamp' => $helper->getTimestamp()]
        ));
    }

    /** 
     * Initializate application after deploy
     * Added: [1.0]
     * @return Response
     */
    public function initAppAction()
    {
        $helper = $this->get('api_helper');
        $result = self::getApplication()->run(new StringInput('doctrine:fixtures:load --no-interaction'));
        return $helper->create404()->setStatusCode(200)->setContent($result);
    }

    /**
     * Get static file from server
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param  string $filename
     * @return Response
     */
    public function getFileAction($filename)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        $filepath = $this->getRequest()
            ->server
            ->get('DOCUMENT_ROOT') . "/static/$filename";
        if (!file_exists($filepath)) {
            return $response;
        }
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', mime_content_type($filepath));
        $response->headers->set('Content-Disposition', "attachment; filename=\"'$filename'\";");
        $response->headers->set('Content-length', filesize($filepath));
        $response->sendHeaders();
        $response->setContent(file_get_contents($filepath));
        return $response;
    }
}