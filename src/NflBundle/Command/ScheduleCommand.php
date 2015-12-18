<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 04.11.2015
 * Time: 11:01
 */

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:schedule')
            ->setDescription('Get NFL games schedule')
        ;
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schedule = $this->nflHandler->getSchedule();
        if ($schedule) {

            if ($schedule["byes"]) {
                $output->writeln(
                    sprintf("<info><info>[size=120][color=gray][i] Byes: %s [/i][/color][/size]</info>", join(", ", $schedule["byes"]))
                );
            }

            foreach ($schedule["week"] as $date => $times) {
                $output->writeln(
                    sprintf("<info>\r\n[size=130][font=Georgia][color=darkblue][b] %s [/b][/color][/font][/size]</info>", $date)
                );
                foreach ($times as $time) {
                    $output->writeln(
                        sprintf("<info>[size=110][font=Georgia][color=gray][i][%s EST] [%s Kiev] [%s Moscow][/i][/color][/font][/size]</info>"
                            , $time["timeEST"]
                            , $time["timeKyiv"]
                            , $time["timeMoscow"]
                        )
                    );
                    foreach ($time["games"] as $game) {
                        $output->writeln(
                            sprintf("<fg=cyan>[size=120][color=indigo] %s [/color][/size]</>", $game)
                        );
                    }
                }
            }
        }

    }

}