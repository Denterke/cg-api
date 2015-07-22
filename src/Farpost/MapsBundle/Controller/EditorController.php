<?php

namespace Farpost\MapsBundle\Controller;

use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EditorController extends CoreController
{
    public function indexAction(Request $request)
    {
        return $this->render('FarpostMapsBundle:Admin:maps.html.twig', [
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks')
        ]);
    }

    public function graphAction(Request $request)
    {
        $level = intval($request->query->get('level', 1));
        $nodes = $this->get('doctrine')->getManager()->getRepository('FarpostMapsBundle:Node')
            ->getNodesForLevel($level);
        $payload = [
            'vertices' => $nodes
        ];
        return new JsonResponse($payload, 200);
    }

    public function getObjectsAction(Request $request)
    {
        $nodeId = intval($request->query->get('node', -1));
        if ($nodeId !== -1) {
            $objectRepository = $this->get('doctrine')->getManager()->getRepository('FarpostCatalogueBundle:CatalogueObject');
            $objects = $objectRepository->serialize(
                $objectRepository->findBy(['node' => $nodeId])
            );
        } else {
            $objects = [];
        }
        $payload = [
            'objects' => $objects
        ];
        return new JsonResponse($payload, 200);
    }
}