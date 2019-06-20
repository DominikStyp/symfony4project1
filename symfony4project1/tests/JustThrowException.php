<?php


namespace App\Tests;


use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class JustThrowException extends CommonTestCase {

   protected static $reloadFixturesBeforeTests = false;

    /**
     * @TODO ZROBIC EVENT SUBSCRIBERA 
     *  https://symfony.com/doc/current/event_dispatcher.html#events-subscriber
     */
    public function testThrowMe(){
        /** @var LoggerInterface $logger */
        $logger = self::$container->get('monolog.logger');
        $logger->error("AAAAAAAAAAAAAAAAAAAAAAAA");
        throw new InvalidConfigurationException("JUST THROWING!");
   }
}