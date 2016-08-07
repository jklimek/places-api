<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp;

class MainController extends Controller
{

    /**
     * @Route("/places", name="places_default")
     * @return array
     */
    public function placesAction() {

        $client = new GuzzleHttp\Client();

        $apiKey = $this->getParameter("google_api_key");
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
        $options = [
            "location" => "54.348538,18.653228", // Default location - Neptune's Fountain
            "radius" => 2000,
            "type" => "bar", // Default type - bar
            "key" => $apiKey
        ];
        $res = $client->request("GET", $url, ["query" => $options]);

        $responseJson = $res->getBody();
        $responseBody = GuzzleHttp\json_decode($responseJson, true);
        $places = [];
        //TODO Make sure responseBody is valid
        foreach ($responseBody["results"] as $place) {
            // /places
            $placeId = $place["place_id"];
            $place["links"] = [
                "href" => "/places/$placeId",
                "rel" => "self"
            ];
            $places[] = $place;
        }

        return new JsonResponse($places);
    }


    /**
     * @Route("/places/{place_id}", name="place_details")
     * @param Request $request
     * @return array
     */
    public function placeDetailsAction(Request $request) {
        $client = new GuzzleHttp\Client();
        $placeId = $request->get("place_id");
        $apiKey = $this->getParameter("google_api_key");
        $url = "https://maps.googleapis.com/maps/api/place/details/json";
        $options = [
            "placeid" => $placeId,
            "key" => $apiKey
        ];
        $res = $client->request("GET", $url, ["query" => $options]);

        $responseJson = $res->getBody();
        $responseBody = GuzzleHttp\json_decode($responseJson, true);
//dump($responseBody);
        return new JsonResponse($responseBody["result"]);
    }
}
