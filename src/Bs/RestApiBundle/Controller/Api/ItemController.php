<?php

namespace RestApiBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use RestApiBundle\Entity\Item;
use RestApiBundle\Form\ItemType;

class ItemController extends Controller {

    /**
     * @Route("/api/items")
     * @Method({"POST"})
     */
    public function newAction(Request $request) {
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

    private function serializeItem(Item $item) {
        return array(
            'name' => $item->getName(),
            'amount' => $item->getAmount(),
        );
    }

}
