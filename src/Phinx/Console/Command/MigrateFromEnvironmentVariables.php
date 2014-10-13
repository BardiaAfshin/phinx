<?php
/**
 * Author: bafshin
 * Email: Bardia Afshin <bafshin@guthy-renker.com>
 * Date: 10/13/14
 * Time: 10:44 AM
 */

namespace Phinx\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;





class MigrateFromEnvironmentVariables extends AbstractCommand {

    private $environemnt_variables = array(
                                    'host'      => 'PHINX_DBHOST',
                                    'name'    => 'PHINX_DBNAME',
                                    'user'      => 'PHINX_DBUSER',
                                    'pass'      => 'PHINX_DBPASS',
                                    'port'      => 'PHINX_DBPORT'
                                    );

    private $arguments = array('host','name','user','port');
    private $options = array('pass');

    protected function configure()
    {
        parent::configure();
        $this->setName('migrate-from-environment-variables');

        $this->addArgument(
            'host',
            InputArgument::REQUIRED,
            'The hostname'
        );

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'The database name to execute the available migrations on'
        );

        $this->addArgument(
            'user',
            InputArgument::REQUIRED,
            'The username credentials to connect to the database'
        );

        $this->addArgument(
            'port',
            InputArgument::REQUIRED,
            'The database port to connect to via'
        );

        $this->addOption(
            'pass',
            null,
            InputOption::VALUE_OPTIONAL,
            'The password credential to connect to the database',
            null
        );


        $this->setDescription('Pass the configuration variables at CLI')
            ->setHelp(
<<<EOT
The <info>migrate-from-environment-variables</info> command runs all available migrations, via set environment
variables

<info>phinx migrate-from-environment-variables [HOST] [DBNAME] [USER] [PASSWORD] [PORT]</info>

EOT
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bootstrap($input, $output);

//        $version = $input->getOption('target');
//        $environment = $input->getOption('environment');

        /*
        if (null === $environment) {
            $environment = $this->getConfig()->getDefaultEnvironment();
            $output->writeln('<comment>warning</comment> no environment specified, defaulting to: ' . $environment);
        } else {
            $output->writeln('<info>using environment</info> ' . $environment);
        }
        */

        foreach( $this->environemnt_variables as $key => $value) {

            if(in_array($key, $this->arguments)) {
                echo "\n getArgument $key => $value";
                ${$key} = $input->getArgument("$key");
            }
            else if (in_array($key, $this->options)) {
                ${$key} = $input->getOption("$key");
            }
            else {
                echo "\n $key not found \n";
            }


            if( ${$key} == null) {
                $output->writeln("<error>${value} is not set</error>");
            }
            else {
                putenv("$value=${$key}");
            }
        }


        $environment = 'adhoc';
        $version = 1;

        $envOptions = $this->getConfig()->getEnvironment($environment);
        $output->writeln('<info>using adapter</info> ' . $envOptions['adapter']);
        $output->writeln('<info>using database</info> ' . $envOptions['name']);

        /*
         * you have to set the environment variables before making a call to getManager
         */

        // run the migrations
        $start = microtime(true);
        $this->getManager()->migrate($environment, $version);
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }

}