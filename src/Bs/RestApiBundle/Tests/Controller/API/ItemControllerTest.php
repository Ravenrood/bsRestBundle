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
        $location = explode('/', $response->headers->get('location'));
        $id = (int) $location[3];
        $deletedItem = $this->deleteTestItem($id);
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
            'GET', 
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
        $id = $item->getId();
        
        $client->request(
            'GET', 
            '/api/items'
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('items', $responseData);
        
        $deletedItem = $this->deleteTestItem($id);
    }
    
    /** 
     * @test 
     */
    public function testPutItem() {
        $client = static::createClient();
        
        $data = array(
            'name' => 'testItemName',
            'amount' => rand(0,10)
        );
        
        $item = $this->createTestItem($data);
        
        $id = $item->getId();
        
        $dataChanged = array(
            'name' => 'testItemNameChanged',
            'amount' => rand(0,10)
        );
        
        $client->request(
            'PUT', 
            '/api/items/' . $id, 
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($dataChanged)
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $responseData);
        
        $deletedItem = $this->deleteTestItem($id);
    }
}
