<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 16.08.2016
 * Time: 16:44
 */

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LogoCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:logo')
            ->setDescription('Overlay selected game with logo')
            ->addOption(
                'game',
                'g',
                InputOption::VALUE_REQUIRED,
                'Team which Game to be overlayed'
            )
            ->addOption(
                'dir',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Game file directory',
                null
            )
            ->addOption(
                'logo',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Logo image path',
                null
            )
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games     = $this->nflHandler->getGames();

        $team      = $input->getOption("game");
        $logo      = $input->getOption("logo");
        $dir       = $input->getOption("dir");

        if (empty($team)) {
            $output->writeln("<error>Team option cannot be empty.</error>");
            return 0;
        }

        $exists = false;
        if ($games) {
            foreach ($games as $game) {
                if (stripos($game->getFileName(), $team) !== false) {
                    $exists = true;
                    $this->nflHandler->addLogo($game, $logo, $dir);
                }
            }
        }

        if (!$exists) {
            $output->writeln("<error>Team was not found.</error>");
            return 0;
        }
    }
}