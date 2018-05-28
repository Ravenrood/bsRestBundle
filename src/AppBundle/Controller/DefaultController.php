<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use RestApiBundle\Entity\Item;
use RestApiBundle\Form\ItemType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class DefaultController extends Controller {

    /**
     * @Route("/", name="list_items")
     */
    public function indexAction(Request $request) {
        $apiCall = $request->query->get('apicall');
        $delete = $request->query->get('delete');
        $params = array();
        $page = array();
        $method = 'GET';
        $query = array ();
        $url = $this->getUrl('api_items_collection');
        if(!empty ($apiCall)) {
            $params = explode('?', base64_decode($apiCall));
            $page = explode ('=', $params[1]);
            $query = array ($page[0] => $page[1]);
            $url = $this->getUrl('api_items_collection');
        }
        if(!empty ($delete)) {
            $method = 'DELETE';
            $params = base64_decode($delete);
            $url = $this->getUrl('list_items') . $params;
            $result = $this->getData ($method, $url , $query);
            $method = 'GET';
            $url = $this->getUrl('api_items_collection');
        }
        
        $urlNA = $this->getUrl('list_items') . 'api/items?filter=0';
        $resultNA = $this->getData ($method, $urlNA , $query);
        $dataNA = $this->processData($resultNA);

        $result = $this->getData ($method, $url , $query);
        $dataToDisplay = $this->processData($result);
        
        return $this->render('@App\Default\index.html.twig', array (
            'items' => $dataToDisplay['items'],
            '_links' => $dataToDisplay['_links'],
            'actualUrl' =>  $this->getUrl('list_items'),
            'notAvailable' => $dataNA['items']
        ));
    }

    /**
     * @Route("/list")
     */
    public function listAction(Request $request) {
        $item = new Item();
        $item->setName('nazwa');
        $item->setAmount(rand(0, 10));

        $form = $this->createForm(ItemType::class, $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            
        }

        return $this->render('@AppBundle\Default\index.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    private function getData($method, $url, $data = array()) {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
    
    private function getUrl ($routeName) {
        $link = $this->generateUrl(
            $routeName, 
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $link;
    }
    
    private function processData ($jsonResponse) {
        $data = json_decode($jsonResponse, true);
        if (!empty($data['_links'])) {
            $data['_links'] = array_map('base64_encode', $data['_links']);
        }
        foreach ($data['items'] as $key => $item) {
            $data['items'][$key]['modify'] = base64_encode('api/items/' . $item['id']);
        } 
        return $data;
    }

}
