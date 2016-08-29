<?php
/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 09/08/16
 * Time: 13:16
 */

namespace AppBundle\Service;

use GuzzleHttp;
use GuzzleHttp\Exception;

class RequestsService
{

    private $client;

    public function __construct(GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    public function makeJsonRequest($url, $options)
    {
        $responseJson = $this->httpRequest("GET", $url, $options);
        $responseBody = GuzzleHttp\json_decode($responseJson, true);
        if ($responseBody["status"] != "OK") {
            $errorBody = $responseBody["status"];
            if (isset($responseBody["error_message"])) {
                $errorBody .= ". " . $responseBody["error_message"];
            }
            throw new \Exception($errorBody, 400);
        }

        return $responseBody;
    }

    public function makeFileRequest($url, $options)
    {

        $responseFile = $this->httpRequest("GET", $url, $options);

        return $responseFile;
    }

    private function httpRequest($method, $url, $options)
    {
        try {
            $responseFile = $this->client
                ->request($method, $url, ["query" => $options])
                ->getBody();

            return $responseFile;
        } catch (\Exception $e) {

            throw new \Exception("Bad request", $e->getCode());
        }

    }

}