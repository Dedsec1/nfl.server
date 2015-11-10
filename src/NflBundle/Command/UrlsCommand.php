<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 12:42
 */

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UrlsCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:urls')
            ->setDescription('Get NFL game urls')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games = $this->nflHandler->getGames();
        if ($games) {
            foreach ($games as $game) {
                $this->nflHandler->searchGameUrl($game);
            }
            return 1;
        }
    }
}