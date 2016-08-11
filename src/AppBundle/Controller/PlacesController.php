<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

//use GuzzleHttp;

/**
 * Rest controller for places
 * @Route("/api")
 * @Method({"GET"})
 */
class PlacesController extends Controller {

    /**
     * General places resource providing information about places from Google Places API
     *
     * @ApiDoc(
     *  section="Places",
     *  description="Returns a places list",
     *  parameters={
     *      {"name"="key", "dataType"="integer", "required"=true, "format"="\w+", "description"="Google Places API key. (https://developers.google.com/places/web-service/get-api-key)"},
     *      {"name"="radius", "dataType"="integer", "required"=false, "format"="\d+", "description"="Search radius in meters (default=2000)"},
     *      {"name"="rankby", "dataType"="string", "required"=false, "format"="\w+", "description"="Specifies order (prominence/distance) in which places are listed (default=prominence"},
     *      {"name"="type", "dataType"="string", "required"=false, "format"="\w+", "description"="Specifies type of returned places are listed (default=bar)"},
     *      {"name"="location", "dataType"="string", "required"=false, "format"="-?\d{1,2}\.\d{0,10},-?\d{1,2}\.\d{0,10}", "description"="The latitude/longitude around which to retrieve places (default=54.348538,18.653228 Neptune's Fountain in Gdansk)"},
     *      {"name"="next_page_token", "dataType"="string", "required"=false, "format"="\w+", "description"="Token for returning next page for previous search"},
     *      {"name"="name", "dataType"="string", "required"=false, "format"="\w+", "description"="Parameter matching against the names of search places."},
     *      {"name"="opennow", "dataType"="bool", "required"=false, "format"=".*", "description"="Flag for filtering only palces open at the request time"},
     *      {"name"="sort", "dataType"="string", "required"=false, "format"="-?\w+(,-?\w+)*", "description"="Parameter for sorting results. Sort parameter take in list of comma separated fields
                (name, place_id, rating, location, price_level, opening_hours, vicinity), each with a possible unary negative (e.g. -rating) to imply descending sort order.
           "},
     *  }
     * )
     *
     * @Route("/places", name="api_places_default")
     * @Method({"GET"})
     * @param Request $request Symfony http Request object
     * @return array
     */
    public function placesAction(Request $request) {

        try {

            // Parameters
            $parameters = $this->preparePlacesRequestParameters($request);
            $options = $this->preparePlacesRequestOptions($parameters);
            $responseBody = $this->get('api.requests.service')->makeJsonRequest($this->getParameter('google_places_url'), $options);
            $places = $this->buildPlacesArray($responseBody, $parameters);

            // Sort places array if requested
            if (!is_null($parameters["sort"]) && is_string($parameters["sort"])) {
//                $places = $this->sortPlacesByParameters($places, $parameters);
                $places = $this->get('api.service.helpers')->sortArrayByFields($places, $parameters["sort"]);
            }

            // Build final response body
            $resultBody = [];
            // Get next page token and uri
            if (isset($responseBody["next_page_token"])) {
                $resultBody["next_page"] = "/api/places?next_page_token=" . $responseBody["next_page_token"];
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
     * Single place details resource providing information about particular place from Google Places API
     *
     * @ApiDoc(
     *  section="Places",
     *  description="Returns a single place details",
     *  requirements={
     *      {
     *          "name"="placeId",
     *          "dataType"="string",
     *          "requirement"="\w+",
     *          "description"="Place unique id from Google Places API."
     *      }
     *  },
     *  parameters={
     *      {"name"="key", "dataType"="integer", "required"=true, "format"="\w+", "description"="Google Places API key. (https://developers.google.com/places/web-service/get-api-key)"},
     *  }
     * )
     *
     * @Route("/places/{placeId}", name="api_place_details")
     * @Method({"GET"})
     * @param string $placeId Unique place id
     * @param Request $request Symfony http Request object
     * @return array
     */
    public function placeDetailsAction($placeId, Request $request) {
        try {

            $options = [
                "placeid" => $placeId,
//                "key"     => $this->getParameter("google_api_key"),
                "key" => $request->get("key"),
            ];
            if (is_null($options["key"])) {
                throw new \Exception("Missing Google Places API key", 401);
            }
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


    /**
     * Method for building output array for single place from Google Place API response
     *
     * @param array $responseBody
     * @return array $placeArray
     */
    private function buildSinglePlaceArray($responseBody) {
        $responseResult = $responseBody["result"];
        $photos = [];
        // Generate array containing photos if they exist
        if (!empty($responseResult["photos"])) {
            foreach ($responseResult["photos"] as $photo) {
                $photos[] = [
                    "height" => $photo["height"],
                    "width"  => $photo["width"],
                    "link"   => "/api/photos/" . $photo["photo_reference"]
                ];
            }
        }

        $placeArray = [
            "name"              => $responseResult["name"] ?? null,
            "place_id"          => $responseResult["place_id"] ?? null,
            "simple_address"    => $responseResult["vicinity"] ?? null,
            "formatted_address" => $responseResult["formatted_address"] ?? null,
//            "address_components" => $responseResult["address_components"] ?? null,
            "phone"             => $responseResult["international_phone_number"] ?? null,
            "photos"            => $photos ?? null,
            "rating"            => $responseResult["rating"] ?? null,
            "price_level"       => $responseResult["price_level"] ?? null,
            "location"          => $responseResult["geometry"]["location"] ?? null,
            "reviews"           => $responseResult["reviews"] ?? null,
            "types"             => $responseResult["types"] ?? null,
            "opening_hours"     => $responseResult["opening_hours"] ?? null,
            "google_maps_url"   => $responseResult["url"] ?? null,
            "website_url"       => $responseResult["website"] ?? null,

        ];

        return $placeArray;
    }

    /**
     * Method for building output array of places from Google Place API response and given parameters
     *
     * @param $responseBody
     * @param $parameters
     * @return array
     */
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
                    "href" => "/api/photos/$photoId",
                    "rel"  => "photo"
                ];
            }

            $place = [
                "name"          => $responsePlace["name"],
                "place_id"      => $placeId,
                "rating"        => $responsePlace["rating"] ?? null,
                "location"      => $responsePlace["geometry"]["location"] ?? null,
                "price_level"   => $responsePlace["price_level"] ?? null,
                "opening_hours" => $responsePlace["opening_hours"] ?? null,
                "vicinity"      => $responsePlace["vicinity"] ?? null,
            ];


            // To calculate distance from user to a place we need to use same format for distances
            // lat,long -> 54.348538,18.653228
            if (!is_null($parameters["location"]) && $place["location"]) {
                //TODO remove implode/explode action, instead use 4 arguments
                $place["distance"] = $this->get('api.service.helpers')->calculateDistanceFromLocation($parameters["location"], implode(",", $place["location"]));
            }

            // HATEOAS link
            $links["self"] = [
                "href" => "/api/places/$placeId",
                "rel"  => "self"
            ];

            // Links added at the end
            $place["links"] = $links;

            $places[] = $place;

        }

        return $places;
    }

    /**
     * Method for preparing parameters for single place request
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function preparePlacesRequestParameters(Request $request) {

        $requestParameters = $request->query->all();

        // Check if key is passed as a parameter
        if (!isset($requestParameters["key"])) {
            throw new \Exception("Missing Google Places API key", 401);
        }

        $defaults = [
            "radius"        => 2000, // Default radius - 2000m
            "rankby"        => "prominence", // Default rank by prominence
            "type"          => "bar", // Default type - bar
            "location"      => "54.348538,18.653228", // Default location - Neptune's Fountain
            "next_page_token" => null, // Next page token is not set as default - used to paginate
            "name"          => null, // Query for a name search
            "opennow"       => null, // Open fo business at the time query is sent
            "sort"          => null, // Sorting parameters
        ];

        $parameters = [
            "radius"        => $requestParameters["radius"] ?? $defaults["radius"],
            "rankby"        => $requestParameters["rankby"] ?? $defaults["rankby"],
            "type"          => $requestParameters["type"] ?? $defaults["type"],
            "location"      => $requestParameters["location"] ?? $defaults["location"],
            "next_page_token" => $requestParameters["next_page_token"] ?? $defaults["next_page_token"],
            "name"          => $requestParameters["name"] ?? $defaults["name"],
            "opennow"       => $requestParameters["opennow"] ?? $defaults["opennow"],
            "sort"          => $requestParameters["sort"] ?? $defaults["sort"],
//            "key"           => $this->getParameter("google_api_key"),
            "key"           => $requestParameters["key"],
        ];
        // Check validity of all requested parameters
        // Searching for extra parameters by diffing request parameters with defined parameters set
        $extraParameters = array_diff(array_keys($requestParameters), array_keys($parameters));
        if (!empty($extraParameters)) {
            throw new \Exception("Invalid parameter(s): '" . implode("', '", $extraParameters) . "'", 400);
        }

        return $parameters;

    }

    /**
     * Method for preparing options for places request
     *
     * @param $parameters
     * @return array
     */
    private function preparePlacesRequestOptions($parameters) {

        $options = [
            "location" => $parameters["location"],
            "radius"   => $parameters["radius"],
            "type"     => $parameters["type"],
            "rankby"   => $parameters["rankby"],
            "key"      => $parameters["key"]
        ];
        // Handling optional parameters
        if ($parameters["next_page_token"]) {
            $options["pagetoken"] = $parameters["next_page_token"];
        }
        if ($parameters["opennow"]) {
            $options["opennow"] = $parameters["opennow"];
        }
        if ($parameters["name"]) {
            $options["name"] = $parameters["name"];
        }
        // Google Places API restriction - rankby=distance must be used without radius parameter
        if ($parameters["rankby"] == "distance") {
            unset($options["radius"]);
        }

        return $options;
    }


}
