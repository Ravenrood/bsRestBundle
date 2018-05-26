<?php

namespace RestApiBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use RestApiBundle\Entity\Item;
use RestApiBundle\Form\ItemType;
use Symfony\Component\Form\Form;

class ItemController extends Controller {

    /**
     * @Route("/api/items")
     * @Method({"POST"})
     */
    public function newAction(Request $request) {
        
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $data = $this->processForm($request, $form);
        
        $item->setName($data['name']);
        $item->setAmount($data['amount']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($item);
        $entityManager->flush();
        $serializedData = $this->serializeItem($item);
        $response = new JsonResponse($serializedData, 201);

        $location = $this->generateUrl('api_items_show', [
            'id' => $item->getId()
        ]);
        $response->headers->set('location', $location);
        return $response;
    }

    /**
     * @Route("/api/items/{id}", name="api_items_show")
     * @Method("GET")
     */
    public function showAction($id) {
        $item = $this->getDoctrine()
                ->getRepository('RestApiBundle:Item')
                ->findOneBy(array('id' => $id));

        if (!$item) {
            throw $this->createNotFoundException(sprintf(
                            'No item found with given id "%s"', $id
            ));
        }

        $data = $this->serializeItem($item);

        $response = new JsonResponse($data, 200);
        return $response;
    }

    /**
     * @Route("/api/items")
     * @Method("GET")
     */
    public function listAction() {
        $items = $this->getDoctrine()
                ->getRepository('RestApiBundle:Item')
                ->findAll();
        $data = ['items' => []];
        foreach ($items as $item) {
            $data ['items'][] = $this->serializeItem($item);
        }
        $response = new JsonResponse($data, 200);
        return $response;
    }
    
    /**
     * @Route("/api/items/{id}", name="api_items_update")
     * @Method({"PUT", "PATCH"})
     */
    public function updateAction($id, Request $request) {
        $item = $this->getDoctrine()
                ->getRepository('RestApiBundle:Item')
                ->findOneBy(array('id' => $id));

        if (!$item) {
            throw $this->createNotFoundException(sprintf(
                            'No item found with given id "%s"', $id
            ));
        }

        $form = $this->createForm(ItemType::class, $item);
        $data = $this->processForm($request, $form);
        if (!empty($data['name'])) {
            $item->setName($data['name']);
        }
        if (!empty($data['name'])) {
            $item->setAmount($data['amount']);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($item);
        $entityManager->flush();
        $serializedData = $this->serializeItem($item);
        $response = new JsonResponse($serializedData, 200);

        return $response;
    }
    
    /**
     * @Route("/api/items/{id}")
     * @Method({"DELETE"})
     */
    public function deleteAction($id, Request $request) {
        $item = $this->getDoctrine()
                ->getRepository('RestApiBundle:Item')
                ->findOneBy(array('id' => $id));

        if ($item) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
        }
        
        return new Response(null, 204);
    }

    private function processForm(Request $request, Form $form) {
        $body = $request->getContent();
        $data = json_decode($body, true);
        
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
        return $data;
    }
    
    private function serializeItem(Item $item) {
        return array(
            'name' => $item->getName(),
            'amount' => $item->getAmount(),
        );
    }

}
