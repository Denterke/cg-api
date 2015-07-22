<?php

namespace Farpost\CatalogueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FarpostCatalogueBundle:Default:index.html.twig', array('name' => $name));
    }

    public function getObjectsAction(Request $request)
    {
        $search = $request->query->get('search', '');
        $objectRepository = $this->get('doctrine')
            ->getManager()
            ->getRepository('FarpostCatalogueBundle:CatalogueObject')
        ;

        $objects = $objectRepository->serialize(
            $objectRepository->searchByName($search)
        );

        $payload = [
            'objects' => $objects
        ];

        return new JsonResponse($payload, 200);
    }

    public function attachObjectAction(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(null, 405);
        }

        $nodeId = intval($request->request->get('nodeId'));
        $objectId = intval($request->request->get('objectId'));

        $result = $this->get('doctrine')
            ->getManager()
            ->getRepository('FarpostCatalogueBundle:CatalogueObject')
            ->attachObjectToNode($objectId, $nodeId)
        ;
        $payload = $result;

        return new JsonResponse($payload, 200);
    }

    public function detachObjectAction(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(null, 405);
        }

        $objectId = intval($request->request->get('objectId'));

        $result = $this->get('doctrine')
            ->getManager()
            ->getRepository('FarpostCatalogueBundle:CatalogueObject')
            ->detachObject($objectId)
        ;

        $payload = $result;

        return new JsonResponse($payload, 200);
    }
}
