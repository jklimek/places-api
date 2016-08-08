<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp;

class MainController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Template
     * @return array
     */
    public function indexAction()
    {
        $client = new GuzzleHttp\Client();

//        $url = "http://places.klemens.ninja/places";
        $url = "http://0.0.0.0:8888/places";
        $options = [
            "location" => "54.348538,18.653228", // Default location - Neptune's Fountain
            "radius" => 2000,
            "type" => "bar", // Default type - bar
        ];
        $res = $client->request("GET", $url, ["query" => $options]);

        $responseJson = $res->getBody();
        $responseBody = GuzzleHttp\json_decode($responseJson, true);
        return ["response" => $responseBody];
    }
}
