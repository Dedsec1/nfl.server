<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 12:49
 */

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StreamCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:stream')
            ->setDescription('Stream selected NFL game')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Stream");
    }
}