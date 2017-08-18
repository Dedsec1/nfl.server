<?php
/**successfully*/

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:template')
            ->setDescription('Template command')
            ->addOption(
                'game',
                null,
                InputOption::VALUE_OPTIONAL,
                'Game to prepare template for.',
                null
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games  = $this->nflHandler->getGames();
        $sgame  = $input->getOption("game");


        if (empty($sgame) && !$this->nflHandler->conds) {
            $output->writeln("<error>Game option cannot be empty.</error>");
            return 0;
        }

        if ($games) {
            if ($this->nflHandler->conds) {
                $this->nflHandler->renderCondsTemplate();
                $output->writeln("<info>Conds template successfully rendered</info>");
            } else {
                foreach ($games as $game) {
                    if (($sgame != null) && (stripos($game->getFileName(), $sgame) === false)) {
                        continue;
                    }
                    $this->nflHandler->renderTemplate($game, null);
                    $output->write($game->getFileName() . "\t\t:: ");
                    $output->writeln("<info>Template successfully rendered</info>");
                }
            }
        }
    }
}