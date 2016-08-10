<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;

class CustomExceptionController extends ExceptionController
{

    /**
     * Overriding showAction method and displaying errors as JSON
     * @param Request $request
     * @param FlattenException $exception
     * @param DebugLoggerInterface|null $logger
     * @return JsonResponse
     */
//    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null) {
//
//        $code = $exception->getStatusCode();
//
//        // TODO more error messages
//        switch ($code) {
//            case 404:
//                $errorMessage = "Resource not found.";
//                break;
//            case 0:
//                $code = 400;
//                $errorMessage = "$code Bad request. " . (Response::$statusTexts[$code] ?? '') . ".";
//                break;
//            default:
//                $errorMessage = "$code " . (Response::$statusTexts[$code] ?? '') . ".";
//        }
//
//
//        return new JsonResponse([
//            'status'        => "ERROR",
//            'error_message' => $errorMessage,
//            'code'          => $code,
//        ]);
//    }
}
