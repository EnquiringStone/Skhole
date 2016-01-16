<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 30-Nov-15
 * Time: 23:36
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends ContainerAwareCommand
{
    private $silent;

    protected function configure()
    {
        $this
            ->setName('skhole:build')
            ->setDescription('This will build the entire website.')
            ->addArgument('build_database', InputArgument::REQUIRED, 'Do you want to build the database? (y/n)')
            ->addOption('silent', null, InputOption::VALUE_NONE, 'If set the build will not display any trace')
            ->addArgument('environment', null, InputArgument::OPTIONAL, 'If set it will use the give environment');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getOption('silent'))
            $this->silent = true;
        else
            $this->silent = false;

        $buildDatabase = $input->getArgument('build_database');
        if(strtoupper($buildDatabase) === 'Y') $this->buildDatabase($this->getApplication()->find('doctrine:schema:update'), $output);
        $this->clearCache($this->getApplication()->find('cache:clear'), $output);
        $this->asseticInstall($this->getApplication()->find('assets:install'), $output);
        $this->asseticDump($this->getApplication()->find('assetic:dump'), $output);
    }

    private function buildDatabase(Command $command, OutputInterface $output) {
        if(!$this->silent) $output->writeln('Building database');
        $arguments = array(
            'command' => 'doctrine:schema:update',
            '--no-backup' => true,
            '--force' => true
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }

    private function clearCache(Command $command, OutputInterface $output) {
        if(!$this->silent) $output->writeln('Clearing cache');
        $arguments = array (
            'command' => 'cache:clear'
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }

    private function asseticInstall(Command $command, OutputInterface $output) {
        if(!$this->silent) $output->writeln('Installing assets');
        $arguments = array(
            'command' => 'assets:install'
        );
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }

    private function asseticDump(Command $command, OutputInterface $output) {
        if(!$this->silent) $output->writeln('dumping assets');
        $arguments = array(
            'command' => 'assetic:dump'
        );

        if($this->silent)
            $arguments['--no-debug'] = true;

        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }
}