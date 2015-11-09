<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 12:52
 */

namespace NflBundle\Command;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NflBundle\Lib\RatingHandler;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;

class RatingCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:rating')
            ->setDescription('Get NFL games rating')
        ;
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rating = $this->nflHandler->getRating();

        $output->writeln(
            sprintf(
                "<info>Rating dates:: [%s - %s]</info>"
                , $rating["start_date"]
                , $rating["end_date"]
            )
        );


        $table = new Table($output);
        $table
            ->setHeaders(array('Game', 'Rating', 'Predict'))
            ->setStyle('borderless')
            ->addRow(array(new TableCell('<error>Finished games</error>', array('colspan' => 3))))
        ;

        $output->writeln("<error>Finished games</error>");
        foreach ($rating["finished"] as $game) {
            $table->addRow(array(
                sprintf("%s @ %s", $game["away_team"], $game["home_team"])
                , $game["gex"]
                , $game["gex_predict"]
            ));
        }

        $table
            ->addRows(array(
                new TableSeparator(),
                array(new TableCell('<error>Future games</error>', array('colspan' => 3)))
            ));

        $output->writeln("");
        $output->writeln("<error>Future games</error>");
        foreach ($rating["future"] as $game) {
            $table->addRow(array(
                sprintf("%s @ %s", $game["away_team"], $game["home_team"])
                , ""
                , $game["gex_predict"]
            ));
        }

        $table->render();
    }
}