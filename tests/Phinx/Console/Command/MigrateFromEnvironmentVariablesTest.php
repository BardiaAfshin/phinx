<?php
/**
 * Created by PhpStorm.
 * User: bafshin
 * Date: 10/13/14
 * Time: 12:28 PM
 */

namespace Test\Phinx\Console\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\StreamOutput;
use Phinx\Config\Config;
use Phinx\Console\Command\Migrate;

class MigrateFromEnvironmentVariables
{
    protected $config = array();

    protected function setUp()
    {
    }

    protected function tearDown()
    {

    }

    public function testExecute()
    {
        $application = new \Phinx\Console\PhinxApplication('testing');
        $application->add( new Migrate() );

        $output = new StreamOutput( fopen('php:://memory', 'a', false) );

        $command = $application->find('migrate');

        // mock the manager class
        $managerStub = $this->getMock('\Phinx\Migration\Manager', array(),
                                        array(
                                        $this->config, $output
                                      ));

        $command->setConfig($this->config);
        $command->setManager($managerStub);

        $commandTester = new CommandTester($command);
        $commandTester->execute( array('command' => $command->getName()));

        $this->assertRegex('/no environment variables found', $commandTester->getDisplay());

    }

    /*
     * verify the user ran the command
     * `phinx
     */
    public function testEnvironmentVariablesRquiredPassedViaCLI() {
        $env_variables = $this->phinxEnvVariables();

    }


    protected function phinxEnvVariables() {
        return array(
            'host' => '%%PHINX_DBHOST%%',
            'dbname' => '%%PHINX_DBNAME%%',
            'user' => '%%PHINX_DBUSER%%',
            'pass' => '%%PHINX_DBPASS%%',
            'port' => '%%PHINX_DBPORT%%'
        );
    }

}