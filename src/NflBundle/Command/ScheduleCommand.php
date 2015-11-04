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
        $date = null;
        $time = null;
        $tzKiev     = new \DateTimeZone('Europe/Kiev');
        $tzMoscow   = new \DateTimeZone('Europe/Moscow');//Moscow
        $tzEST      = new \DateTimeZone('America/New_York');

        $games = $this->nflHandler->getGames(true);
        if ($games) {
            foreach ($games as $game) {
                if (strcmp($game['d'], $date) != 0) {
                    $date = $game['d'];
                    $output->writeln(
                        sprintf("<info>\r\n[size=16][font=\"Georgia\"][color=darkblue][b] %s [/b][/color][/font][/size]</info>", $date)
                    );
                }
                $gtime = $game["time"];
                if (strcmp(strtotime($gtime), $time) != 0) {
                    $time = strtotime($gtime);

                    $timeKyiv   = new \DateTime($gtime, $tzEST);
                    $timeKyiv->setTimezone($tzKiev);
                    $timeMoscow = new \DateTime($gtime, $tzEST);
                    $timeMoscow->setTimezone($tzMoscow);
                    $timeEST    = new \DateTime($gtime, $tzEST);

                    $output->writeln(
                        sprintf("<info>[size=14][font=\"Georgia\"][color=gray][i][%s EST] [%s Kiev] [%s Moscow][/i][/color][/font][/size]</info>"
                            , $timeEST->format("H:i")
                            , $timeKyiv->format("H:i")
                            , $timeMoscow->format("H:i")
                        )
                    );
                }
                $output->writeln(
                    sprintf("<fg=cyan>[size=14][color=indigo] %s @ %s [/color][/size]</>",
                        $game["away"],
                        $game["home"]
                    )
                );
            }
        }
    }

}