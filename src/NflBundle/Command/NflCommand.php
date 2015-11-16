<?php

namespace NflBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

use NflBundle\Lib\NflHandler;
use NflBundle\Lib\Event\GameStatusEvent;

abstract class NflCommand extends ContainerAwareCommand
{
    protected $nflHandler;
    private static $listeningStatusEvent = false;

    public function __construct(NflHandler $nflHandler)
    {
        $this->nflHandler = $nflHandler;
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $stdout)
    {
        if (!self::$listeningStatusEvent) {
            $dispatcher = $this->getContainer()->get("event_dispatcher");
            $dispatcher->addListener("nfl.game_status", array($this, 'onStatusChange'));
            self::$listeningStatusEvent = true;
        }

        $this->nflHandler->init(
            $input->getOption("year")
            , $input->getOption("week")
            , $input->getOption("type")
            , $input->getOption("conds")
            , $input->getOption("qlty")
        );

        $stdout->writeln(sprintf(
            "<comment>[%s] [year=%d] [week=%d] [type=%s] [qlty=%d] [game=%s]</>"
            , $this->getDescription()
            , $this->nflHandler->year
            , $this->nflHandler->week
            , $this->nflHandler->type
            , $this->nflHandler->qlty
            , $this->nflHandler->conds ? "conds" : "whole"
        ));
    }

    protected function configure() {
        $this
            ->addOption(
                'year',
                'y',
                InputOption::VALUE_OPTIONAL,
                'NFL Season year',
                null
            )
            ->addOption(
                'week',
                'w',
                InputOption::VALUE_OPTIONAL,
                'NFL Season week'
            )
            ->addOption(
                'conds',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Condensed true/false',
                true
            )
            ->addOption(
                'qlty',
                null,
                InputOption::VALUE_OPTIONAL,
                'Video Stream quality',
                3000
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Nfl Game type (REG | POST | PRE | PRO)',
                'reg'
            )
        ;
    }

    public function onStatusChange(GameStatusEvent $event) {
        $output = new ConsoleOutput();
        $game   = $event->getGame();
        $status = $event->getStatus();


        $output->write($game['file_name'] . "\t\t:: ");
        switch ($status) {
            case GameStatusEvent::GAME_MD5_NOT_FOUND:
                $output->writeln("<error>MD5 not found, try again later</error>");
                break;
            case GameStatusEvent::GAME_STREAMING:
                if ($game["shift"] != false) {
                    $output->writeln("<fg=cyan>continue streaming from ".$game["shift"]."</>");
                } else {
                    $output->writeln("<fg=cyan>start game streaming...</>");
                }
                break;
            case GameStatusEvent::GAME_FILE_EXISTS:
                $output->writeln("<info>Game file already exists</info>");
                break;
            case GameStatusEvent::GAME_NOT_STARTED:
                $timeKyiv = new \DateTime($game['time'], new \DateTimeZone("America/New_York"));
                $timeKyiv->setTimezone(new \DateTimeZone("Europe/Kiev"));
                $output->writeln(
                    sprintf(
                        "<fg=cyan>Game will start at %s</>"
                        , $timeKyiv->format("Y-m-d H:i")
                    )
                );
                break;
            case GameStatusEvent::GAME_IS_RUNNING:
                $output->writeln("<fg=cyan>Game is still running LIVE</>");
                break;
            case GameStatusEvent::GAME_URL_EXISTS:
                $output->writeln("<info>Game URL already exists</info>");
                break;
            case GameStatusEvent::GAME_URL_FOUND:
                $output->writeln("<info>Game URL found</info>");
                break;
            case GameStatusEvent::GAME_URL_NOT_FOUND:
            default:
                $output->writeln("<error>Game URL NOT FOUND</error>");
                break;

        }
    }
}