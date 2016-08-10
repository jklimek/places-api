<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Template
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $parameters = [
            "type" => $request->get('type'),
            "location" => $request->get('location'),
        ];


        $url = "http://places.klemens.ninja/places";
//        $url = "http://0.0.0.0:8888/places";
        $options = [
            "location" => "54.348538,18.653228", // Default location - Neptune's Fountain
            "radius" => 2000,
            "type" => $parameters["type"], // Default type - bar
        ];
        $responseBody = $this->get('api.requests.service')->makeJsonRequest($url, $options);
        return ["response" => $responseBody];
    }
}
