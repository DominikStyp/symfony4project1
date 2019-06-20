<?php


namespace App\EventListeners;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class RuntimeExceptionsLogger {
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $message = $exception->getMessage();
        $response = new Response();
        // setup the Response object based on the caught exception
        $event->setResponse($response);
        $this->logger->error($message, ['stack_trace' => $exception->getTraceAsString()]);

        // you can alternatively set a new Exception
        // $exception = new \Exception('Some special exception');
        // $event->setException($exception);
    }



}