<?php

namespace RestApiBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use RestApiBundle\Entity\Item;
//use AppBundle\Entity\Programmer;
//use AppBundle\Entity\User;
//use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ApiTestCase extends WebTestCase
{
    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }
    
    protected function createTestItem(array $data)
    {
        $item = new Item();
        
        $item->setName($data['name']);
        $item->setAmount($data['amount']);


        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();

        return $item;
    }
    
    protected function deleteTestItem( $id )
    {
        $repository = $this->getEntityManager()->getRepository(Item::class);
        $item = $repository->find($id);
        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->flush();
        return $item;
    }

    protected function getEntityManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }
}
