<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp;

/**
 * Rest controller for places
 */

class ApiController extends Controller
{

    /**
     * @Route("/places", name="places_default")
     * @param Request $request
     * @return array
     */
    public function placesAction(Request $request) {

        try {
            // Check if http method is valid (accepting only GET for this resource)
            if(!$request->isMethod('GET')) {
                throw new \Exception("Method not allowed. This resource is handled via GET method.", 405);
            }

            $client = new GuzzleHttp\Client();
            // Parameters
            $radius = $request->get("radius") ?? 2000; // Default radius - 2000m
            $rankBy = $request->get("rankby") ?? "prominence"; // Default rank by prominence
            $type = $request->get("type") ?? "bar"; // Default type - bar
            $location = $request->get("location") ?? "54.348538,18.653228"; // Default location - Neptune's Fountain
            $nextPageToken = $request->get("next_page_token") ?? null; // Next page token is not set as default - used to paginate
            $nameSearch = $request->get("name") ?? null; // Query for a name search
            $sort = $request->get("sort") ?? null; // Sorting parameters
            $apiKey = $this->getParameter("google_api_key");
    //        $apiKey = $request->get("key");

            $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
            $options = [
                "location" => $location,
                "radius" => $radius,
                "type" => $type,
                "rankby" => $rankBy,
                "key" => $apiKey
            ];
            // Handling ptional parameters
            if ($nextPageToken) {
                $options["pagetoken"] = $nextPageToken;
            }
            if ($nameSearch) {
                $options["name"] = $nameSearch;
            }
            // Google Places API restriction - rankby=distance must be used without radius parameter
            if ($rankBy == "distance") {
                unset($options["radius"]);
            }
            $res = $client->request("GET", $url, ["query" => $options]);


            $responseJson = $res->getBody();
            $responseBody = GuzzleHttp\json_decode($responseJson, true);
            if ($responseBody["status"] != "OK") {
                throw new \Exception($responseBody["status"], 400);
            }
            $places = [];
            // Get new next_page_token
            $nextPageToken = $responseBody["next_page_token"] ?? null;

            // Building response body
            //TODO Make sure responseBody is valid
            foreach ($responseBody["results"] as $responsePlace) {
                $place = [];
                if (!isset($responsePlace["place_id"])) {
                    continue;
                } else {
                    $placeId = $responsePlace["place_id"];
                }
                if (isset($responsePlace["photos"][0]["photo_reference"])) {
                    // Get first photo reference
                    $photoId = $responsePlace["photos"][0]["photo_reference"];

                    // Additional HATEOAS photo link
                    $links["photo"] = [
                        "href" => "/photos/$photoId",
                        "rel" => "photo"
                    ];
                }

                $place["name"] = $responsePlace["name"];
                $place["place_id"] = $placeId;
                $place["rating"] = $responsePlace["rating"] ?? null;
                $place["location"] = $responsePlace["geometry"]["location"] ?? null;
                // To calculate distance from user to a place we need to use same format for distances
                // lat,long -> 54.348538,18.653228
                if (!is_null($location) && $place["location"]) {
                    $place["distance"] = $this->get('api.service.helpers')->calculateDistanceFromLocation($location, implode(",", $place["location"]));
                }

                // HATEOAS link
                $links["self"] = [
                    "href" => "/places/$placeId",
                    "rel" => "self"
                ];

                // Links added at the end
                $place["links"] = $links;

                $places[] = $place;

            }


            // Sorting

            if (!is_null($sort) && is_string($sort)) {
                $sortingOrder = explode(",",$sort);
                array_unique($sortingOrder);
                usort($places, $this->get('api.service.helpers')->sorter($sortingOrder));
            }

            // Build final response body
            $resultBody = [];
            if ($nextPageToken) {
                $resultBody["next_page"] = "/places?next_page_token=$nextPageToken";
            }

            $resultBody["results"] = $places;

            return new JsonResponse($resultBody);

        } catch (\Exception $exception) {

            return new JsonResponse([
                'error_message' => $exception->getMessage(),
                'code'    => $exception->getCode(),
            ]);

        }
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

        return new JsonResponse($responseBody["result"]);
    }


    
}
