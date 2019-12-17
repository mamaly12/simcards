<?php

namespace App\EventListener;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This class will handle how to show all exceptions to the users
 * Class MessageExceptionListener
 * @package App\EventListener
 */
class MessageExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        $exception = $event->getException();

        $code = $exception->getCode();

        $message = $exception->getMessage();

        $responseData = [
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ];
        if($code!=0) {
            $event->setResponse(new JsonResponse($responseData, $code));
        }else{
            $event->setResponse(new JsonResponse($responseData));
        }
    }

}