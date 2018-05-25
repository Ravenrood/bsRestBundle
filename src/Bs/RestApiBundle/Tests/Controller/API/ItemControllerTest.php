<?php

namespace RestApiBundle\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use RestApiBundle\Test\ApiTestCase;

class ItemControllerTest extends ApiTestCase {
//class ItemControllerTest extends TestCase {

    /** @test */
    public function testPOST() {
        $client = static::createClient();

        $name = 'losowa nazwa ' . rand(0, 999);
        
        $data = array(
            'name' => $name,
            'amount' => rand(0, 999)
        );
        
        $client->request(
            'POST', 
            '/api/items', 
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );
        
        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Location'));
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals($name, $responseData['name']);
    }

}
