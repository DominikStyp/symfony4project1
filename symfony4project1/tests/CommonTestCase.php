<?php
/**
 * Created by PhpStorm.
 * User: Dominik
 * Date: 2019-06-11
 * Time: 00:15
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class CommonTestCase extends KernelTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected static $reloadFixturesBeforeTests = true;
    protected static $fixturesClassUsed = 'StaticFixtures';

    /**
     * @param Application $application
     * @return int
     * @throws \Exception
     */
    private static function dropSchema(Application $application){

        $command = $application->find('doctrine:schema:drop');
        $arguments = [
            'command' => 'doctrine:schema:drop',
            '--force' => true

        ];
        $input = new ArrayInput($arguments);
        $output = new ConsoleOutput();
        $returnCode = $command->run($input,$output);
        return $returnCode;
    }


    /**
     * @param Application $application
     * @return int
     * @throws \Exception
     */
    private static function createSchema(Application $application){

        $command = $application->find('doctrine:schema:create');
        $arguments = [
            'command' => 'doctrine:schema:create'
        ];
        $input = new ArrayInput($arguments);
        $output = new ConsoleOutput();
        $returnCode = $command->run($input,$output);
        return $returnCode;
    }


    /**
     * @param Application $application
     * @return int
     * @throws \Exception
     */
    private static function loadDatabaseFixtures(Application $application){
        $command = $application->find('doctrine:fixtures:load');
        $arguments = [
            'command' => 'doctrine:fixtures:load',
            '--group' => [self::$fixturesClassUsed]
        ];
        $input = new ArrayInput($arguments);
        $input->setInteractive(false);
        $output = new ConsoleOutput();
        $returnCode = $command->run($input, $output);
        return $returnCode;
    }

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::bootKernel();
        if(static::$reloadFixturesBeforeTests) {
            $application = new Application(self::$kernel);
            self::loadDatabaseFixtures($application);
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    protected function setUp()
    {
        //$kernel = self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}