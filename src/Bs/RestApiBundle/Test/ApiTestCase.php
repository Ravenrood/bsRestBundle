<?php

namespace RestApiBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTestCase extends KernelTestCase
{
    public static function setUpBeforeClass()
    {
        self::bootKernel();
    }  
    
    protected function setUp()
    {
        $this->purgeDatabase();
    }
    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
    }
    
    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }

    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getService('doctrine')->getManager());
        $purger->purge();
    }

}
