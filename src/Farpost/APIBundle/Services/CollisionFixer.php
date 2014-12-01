<?php
namespace Farpost\APIBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Farpost\StoreBundle\Entity\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollisionFixer
{
    private $doctrine;
    private $helper;
    const NO_ERROR = 0;
    const BAD_REQUEST = -1;
    const NOT_FOUND = -2;
    const DISCONFIG = -3;

    public function __construct($doctrine, $api_helper)
    {
        $this->doctrine = $doctrine;
        $this->helper = $api_helper;
    }


    /**
     * Fix client group Id and server group Id disconfig by client alias MD5
     * Added: [2.0]
     * @param integer cliId
     * @param string cliMD5
     * @param integer cliTS
     * @param &Array: [update, groupId] result
     * @return integer errorCode
    */
    public function getGroupFix($cliId, $cliMD5, $cliTS, &$result)
    {
        $wipeTS = $this->helper->getLastWipeTS();
        if ($wipeTS >= $cliTS) {
            if (!$cliMD5 || !$cliId) {
                return self::BAD_REQUEST;
            }
            $realGroupId = $this->doctrine
                ->getManager()
                ->getRepository('FarpostStoreBundle:Group')
                ->getGroupByMD5($cliMD5);
            if (!$realGroupId) {
                $result = [
                    "update" => true,
                    "group_id" => null
                ];
                return self::NOT_FOUND;
            } else if ($realGroupId != $cliId) {
                $result = [
                    "update" => true,
                    "group_id" => $realGroupId
                ];
                return self::DISCONFIG;
            } else {
                $result = [
                    "update" => false,
                    "group_id" => $realGroupId
                ];
                return self::NO_ERROR;
            }
        } else {
            $result = [
                "update" => false,
                "group_id" => $cliId
            ];
            return self::NO_ERROR;
        }
    }

    /**
     * Usual check for sync status of group request params,
     * Added: [2.0]
     * @param  Request $request
     * @param  &Response  $response
     * @param  &Array  $result
     * @return boolean returns true if fatal error in sync found, false otherwise
     */
    public function usualCheck(Request $request, &$response, &$result)
    {
        $response = $this->helper->create404();
        $errCode = $this->getGroupFix(
            $request->query->getInt('group', 0),
            $request->query->get('alias', ''),
            $request->query->get('t', 1),
            $result
        );
        $result['timestamp'] = $this->helper->getTimestamp();
        if ($errCode == self::BAD_REQUEST) {
            return true;
        }
        if ($errCode == self::NOT_FOUND) {
            $response->setContent(json_encode($result))->setStatusCode(200);
            return true;
        }
        return false;
    }

    public function wipeCheck(Request $request, &$response, &$result)
    {
        $response = $this->helper->create404();
        $cliTS = $request->query->get('t', 1);
        $wipeTS = $this->helper->getLastWipeTS();
        if ($wipeTS >= $cliTS) {
            $result = [
                "update" => true,
                "timestamp" => $wipeTS
            ];
        } else {
            $result = [
                "update" => false,
                "timestamp" => $cliTS
            ];
        }
        return true;
    }
}
