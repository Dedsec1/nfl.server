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
    protected $ratingHandler;

    public function setRatingHandler(RatingHandler $handler)
    {
        $this->ratingHandler = $handler;
    }

    protected function configure()
    {
        $this
            ->setName('nfl:rating')
            ->setDescription('Get NFL games rating')
        ;
        parent::configure();
    }
    protected function initialize(InputInterface $input, OutputInterface $stdout)
    {
        parent::initialize($input, $stdout);

        $this->ratingHandler->init(
            $this->nflHandler->year
            , $this->nflHandler->week
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                "<info>Rating dates:: [%s - %s]</info>"
                , $this->ratingHandler->start_date
                , $this->ratingHandler->end_date
            )
        );

        $rating = $this->ratingHandler->getRating();

        $table = new Table($output);
        $table
            ->setHeaders(array('Game', 'Rating'))
            ->setStyle('borderless')
            ->addRow(array(new TableCell('<error>Finished games</error>', array('colspan' => 2))))
        ;

        $output->writeln("<error>Finished games</error>");
        foreach ($rating["finished"] as $game) {
            $output->writeln(sprintf(
                    "<fg=cyan>[size=14][color=indigo]%s @ %s [/color][/size] [color=darkred][size=14][b]%d[/b][/size][/color]</>"
                    , $game["away_team"]
                    , $game["home_team"]
                    , $game["gex"]
                )
            );
            $table->addRow(array(
                sprintf("%s @ %s", $game["away_team"], $game["home_team"]),
                $game["gex"]
            ));
        }

        $table
            ->addRows(array(
                new TableSeparator(),
                array(new TableCell('<error>Future games</error>', array('colspan' => 2)))
            ));

        $output->writeln("");
        $output->writeln("<error>Future games</error>");
        foreach ($rating["future"] as $game) {
            $output->writeln(sprintf(
                    "<fg=cyan>[size=14][color=indigo]%s @ %s [/color][/size] [color=darkgreen][size=14][b]%d[/b][/size][/color]</>"
                    , $game["away_team"]
                    , $game["home_team"]
                    , $game["gex_predict"]
                )
            );
            $table->addRow(array(
                sprintf("%s @ %s", $game["away_team"], $game["home_team"]),
                $game["gex_predict"]
            ));
        }

        $table->render();
    }
}