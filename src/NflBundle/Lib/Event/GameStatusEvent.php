<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 10.11.2015
 * Time: 14:25
 */

namespace NflBundle\Lib\Event;

use Symfony\Component\EventDispatcher\Event;

class GameStatusEvent extends Event
{
    const GAME_NOT_STARTED      = 0;
    const GAME_IS_RUNNING       = 1;
    const GAME_URL_FOUND        = 2;
    const GAME_URL_EXISTS       = 3;
    const GAME_URL_NOT_FOUND    = 4;
    const GAME_FILE_EXISTS      = 5;
    const GAME_STREAMING        = 6;
    const GAME_MD5_NOT_FOUND    = 7;
    const FILE_NOT_EXISTS       = 8;
    const GAME_ADD_LOGO         = 9;

    protected $status;
    protected $game;

    public function __construct($status, $game)
    {
        $this->status   = $status;
        $this->game     = $game;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }
}