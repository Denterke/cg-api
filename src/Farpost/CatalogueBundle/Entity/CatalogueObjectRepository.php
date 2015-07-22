<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 20/07/15
 * Time: 15:05
 */

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CatalogueObjectRepository extends EntityRepository
{

    public function serialize($objects)
    {
        $items = [];
        foreach($objects as $object) {
            $items[] = [
                'id' => $object->getId(),
                'name' => $object->getName(),
                'logo' => $object->getLogoThumbnail()
                    ? $object->getLogoThumbnail()->getWebPath()
                    : null
            ];
        }
        return $items;
    }

    public function searchByName($query)
    {
        return $this->_em->createQueryBuilder()
            ->select('o')
            ->from('FarpostCatalogueBundle:CatalogueObject', 'o')
            ->where('LOWER(o.name) like LOWER(:query)')
            ->setParameter('query', "%$query%")
            ->getQuery()
            ->getResult()
        ;
    }

    public function attachObjectToNode($objectId, $nodeId)
    {
        $object = $this->findOneBy(['id' => $objectId]);
        if (!$object) {
            return [
                'success' => false,
                'status' => 'ObjectNotFound'
            ];
        }
        $node = $this->_em->getRepository('FarpostMapsBundle:Node')->findOneBy(['id' => $nodeId]);
        if (!$node) {
            return [
                'success' => false,
                'status' => 'NodeNotFound'
            ];
        }
        if ($object->getNode() && $object->getNode()->getId() === $node->getId()) {
            return [
                'success' => true,
                'status' => 'ObjectAlreadyAttached'
            ];
        }
        $object->setNode($node);
        $this->_em->flush();
        return [
            'success' => true,
            'status' => 'ObjectAttached'
        ];
    }

    public function detachObject($objectId)
    {
        $object = $this->findOneBy(['id' => $objectId]);
        if (!$object) {
            return [
                'success' => false,
                'status' => 'ObjectNotFound'
            ];
        }
        if (!$object->getNode()) {
            return [
                'success' => true,
                'status' => 'AlreadyDetached'
            ];
        }
        $object->setNode(null);
        $this->_em->flush();
        return [
            'success' => true,
            'status' => 'ObjectDetached'
        ];
    }
}