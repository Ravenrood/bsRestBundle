<?php

namespace RestApiBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use RestApiBundle\Entity\Item;
use RestApiBundle\Form\ItemType;
use Symfony\Component\Form\Form;
use RestApiBundle\Api\ApiProblem;
use RestApiBundle\Api\ApiProblemException;

class ItemController extends Controller {

    /**
     * @Route("/api/items")
     * @Method({"POST"})
     */
    public function newAction(Request $request) {
        
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $data = $this->processForm($request, $form);
        
        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }
        
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
     * @Route("/api/items", name="api_items_collection")
     * @Method("GET")
     */
    public function listAction(Request $request) {
        
        $page = $request->query->get('page', 1);
        
        $qb = $this->getDoctrine()
                ->getRepository('RestApiBundle:Item')
                ->findAllQueryBuilder();
        
        $paginatedCollection = $this->get('pagination_factory')->createCollection($qb, $request, 'api_items_collection');
       
        $response = new JsonResponse($paginatedCollection, 200);
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
        if (!$form->isValid()) {
             $this->throwApiProblemValidationException($form);
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
        if (null === $data) {
            $apiProblem = new ApiProblem(
                    400, 
                    ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
                    );
            throw new ApiProblemException(
                    $apiProblem
            );
        }
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

    private function getErrorsFromForm(Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
   
    private function throwApiProblemValidationException(Form $form)    {       
        $errors = $this->getErrorsFromForm($form);
        
        $apiProblem = new ApiProblem(
                400, 
                ApiProblem::TYPE_VALIADTION_ERROR
        );
        $apiProblem->set ('error', $errors);
        throw new ApiProblemException($apiProblem);        
    }  

}
