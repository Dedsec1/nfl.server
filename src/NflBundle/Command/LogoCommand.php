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
                null,
                InputOption::VALUE_REQUIRED,
                'Team which Game to be overlayed'
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
        $games      = $this->nflHandler->getGames();

        $team      = $input->getOption("game");
        $logo      = $input->getOption("logo");

        if (empty($team)) {
            $output->writeln("<error>Team option cannot be empty.</error>");
            return 0;
        }

        $exists = false;
        if ($games) {
            foreach ($games as $game) {
                if (stripos($game->getFileName(), $team) !== false) {
                    $exists = true;
                    $this->nflHandler->addLogo($game, $logo);
                }
            }
        }

        if (!$exists) {
            $output->writeln("<error>Team was not found.</error>");
            return 0;
        }
    }
}