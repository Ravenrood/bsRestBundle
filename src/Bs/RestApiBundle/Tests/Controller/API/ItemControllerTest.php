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
            'amount' => rand(0, 10)
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
        $this->deleteTestItem($id);
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
        
        $this->deleteTestItem($id);
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
        
        $this->deleteTestItem($id);
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
        
        $this->deleteTestItem($id);
    }
    
    /** 
     * @test 
     */
    public function testDeleteItem() {
        $client = static::createClient();
        
        $data = array(
            'name' => 'testItemName',
            'amount' => rand(0,10)
        );
        
        $item = $this->createTestItem($data);
        
        $id = $item->getId();
        
        $client->request(
            'DELETE', 
            '/api/items/' . $id
        );
        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
    }
    
    /** 
     * @test 
     */
    public function testPatchItem() {
        $client = static::createClient();
        
        $data = array(
            'name' => 'testItemName',
            'amount' => rand(0,10)
        );
        
        $item = $this->createTestItem($data);
        
        $id = $item->getId();
        
        $dataChanged = array(
            'amount' => rand(0,10)
        );
        
        $client->request(
            'PATCH', 
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
        
        $this->deleteTestItem($id);
    }
    
    /** 
     * @test 
     */
    public function testValidationErrors() {
        $client = static::createClient();
        
        $data = array(
            'amount' => rand(0, 10)
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
        $this->assertEquals(400, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('type', $responseData);
        $this->assertEquals('application/problem+json', $response->headers->get('CONTENT_TYPE'));
    }
    
    /** 
     * @test 
     */
    public function testInvalidJson() {
        $client = static::createClient();
        
        $invalidJson = '{ "amount" : 1 }';
        
        $client->request(
            'POST', 
            '/api/items', 
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($invalidJson)
        );
        
        $response = $client->getResponse();        
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    /** 
     * @test 
     */
    public function testNotFoundException() {
        $client = static::createClient();
        
        $client->request(
            'GET', 
            '/api/itms'
        );
        $response = $client->getResponse(); 
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->headers->get('CONTENT_TYPE'));
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('title', $responseData);
        $this->assertContains('Not Found', $responseData);
    }
}
