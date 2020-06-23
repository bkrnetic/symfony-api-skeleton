<?php

namespace App\EventListener;

use App\Exception\ApiAccessDeniedException;
use App\Exception\VerboseExceptionInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request instanceof Request || $request->getRequestFormat() !== 'json') {
            return;
        }

        $exception = $event->getThrowable();

        $response = [
            'message' => '',
        ];

        $exceptionInterfaces = class_implements(\get_class($exception));

        if (
            isset($exceptionInterfaces[HttpExceptionInterface::class]) &&
            method_exists($exception, 'getStatusCode')
        ) {
            // our exception is a HttpException, get status code from it
            /* @var HttpExceptionInterface $exception */
            $response['code'] = $exception->getCode();

            if (
                $exception instanceof NotFoundHttpException &&
                strpos($exception->getMessage(), 'No route found') !== false
            ) {
                // Invalid route.
                $response['message'] = 'Invalid request.';
            } elseif ($exception instanceof AccessDeniedHttpException || $exception instanceof ApiAccessDeniedException) {
                $response['message'] = 'Not allowed, insufficient permissions.';
            }
        } else {
            $response['code'] = $exception->getCode() === 0 ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode();

            if (!$this->kernel->isDebug()) {
                $response['message'] = '';
            }
        }

        if ($response['message'] === '' && isset(Response::$statusTexts[$response['code']])) {
            $response['message'] = Response::$statusTexts[$response['code']];
        }

        if (
            isset($exceptionInterfaces[VerboseExceptionInterface::class]) &&
            method_exists($exception, 'getExtraData')
        ) {
            /* @var VerboseExceptionInterface $exception */
            $response['errors'] = $exception->getExtraData();
        }

        // give some more feedback in debug mode :-)
        if ($this->kernel->isDebug()) {
            $response['debug'] = [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'exception' => FlattenException::createFromThrowable($exception)->toArray(),
            ];
        }

        $event->setResponse(new JsonResponse($response, $response['code']));
    }
}
