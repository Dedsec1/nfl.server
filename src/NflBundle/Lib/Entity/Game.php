<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 16.08.2016
 * Time: 11:56
 */

namespace NflBundle\Lib\Entity;

use NflBundle\Lib\Utils\NflTeams;

class Game
{
    private $date;
    private $time;
    private $datetime;

    private $id;
    private $gameId;
    private $game;
    private $fileName;
    private $elias;

    private $away;
    private $home;
    private $score;

    private $logo_away;
    private $logo_home;

    //video info
    private $duration = null;
    private $shift = false;

    public function __construct($xml, $conf)
    {
//        $day        = strtotime($xml['d']);
        $awayId     = strtolower($xml->away['id']);
        $homeId     = strtolower($xml->home['id']);

        switch ($conf["type"]) {
            case "pre":
                $index = 1;
                break;
            case "post":
            case "pro":
                $index = 3;
                break;
            case "reg":
            default:
                $index = 2;
        };

        $gameId = sprintf("%d_%d_%s_%s_%d_h_%s"
            , $index
            , $xml['id']
            , $awayId
            , $homeId
            , $conf["year"]
            , $conf["conds"] ? 'snap2w' : 'whole'
        );
        $fileName = sprintf("NFL%d.%s%02d.%s-%s.%s%s"
            , $conf["year"]
            , $conf["type"] == "pre" ? "PRE" : "W"
            , $conf["week"]
            , NflTeams::$teams[$awayId] ? NflTeams::$teams[$awayId]["name"] : $awayId
            , NflTeams::$teams[$homeId] ? NflTeams::$teams[$homeId]["name"] : $homeId
            , $conf["resolution"]
            , $conf["conds"] ? ".CG" : ""
        );

        $this->date     = $xml['d'];
        $this->time     = $xml['t'];
        $this->datetime = $xml['d']." ".$xml['t'];

        $this->gameId   = $gameId;
        $this->fileName = $fileName;

        $this->away     = NflTeams::$teams[$awayId]["city"]." ".NflTeams::$teams[$awayId]["name"];
        $this->home     = NflTeams::$teams[$homeId]["city"]." ".NflTeams::$teams[$homeId]["name"];

        $this->game      = $awayId."@".$homeId;
        $this->id        = intval($xml["id"]);
        $this->elias     = strtolower($xml["elias"]);
        $this->score     = sprintf("%d - %d", $xml->away['p'], $xml->home['p']);

        $this->logo_away = NflTeams::$teams[$awayId]["logo"];
        $this->logo_home = NflTeams::$teams[$homeId]["logo"];

    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param string $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return string
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param string $game
     */
    public function setGame($game)
    {
        $this->game = $game;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getElias()
    {
        return $this->elias;
    }

    /**
     * @param string $elias
     */
    public function setElias($elias)
    {
        $this->elias = $elias;
    }

    /**
     * @return string
     */
    public function getAway()
    {
        return $this->away;
    }

    /**
     * @param string $away
     */
    public function setAway($away)
    {
        $this->away = $away;
    }

    /**
     * @return string
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * @param string $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param string $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getLogoAway()
    {
        return $this->logo_away;
    }

    /**
     * @param mixed $logo_away
     */
    public function setLogoAway($logo_away)
    {
        $this->logo_away = $logo_away;
    }

    /**
     * @return mixed
     */
    public function getLogoHome()
    {
        return $this->logo_home;
    }

    /**
     * @param mixed $logo_home
     */
    public function setLogoHome($logo_home)
    {
        $this->logo_home = $logo_home;
    }

    /**
     * @return null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param null $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }


    /**
     * @return boolean
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * @param boolean $shift
     */
    public function setShift($shift)
    {
        $this->shift = $shift;
    }
}