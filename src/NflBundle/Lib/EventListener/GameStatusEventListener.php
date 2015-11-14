<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 10.11.2015
 * Time: 15:13
 */

namespace NflBundle\Lib\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

use NflBundle\Lib\Event\GameStatusEvent;

class GameStatusEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'nfl.game_status'   => "onStatusChange"
        );
    }

    public function onStatusChange(GameStatusEvent $event)
    {
/*
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
*/
    }
}