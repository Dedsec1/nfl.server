<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 12:49
 */

namespace NflBundle\Command;

use NflBundle\Lib\NflHandler;
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
//        $is_topic   = $input->getOption("topic");


        if ($games) {
            foreach ($games as $game) {
                if (($sgame != null) && (stripos($game['file_name'], $sgame) === false)) {
                    continue;
                }
                $is_shift = (stripos($game['file_name'], $sgame) !== false) && ($shift != null);


                $output->write($game['file_name'] . "\t\t:: ");
                $status = $this->nflHandler->streamGame(
                    $game
                    , $is_shift ? $shift : false
                    , !$this->nflHandler->conds
                );

                switch ($status) {
                    case NflHandler::GAME_MD5_NOT_FOUND:
                        $output->writeln("<error>MD5 not found, try again later</error>");
                        break;
                    case NflHandler::GAME_STREAMING:
                        if ($is_shift) {
                            $output->writeln("<fg=cyan>continue streaming from ".$shift."</>");
                        } else {
                            $output->writeln("<fg=cyan>start game streaming...</>");
                        }
                        break;
                    case NflHandler::GAME_FILE_EXISTS:
                        $output->writeln("<info>Game file already exists</info>");
                        break;
                    case NflHandler::GAME_URL_NOT_FOUND:
                    default:
                        $output->writeln("<error>Game URL NOT FOUND</error>");
                        break;

                }

                if (!$this->nflHandler->conds){//$is_topic) {
                    $topic = $this
                        ->getContainer()
                        ->get('templating')
                        ->render(
                            "NflBundle:Default:topic.html.twig"
                            , array(
                                'game' => $game,
                                'nfl'  => $this->nflHandler
                            )
                        )
                    ;
                    file_put_contents(
                        sprintf(
                            "%s/%s.txt"
                            , $this->nflHandler->getGameFileDir()
                            , $game['file_name']
                        )
                        , $topic
                    );
                }

                if ($status === NflHandler::GAME_STREAMING) {
                    $this->isStreaming = true;
                    break;
                }
            }
        }
    }
}