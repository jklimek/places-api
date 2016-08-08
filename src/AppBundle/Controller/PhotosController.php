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
}
