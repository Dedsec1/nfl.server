<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 12:42
 */

namespace NflBundle\Command;

use NflBundle\Lib\NflHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
//use Symfony\Component\Console\Helper\Table;

class UrlsCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:urls')
            ->setDescription('Get NFL game urls')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $games = $this->nflHandler->getGames();
        if ($games) {
/*
            $table = new Table($output);
            $table
                ->setHeaders(array('Game', 'Status'))
                ->setStyle('borderless')
                ->render()
            ;
*/
            foreach ($games as $game) {
                $output->write($game['file_name']."\t\t:: ");
                $status = $this->nflHandler->searchGameUrl($game);

                switch ($status) {
                    case NflHandler::GAME_NOT_STARTED:

                        $timeKyiv = new \DateTime($game['time'], new \DateTimeZone("America/New_York"));
                        $timeKyiv->setTimezone(new \DateTimeZone("Europe/Kiev"));
                        $output->writeln(
                            sprintf(
                                "<fg=cyan>Game will start at %s</>"
                                , $timeKyiv->format("Y-m-d H:i")
                            )
                        );
                        break;
                    case NflHandler::GAME_IS_RUNNING:
                        $output->writeln("<fg=cyan>Game is still running LIVE</>");
                        break;
                    case NflHandler::GAME_URL_EXISTS:
                        $output->writeln("<info>Game URL already exists</info>");
                        break;
                    case NflHandler::GAME_URL_FOUND:
                        $output->writeln("<info>Game URL found</info>");
                        break;
                    case NflHandler::GAME_URL_NOT_FOUND:
                    default:
                        $output->writeln("<error>Game URL NOT FOUND</error>");
                        break;

                }
/*
                $table = new Table($output);
                $table
                    ->setStyle('compact')
                    ->addRow(array(
                        $game['name']
                        ,
                    ))
                    ->render()
                ;
*/
            }
            return 1;
        }
    }
}