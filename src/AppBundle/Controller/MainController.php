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
            "radius" => $request->get('type') ?? 2000,
            "location" => $request->get('location') ?? "54.348538,18.653228",
        ];


        $url = "http://places.klemens.ninja/places";
//        $url = "http://0.0.0.0:8888/places";
        $options = [
            "location" => str_replace("%2C",",",$parameters["location"]), // Default location - Neptune's Fountain
            "radius" => 2000,
            "type" => $parameters["type"], // Default type - bar
        ];
        $responseBody = $this->get('api.requests.service')->makeJsonRequest($url, $options);
        return ["response" => $responseBody, "type" => $parameters["type"], "location" => $parameters["location"]];
    }

    /**
     * @Route("/{placeId}", name="place_details")
     * @Template
     * @param $placeId
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function placeDetailsAction($placeId, Request $request)
    {
        $parameters = [
            "type" => $request->get('type'),
            "location" => $request->get('location') ?? "54.348538,18.653228",
        ];


        $url = "http://places.klemens.ninja/places/".$placeId;
//        $url = "http://0.0.0.0:8888/places";
        $options = [
            "location" => str_replace("%2C",",",$parameters["location"]), // Default location - Neptune's Fountain
            "radius" => 2000,
            "type" => $parameters["type"], // Default type - bar
        ];
        $responseBody = $this->get('api.requests.service')->makeJsonRequest($url, $options);

        dump($responseBody);

        return ["place" => $responseBody, "key" => $this->getParameter("google_api_key")];
    }
}
