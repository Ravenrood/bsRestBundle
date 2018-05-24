<?php

namespace RestApiBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use RestApiBundle\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use RestApiBundle\Form\ItemType;

class ItemController extends Controller
{
    /**
     * @Route("/api/items")
     * @Method({"POST"})
     */
    public function newAction(Request $request) 
    {
        $body = $request->getContent();
        //zakładamy że przyszedł json
        $data = json_decode($body, true);
        
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item); 
        $form->submit($data);
        
        $item->setName($data['name']);
        $item->setAmount($data['amount']);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($item);
        $entityManager->flush();        
        return new Response($body);
    }
}
