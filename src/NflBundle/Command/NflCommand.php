<?php

namespace NflBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use NflBundle\Lib\NflHandler;

abstract class NflCommand extends ContainerAwareCommand
{
    protected $nflHandler;

    public function __construct(NflHandler $nflHandler)
    {
        $this->nflHandler = $nflHandler;
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $stdout)
    {

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
                date("Y")
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


}