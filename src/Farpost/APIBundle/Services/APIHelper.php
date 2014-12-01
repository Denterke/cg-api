<?php
namespace Farpost\APIBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Farpost\StoreBundle\Entity\Group;
use Symfony\Component\HttpFoundation\Response;

class APIHelper
{
    /**
     * returns current timestamp
     * @return timestamp
     */
    public function getTimestamp()
    {
        $dt = new \DateTime;
        return $dt->getTimestamp();
    }

    /**
     * Creates "Not found" response
     * Added: [2.0]
     * @return Response
     */
    public function create404()
    {
        return new Response(
            'Not found',
            404,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Creates "Not implemented yet" response
     * Added: [2.0]
     * @return Response
     */
    public function notImplementedYet()
    {
        return new Response(
            'This method not implemented yet',
            501,
            ['Content-Type' => 'application/json']
        );
    }
}
