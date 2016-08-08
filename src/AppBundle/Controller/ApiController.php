<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{

    /**
     * @Route("/photos/{photo_id}", name="photo")
     * @param Request $request
     * @return array
     */
    public function photosAction(Request $request) {
        $client = new GuzzleHttp\Client();
        $photoId = $request->get("photo_id");
        $apiKey = $this->getParameter("google_api_key");
        $url = "https://maps.googleapis.com/maps/api/place/photo";
        $options = [
            "photoreference" => $photoId,
            "maxwidth" => 200,
            "key" => $apiKey
        ];
        $res = $client->request("GET", $url, ["query" => $options]);

        $responsePhoto = $res->getBody();

        $headers = [
            'Content-Type' => 'image/png'
        ];

        return new Response($responsePhoto, 200, $headers);
    }


    /**
     * @Route("/places", name="places_default")
     * @return array
     */
    public function placesAction(Request $request) {

        $client = new GuzzleHttp\Client();
        $radius = ($request->get("radius") != null) ? $request->get("radius") : 2000;
        $type = ($request->get("type") != null) ? $request->get("type") : "bar";
        $apiKey = $this->getParameter("google_api_key");
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
        $options = [
            "location" => "54.348538,18.653228", // Default location - Neptune's Fountain
            "radius" => $radius,
            "type" => $type, // Default type - bar
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
            $photoId = $place["photos"][0]["photo_reference"];
            $place["links"]["self"] = [
                "href" => "/places/$placeId",
                "rel" => "self"
            ];
            $place["links"]["photo"] = [
                "href" => "/photos/$photoId",
                "rel" => "photo"
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
