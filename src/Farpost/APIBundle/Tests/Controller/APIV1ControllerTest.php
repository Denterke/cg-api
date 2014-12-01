<?php

namespace Farpost\APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class APIV1ControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/method/login');
        $content = json_decode($client->getResponse()->getContent(), true);
        $keys = [
        	'secret', 'first_name', 'last_name',
        	'middle_name', 'role_id', 'group_id', 
        	'study_type_id', 'school_id', 'specialization_id'
        ];
        foreach ($keys as &$key) {
        	$this->assertArrayHasKey($key, $content);
    	}
    }

    public function testList()
    {
    	$client = static::createClient();
    	
    	$crawler = $client->request('GET', '/method/building.list.json');
    	$content = json_decode($client->getResponse()->getContent(), true);
    }
}