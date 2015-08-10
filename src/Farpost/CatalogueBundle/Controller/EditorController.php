<?php

namespace Farpost\CatalogueBundle\Controller;

use Farpost\CatalogueBundle\Serializer\CategoryEdgeSerializer;
use Farpost\CatalogueBundle\Serializer\CategoryNodeEdgeSerializer;
use Farpost\CatalogueBundle\Serializer\CategorySerializer;
use Farpost\CatalogueBundle\Serializer\ObjectSerializer;
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
        $categoryRepository = $this->getDoctrine()->getManager()->getRepository('FarpostCatalogueBundle:CatalogueCategory');
        $categoryEdgeRepository = $this->getDoctrine()->getManager()->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge');
//        $objectRepository = $this->getDoctrine()->getManager()->getRepository('FarpostCatalogueBundle:CatalogueObject');
//        $objectEdgeRepository = $this->getDoctrine()->getManager()->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');

        $categoriesEdges = $categoryEdgeRepository->findAll();
        $edges = $this->get('farpost_catalogue.serializer.category_edge')->serialize($categoriesEdges, CategoryEdgeSerializer::EDITOR_CARD);

        $categories = $categoryRepository->findAll();
        $nodes = $this->get('farpost_catalogue.serializer.category')->serialize($categories, CategorySerializer::EDITOR_CARD);

//        $objects =$objectRepository->findAll();
//        $nodes = array_merge($nodes, $this->get('farpost_catalogue.serializer.object')->serialize($objects, ObjectSerializer::EDITOR_CARD));
//
//        $objectsEdges = $objectEdgeRepository->findAll();
//        $edges = array_merge($edges, $this->get('farpost_catalogue.serializer.category_node_edge')->serialize($objectsEdges, ObjectSerializer::EDITOR_CARD));

        return new JsonResponse([
            'nodes' => $nodes,
            'edges' => $edges
        ]);
    }

    public function getCategoryObjectsAction(Request $request)
    {
        $id = intval($request->query->get('id', 0));
        $objectRepository = $this->getDoctrine()->getManager()->getRepository('FarpostCatalogueBundle:CatalogueObject');
        $categoryNodeEdgeRepository = $this->getDoctrine()->getManager()->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');

        $objects = $objectRepository->findByCategory($id);
        $edges = [];
        foreach($objects as $object) {
            foreach($object->getCategories() as $category) {
                $edges[] = $category;
            }
        }
        $objects = $this->get('farpost_catalogue.serializer.object')->serialize($objects, ObjectSerializer::EDITOR_CARD);
        $edges = $this->get('farpost_catalogue.serializer.category_node_edge')->serialize($edges, CategoryNodeEdgeSerializer::EDITOR_CARD);

        return new JsonResponse([
            'nodes' => $objects,
            'edges' => $edges
        ]);
    }
}
