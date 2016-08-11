<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp;
use Symfony\Component\HttpFoundation\Response;


/**
 * Rest controller for photos
 * @Route("/api")
 */
class PhotosController extends Controller {
    /**
     *
     * Photo resource from Google Places API
     *
     * @ApiDoc(
     *  section="Photos",
     *  description="Returns photo file",
     *  requirements={
     *      {
     *          "name"="photoId",
     *          "dataType"="string",
     *          "requirement"="\w+",
     *          "description"="Photo unique id from Google Places API."
     *      }
     *  },
     *  parameters={
     *      {"name"="key", "dataType"="integer", "required"=true, "format"="\w+", "description"="Google Places API key. (https://developers.google.com/places/web-service/get-api-key)"},
     *  }
     * )
     * @Route("/photos/{photoId}", name="api_photo")
     * @Method({"GET"})
     * @param string $photoId Unique photo id
     * @param Request $request Symfony http Request object
     * @return Response Photo file stream
     */
    public function photosAction($photoId, Request $request) {
        try {
            $parameters = $this->prepareParameters($photoId, $request);
            $options = $this->prepareOptions($parameters);

            $responsePhoto = $this->get('api.requests.service')->makeFileRequest($this->getParameter('google_place_photo_url'), $options);
            $headers = [
                'Content-Type' => 'image/png'
            ];

            return new Response($responsePhoto, 200, $headers);


        } catch (\Exception $exception) {

            return new JsonResponse([
                'status'        => "ERROR",
                'error_message' => $exception->getMessage(),
                'code'          => $exception->getCode(),
            ]);

        }

    }

    private function prepareOptions($parameters) {
        $options = [
            "photoreference" => $parameters["photoId"],
            "key"            => $parameters["key"]
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
            "maxWidth"  => 800,
            "maxHeight" => 600,
        ];

        $parameters = [
            "photoId"   => $photoId ?? null,
            "maxWidth"  => $request->get("maxwidth") ?? $defaults["maxWidth"],
            "maxHeight" => $request->get("maxheight") ?? $defaults["maxHeight"],
            "key"       => $this->getParameter("google_api_key"),
//            "key"           => $request->get("key"),
        ];

        return $parameters;

    }
}
