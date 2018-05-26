<?php

namespace RestApiBundle\Tests\Controller\Api;

use RestApiBundle\Test\ApiTestCase;

class ItemControllerTest extends ApiTestCase {

    /** 
     * @test 
     */
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
    
    /** 
     * @test 
     */
    public function testGetItem() {
        $client = static::createClient();
        
        $data = array(
            'name' => 'testItemName',
            'amount' => rand(0,10)
        );
        
        $item = $this->createTestItem($data);
        
        $id = $item->getId();
        $client->request(
            'Get', 
            '/api/items/' .$id
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $responseData);
        
        $deletedItem = $this->deleteTestItem($id);
    }
    
    /** 
     * @test 
     */
    public function testListItems() {
        $client = static::createClient();
        
        $data = array(
            'name' => 'testItemName',
            'amount' => rand(0,10)
        );
        
        $item = $this->createTestItem($data);
        
        $client->request(
            'Get', 
            '/api/items'
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('items', $responseData);
        
        $deletedItem = $this->deleteTestItem($id);
    }
}
