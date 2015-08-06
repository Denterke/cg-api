<?php

namespace Farpost\CatalogueBundle\Controller;

use Farpost\CatalogueBundle\Serializer\CategorySerializer;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EditorController extends CoreController
{
    public function indexAction(Request $request)
    {
        return $this->render('FarpostCatalogueBundle:Admin:editor.html.twig', [
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks')
        ]);
    }

    public function getGraphAction(Request $request)
    {
        $categoryRepository = $this->getDoctrine()->getManager()
            ->getRepository('FarpostCatalogueBundle:CatalogueCategory')
        ;

        $categories = $categoryRepository->findAll();
        $nodes = $this->get('farpost_catalogue.serializer.category')->serialize($categories, CategorySerializer::EDITOR_CARD);

        return new JsonResponse(['nodes' => $nodes]);
    }
}
