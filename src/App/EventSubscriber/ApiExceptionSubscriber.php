<?php
namespace App\EventSubscriber;

use \InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * Called on kernel exception.
     *
     * This allows exceptions to be reformatted and sent to the API as JSON.
     *
     * @param      \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent  $event  The event
     */
    public function onKernelException($event)
    {
        $exception = $event->getException();

        $code = $exception instanceof InvalidArgumentException ? 400 : 500;

        $responseData = [
            'status' => $code,
            'message' => $exception->getMessage(),
        ];

        $event->setResponse(new JsonResponse($responseData, $code));
    }

    /**
      * @return mixed[]
      */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
