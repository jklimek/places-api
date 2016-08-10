<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp;
use Symfony\Component\HttpFoundation\Response;

class PhotosController extends Controller
{
    /**
     * @Route("/photos/{photoId}", name="api_photo")
     * @param $photoId
     * @param Request $request
     * @return array
     */
    public function photosAction($photoId, Request $request) {
        $parameters = $this->prepareParameters($photoId, $request);
        $options = $this->prepareOptions($parameters);

        $responsePhoto = $this->get('api.requests.service')->makeFileRequest($this->getParameter('google_place_photo_url'), $options);
        $headers = [
            'Content-Type' => 'image/png'
        ];

        return new Response($responsePhoto, 200, $headers);
    }

    private function prepareOptions($parameters) {
        $options = [
            "photoreference" => $parameters["photoId"],
            "key" => $parameters["key"]
        ];

        if ($parameters["maxWidth"]) {
            $options["maxwidth"] = $parameters["maxWidth"];
        }
        if ($parameters["maxHeight"]) {
            $options["maxheight"] = $parameters["maxHeight"];
        }

        return $options;
    }


    private function prepareParameters($photoId, Request $request) {

        $defaults = [
            "maxWidth"        => 800,
            "maxHeight"        => 600,
        ];

        $parameters = [
            "photoId"        => $photoId ?? null,
            "maxWidth"        => $request->get("maxwidth") ?? $defaults["maxWidth"],
            "maxHeight"        => $request->get("maxheight") ?? $defaults["maxHeight"],
            "key"           => $this->getParameter("google_api_key"),
//            "key"           => $request->get("key"),
        ];

        return $parameters;

    }
}
