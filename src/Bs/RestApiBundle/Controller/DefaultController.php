<?php

namespace RestApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use RestApiBundle\Entity\Item;
use RestApiBundle\Form\ItemType;

class DefaultController extends Controller
{
    /**
     * @Route("/editform")
     */
    public function indexAction(Request $request)
    {
        $item = new Item();
        $item->setName('nazwa');
        $item->setAmount(rand(0,10));
        
        $form = $this->createForm(ItemType::class, $item);    
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            
        }
        
        return $this->render('@RestApi\Default\index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
