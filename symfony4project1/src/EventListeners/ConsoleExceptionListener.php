<?php


namespace App\EventListeners;


use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;

class ConsoleExceptionListener {

    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function onConsoleError(ConsoleErrorEvent $event) {
        $error = $event->getError();
        $message = $error->getMessage();
        $this->logger->error($message, ['stack_trace' => $error->getTraceAsString()]);
    }


}