<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
//use GuzzleHttp;

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

            // Parameters
            $parameters = $this->preparePlacesRequestParameters($request);
            $options = $this->preparePlacesRequestOptions($parameters);
            $responseBody = $this->get('api.requests.service')->makeJsonRequest($this->getParameter('google_places_url'), $options);
            $places = $this->buildPlacesArray($responseBody, $parameters);

            // Sort places array if requested
            if (!is_null($parameters["sort"]) && is_string($parameters["sort"])) {
                $places = $this->sortPlacesByParameters($places, $parameters);
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


    /**
     * @Route("/places/{placeId}", name="place_details")
     * @param string $placeId
     * @param Request $request
     * @return array
     */
    public function placeDetailsAction($placeId, Request $request) {
        try {
            // Check if http method is valid (accepting only GET for this resource)
            if (!$request->isMethod('GET')) {
                throw new \Exception("Method not allowed. This resource is handled via GET method.", 405);
            }
            $options = [
                "placeid" => $placeId,
                "key" => $this->getParameter("google_api_key"),
//                "key" => $request->get("key"),
            ];
            $responseBody = $this->get('api.requests.service')->makeJsonRequest($this->getParameter('google_place_details_url'), $options);
            $placeArray = $this->buildSinglePlaceArray($responseBody);

            // Build final response body
            $resultBody = [];
            $resultBody["results"] = $placeArray;
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

    private function buildSinglePlaceArray($responseBody) {
        $responseResult = $responseBody["result"];
        $photos = [];
        if (!empty($responseResult["photos"])) {
            foreach ($responseResult["photos"] as $photo) {
                $photos[] = [
                    "height" => $photo["height"],
                    "width" => $photo["width"],
                    "link" => "/photos/".$photo["photo_reference"]
                ];
            }
        }

        $placeArray = [
            "name" => $responseResult["name"] ?? null,
            "place_id" => $responseResult["place_id"] ?? null,
            "simple_address" => $responseResult["vicinity"] ?? null,
            "formatted_address" => $responseResult["formatted_address"] ?? null,
//            "address_components" => $responseResult["address_components"] ?? null,
            "phone" => $responseResult["international_phone_number"] ?? null,
            "photos" => $photos ?? null,
            "rating" => $responseResult["rating"] ?? null,
            "price_level" => $responseResult["price_level"] ?? null,
            "location" => $responseResult["geometry"]["location"] ?? null,
            "reviews" => $responseResult["reviews"] ?? null,
            "types" => $responseResult["types"] ?? null,
            "opening_hours" => $responseResult["opening_hours"] ?? null,
            "google_maps_url" => $responseResult["url"] ?? null,
            "website_url" => $responseResult["website"] ?? null,

        ];

        return $placeArray;
//        return $responseBody;
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
                "price_level" => $responsePlace["price_level"] ?? null,
                "opening_hours" => $responsePlace["opening_hours"] ?? null,
                "vicinity" => $responsePlace["vicinity"] ?? null,
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

    private function sortPlacesByParameters($places, $parameters) {

        $sortingOrder = explode(",", $parameters["sort"]);
        array_unique($sortingOrder);
        // Check if sorting arguments match existing fields
        // Diffing sortingOrder with place keys
        $extraFields = array_diff($sortingOrder, array_keys($places[0]));
        if (!empty($extraFields)) {
            throw new \Exception("Invalid sorting field(s): '". implode("', '", $extraFields) . "'", 400);
        }
        usort($places, $this->get('api.service.helpers')->sorter($sortingOrder));

        return $places;

    }

    private function preparePlacesRequestParameters(Request $request) {

        $requestParameters = $request->query->all();
        $defaults = [
            "radius"        => 2000, // Default radius - 2000m
            "rankBy"        => "prominence", // Default rank by prominence
            "type"          => "bar", // Default type - bar
            "location"      => "54.348538,18.653228", // Default location - Neptune's Fountain
            "nextPageToken" => null, // Next page token is not set as default - used to paginate
            "name"          => null, // Query for a name search
            "openNow"       => null, // Open fo business at the time query is sent
            "sort"          => null, // Sorting parameters
        ];

        $parameters = [
            "radius"        => $requestParameters["radius"] ?? $defaults["radius"],
            "rankBy"        => $requestParameters["rankby"] ?? $defaults["rankBy"],
            "type"          => $requestParameters["type"] ?? $defaults["type"],
            "location"      => $requestParameters["location"] ?? $defaults["location"],
            "nextPageToken" => $requestParameters["next_page_token"] ?? $defaults["nextPageToken"],
            "name"          => $requestParameters["name"] ?? $defaults["name"],
            "openNow"       => $requestParameters["opennow"] ?? $defaults["openNow"],
            "sort"          => $requestParameters["sort"] ?? $defaults["sort"],
            "key"           => $this->getParameter("google_api_key"),
//            "key"           => $requestParameters["key"],
        ];
        // Check validity of all requested parameters
        // Searching for extra parameters by diffing request parameters with defined parameters set
        $extraParameters = array_diff(array_keys($requestParameters), array_keys($parameters));
        if (!empty($extraParameters)) {
            throw new \Exception("Invalid parameter(s): '". implode("', '", $extraParameters) . "'", 400);
        }

        return $parameters;

    }

    private function preparePlacesRequestOptions($parameters) {

        $options = [
            "location" => $parameters["location"],
            "radius"   => $parameters["radius"],
            "type"     => $parameters["type"],
            "rankby"   => $parameters["rankBy"],
            "key"      => $parameters["key"]
        ];
        // Handling optional parameters
        if ($parameters["nextPageToken"]) {
            $options["pagetoken"] = $parameters["nextPageToken"];
        }
        if ($parameters["openNow"]) {
            $options["opennow"] = $parameters["openNow"];
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



}
