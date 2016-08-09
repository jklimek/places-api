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
class PlacesController extends Controller {

    /**
     * @Route("/places", name="places_default")
     * @param Request $request
     * @return array
     */
    public function placesAction(Request $request) {

        try {
            // Check if http method is valid (accepting only GET for this resource)
            if (!$request->isMethod('GET')) {
                throw new \Exception("Method not allowed. This resource is handled via GET method.", 405);
            }

            $client = new GuzzleHttp\Client();
            $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";

            // Parameters
            $parameters = $this->prepareParameters($request);
            $options = $this->preparePlacesRequestOptions($parameters);
            $responseBody = $this->makeApiRequest($client, $url, $options);
            $places = $this->buildPlacesArray($responseBody, $parameters);

            // Sort places array if requested
            if (!is_null($parameters["sort"]) && is_string($parameters["sort"])) {
                $places = $this->sortByParameters($places, $parameters);
            }

            // Build final response body
            $resultBody = [];
            // Get next page token and uri
            if ($responseBody["next_page_token"]) {
                $resultBody["next_page"] = "/places?next_page_token=".$responseBody["next_page_token"];
            }
            $resultBody["results"] = $places;
            $resultBody["status"] = "OK";

            return new JsonResponse($resultBody);

        } catch (\Exception $exception) {

            return new JsonResponse([
                'status'        => "ERROR",
                'error_message' => $exception->getMessage(),
                'code'          => $exception->getCode(),
            ]);

        }
    }

    private function buildPlacesArray($responseBody, $parameters) {
        // Building places array
        $places = [];
        foreach ($responseBody["results"] as $responsePlace) {
            $place = [];
            if (!isset($responsePlace["place_id"])) {
                continue;
            }
            $placeId = $responsePlace["place_id"];

            if (isset($responsePlace["photos"][0]["photo_reference"])) {
                // Get first photo reference
                $photoId = $responsePlace["photos"][0]["photo_reference"];

                // Additional HATEOAS photo link
                $links["photo"] = [
                    "href" => "/photos/$photoId",
                    "rel"  => "photo"
                ];
            }

            $place = [
                "name" => $responsePlace["name"],
                "place_id" => $placeId,
                "rating" => $responsePlace["rating"] ?? null,
                "location" => $responsePlace["geometry"]["location"] ?? null,
            ];


            // To calculate distance from user to a place we need to use same format for distances
            // lat,long -> 54.348538,18.653228
            if (!is_null($parameters["location"]) && $place["location"]) {
                //TODO remove implode/explode action, instead use 4 arguments
                $place["distance"] = $this->get('api.service.helpers')->calculateDistanceFromLocation($parameters["location"], implode(",", $place["location"]));
            }

            // HATEOAS link
            $links["self"] = [
                "href" => "/places/$placeId",
                "rel"  => "self"
            ];

            // Links added at the end
            $place["links"] = $links;

            $places[] = $place;

        }

        return $places;
    }

    private function sortByParameters($places, $parameters) {

        $sortingOrder = explode(",", $parameters["sort"]);
        array_unique($sortingOrder);
        usort($places, $this->get('api.service.helpers')->sorter($sortingOrder));

        return $places;

    }

    private function prepareParameters(Request $request) {
        $defaults = [
            "radius" => 2000, // Default radius - 2000m
            "rankBy" => "prominence", // Default rank by prominence
            "type" => "bar", // Default type - bar
            "location" => "54.348538,18.653228", // Default location - Neptune's Fountain
            "nextPageToken" => null, // Next page token is not set as default - used to paginate
            "name" => null, // Query for a name search
            "sort" => null, // Sorting parameters
        ];

        $parameters = [
            "radius" => $request->get("radius") ?? $defaults["radius"],
            "rankBy" => $request->get("rankby") ?? $defaults["rankBy"],
            "type" => $request->get("type") ?? $defaults["type"],
            "location" => $request->get("location") ?? $defaults["location"],
            "nextPageToken" => $request->get("next_page_token") ?? $defaults["nextPageToken"],
            "name" => $request->get("name") ?? $defaults["name"],
            "sort" => $request->get("sort") ?? $defaults["sort"],
            "key" => $this->getParameter("google_api_key"),
//            "key" => $request->get("key"),
        ];

        if (empty($parameters["key"])) {
            throw new \Exception("Missing Google Places API key", 401);
        }

        return $parameters;

    }

    private function preparePlacesRequestOptions($parameters) {

        $options = [
            "location" => $parameters["location"], // Default location - Neptune's Fountain,
            "radius"   => $parameters["radius"],
            "type"     => $parameters["type"],
            "rankby"   => $parameters["rankBy"],
            "key"      => $parameters["key"]
        ];
        // Handling optional parameters
        if ($parameters["nextPageToken"]) {
            $options["pagetoken"] = $parameters["nextPageToken"];
        }
        if ($parameters["name"]) {
            $options["name"] = $parameters["name"];
        }
        // Google Places API restriction - rankby=distance must be used without radius parameter
        if ($parameters["rankBy"] == "distance") {
            unset($options["radius"]);
        }

        return $options;
    }

    private function makeApiRequest(GuzzleHttp\Client $client, $url, $options) {
        $responseJson = $client
                            ->request("GET", $url, ["query" => $options])
                            ->getBody();
        $responseBody = GuzzleHttp\json_decode($responseJson, true);
        if ($responseBody["status"] != "OK") {
            throw new \Exception($responseBody["status"], 400);
        }

        return $responseBody;
    }


    /**
     * @Route("/places/{place_id}", name="place_details")
     * @param Request $request
     * @return array
     */
    public function placeDetailsAction(Request $request) {
        try {
            $client = new GuzzleHttp\Client();
            $options = [
                "placeid" => $request->get("placeId"),
                "key"     => $request->get("key")
            ];
            $url = "https://maps.googleapis.com/maps/api/place/details/json";
            $responseBody = $this->makeApiRequest($client, $url, $options);

            return new JsonResponse($responseBody["result"]);


        } catch (\Exception $exception) {

            return new JsonResponse([
                'status'        => "ERROR",
                'error_message' => $exception->getMessage(),
                'code'          => $exception->getCode(),
            ]);

        }
    }


}
