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
    public $isStreaming = false;

    protected function configure()
    {
        $this
            ->setName('nfl:stream')
            ->setDescription('Stream selected NFL game')
            ->addOption(
                'game',
                null,
                InputOption::VALUE_OPTIONAL,
                'Game to be streamed',
                null
            )
            ->addOption(
                'shift',
                null,
                InputOption::VALUE_OPTIONAL,
                'Time shift for streaming',
                null
            )
            ->addOption(
                'logo',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Include logo ?',
                false
            )
/*
            ->addOption(
                'topic',
                null,
                InputOption::VALUE_OPTIONAL,
                'Create topic for tracker',
                true
            )
*/
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games      = $this->nflHandler->getGames();
        $sgame      = $input->getOption("game");
        $shift      = $input->getOption("shift");
        $logo       = $input->getOption("logo");
//        $is_topic   = $input->getOption("topic");


        if ($games) {
            foreach ($games as $game) {
                if (($sgame != null) && (stripos($game['file_name'], $sgame) === false)) {
                    continue;
                }
                $is_shift = (stripos($game['file_name'], $sgame) !== false) && ($shift != null);

                $game['shift'] = $is_shift ? $shift : false;
                $game['logo']  = $logo;

                if ($this->nflHandler->streamGame($game)) {
                    $this->isStreaming = true;
                    break;
                }
            }
        }
    }
}