<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 12:52
 */

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RatingCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:rating')
            ->setDescription('Get NFL games rating')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Rating");
    }
}