<?php

namespace Farpost\CatalogueBundle\Controller;

use Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge;
use Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge;
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

    public function createEdgeAction(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(null, 405);
        }
        $sourceId = $request->request->get('sourceId', null);
        $targetId = $request->request->get('targetId', null);
        $targetType = $request->request->get('targetType', null);

        if (!$sourceId || !$targetId || ($targetType !== 'object' && $targetType !== 'category')) {
            return new JsonResponse(null, 400);
        }

        //prevent cycles in category graph
        if ($targetType === 'category' && $sourceId === $targetId) {
            return new JsonResponse(null, 400);
        }

        $em = $this->getDoctrine()->getManager();

        $categoryRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategory');
        $objectRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueObject');
        $categoriesEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge');
        $categoryNodeEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');

        $source = $categoryRepository->findOneBy(['id' => $sourceId]);

        if (!$source) {
            return new JsonResponse(null, 404);
        }

        switch ($targetType) {
            case 'category':
                $child = $categoryRepository->findOneBy(['id' => $targetId]);
                if (!$child) {
                    return new JsonResponse(null, 404);
                }
                $existedEdge = $categoriesEdgeRepository->findOneBy(['parent' => $sourceId, 'child' => $targetId]);
                if ($existedEdge) {
                    return new JsonResponse(null, 400);
                }
                $edge = new CatalogueCategoryEdge();
                $edge->setChild($child)
                    ->setParent($source)
                ;
                $em->persist($edge);
                $em->flush();
                $edge = $this->get('farpost_catalogue.serializer.category_edge')->serializeOne($edge, CategorySerializer::EDITOR_CARD);
                break;
            case 'object':
                $object = $objectRepository->findOneBy(['id' => $targetId]);
                if (!$object) {
                    return new JsonResponse(null, 404);
                }
                $exictedEdge = $categoryNodeEdgeRepository->findOneBy(['object' => $targetId, 'category' => $sourceId]);
                if ($exictedEdge) {
                    return new JsonResponse(null, 400);
                }
                $edge=  new CatalogueCategoryObjectEdge();
                $edge->setCategory($source)
                    ->setObject($object)
                ;
                $em->persist($edge);
                $em->flush();
                $edge = $this->get('farpost_catalogue.serializer.category_node_edge')->serializeOne($edge, CategoryNodeEdgeSerializer::EDITOR_CARD);
                break;
            default:
                return new JsonResponse(null, 400);
        }

        return new JsonResponse($edge, 200);
    }

    public function deleteEdgeAction(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(null, 405);
        }
        $id = $request->request->get('id', null);
        $type = $request->request->get('type', null);
        if (!$id || !$type || ($type !== 'categoryedge' && $type !== 'categorynodeedge')) {
            return new JsonResponse(null, 400);
        }
        $em = $this->getDoctrine()->getManager();
        switch ($type) {
            case 'categoryedge':
                $categoryEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge');
                $edge = $categoryEdgeRepository->findOneBy(['id' => $id]);
                break;
            case 'categorynodeedge':
                $categoryObjectEdgeRepository = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryObjectEdge');
                $edge = $categoryObjectEdgeRepository->findOneBy(['id' => $id]);
                break;
            default:
                return new JsonResponse(null, 400);
        }
        if (!$edge) {
            return new JsonResponse(null, 200);
        }
        $em->remove($edge);
        $em->flush();

        return new JsonResponse(null, 200);
    }

    public function revertEdgeAction(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(null, 405);
        }
        $id = $request->request->get('id', null);
        if (!$id) {
            return new JsonResponse(null, 400);
        }
        $em = $this->getDoctrine()->getManager();
        $edge = $em->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge')->findOneBy(['id' => $id]);
        if (!$edge) {
            return new JsonResponse(null, 404);
        }
        $child = $edge->getParent();
        $parent = $edge->getChild();
        $edge->setChild($child)
            ->setParent($parent);
        $em->flush();

        return new JsonResponse($this->get('farpost_catalogue.serializer.category_edge')->serializeOne($edge, CategoryEdgeSerializer::EDITOR_CARD), 200);
    }
}
