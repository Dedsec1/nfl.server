<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 16:53
 */

namespace NflBundle\Lib;

use NflBundle\Lib\Entity\Game;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\EngineInterface;;

use NflBundle\Lib\Provider\NflProviderInterface;
use NflBundle\Lib\Utils\Utils;
use NflBundle\Lib\Utils\NflTeams;
use NflBundle\Lib\Event\GameStatusEvent;

class NflHandler extends ContainerAware
{
    public $year;
    public $week;
    public $type;    //reg post //pre //pro
    public $conds    = true;
    public $qlty     = 3000; //4500

    public $resolution;
    public $playoff;

    public static $SERVER_LIST 	= ['cdnak.neulion.com']; //'cdnak.neulion.com','cdnl3nl.neulion.com'
    public static $SERVER_ARCH 	= [82, 84];
    const SERVER_START 	= 200;//80
    const SERVER_END 	= 220;//170

    protected $nflProvider;
    protected $dispatcher;
    protected $ratingHandler;
    protected $templating;

    public function __construct(
        NflProviderInterface $provider
        , RatingHandler $handler
        , EngineInterface $templating
        , EventDispatcherInterface $dispatcher

    )
    {
        $this->nflProvider      = $provider;
        $this->ratingHandler    = $handler;
        $this->templating       = $templating;
        $this->dispatcher       = $dispatcher;
    }

    public function init($year, $week, $type, $conds, $qlty)
    {
        if ($year) {
            $this->year = $year;
        } else {
            $this->year = date("Y", strtotime($this->container->getParameter("nfl_kick_off")));
        }
        if ($week != null && is_numeric($week)) {
            $this->week = $week;
        } else {
            if (time() < strtotime($this->container->getParameter("nfl_kick_off"))) {

                $datediff = strtotime($this->container->getParameter("nfl_kick_off")) - time();
                $this->week = floor($datediff / (60 * 60 * 24 * 7));

            } else {
                $datediff = time() - strtotime($this->container->getParameter("nfl_kick_off"));

                $this->week = floor($datediff / (60 * 60 * 24 * 7));
                $this->week++;
            }
        }
        if ($type) {
            $this->type = $type;
        } else {
            if (time() < strtotime($this->container->getParameter("nfl_kick_off"))) {
                $this->type = "pre";
            } elseif ($this->week >= 18) {
                $this->type = "post";
            } else {
                $this->type = "reg";
            }
        }
        $this->conds    = $conds;
        $this->qlty     = $qlty;

        $this->setGameOptions();

        $this->ratingHandler->init(
            $this->year
            , $this->week
            , $this->type
        );
    }

    public function getGames($sort = false)
    {
        $games = array();
        $scores = $this->getScoresXML();

        if (is_null($scores)) {
            return;
        }

        foreach ($scores->games->game as $gamexml) {
            $games[] = new Game($gamexml, array(
                    "year"          => $this->year
                ,   "week"          => $this->week
                ,   "type"          => $this->type
                ,   "conds"         => $this->conds
                ,   "resolution"    => $this->resolution
            ));
        }

        if ($sort) {
            usort($games, function (Game $a, Game $b) {

                //strcmp($a["game"], $b["game"]); //$a["id"] > $b["id"] ? 1 : -1;
                $atime = strtotime($a->getDatetime());
                $btime = strtotime($b->getDatetime());
                $cmp = strcmp($atime, $btime);
                if ($cmp == 0) {
                    return $a->getElias() > $b->getElias()
                        ? 1
                        : -1;
                } else {
                    return $cmp;
                }
            });
        }

        return $games;
    }

    public function getRating() {
        $rating = $this->ratingHandler->getRating();
        $topic = $this
            ->templating
            ->render(
                "NflBundle:Console:rating.html.twig"
                , array(
                    'rating' => $rating
                )
            )
        ;
        file_put_contents(
            sprintf(
                "%s/%d_rating_%s%02d.txt"
                , $this->container->getParameter("nfl_path")
                , $this->year
                , strtoupper($this->type)
                , $this->week
            )
            , $topic
        );

        return $rating;
    }

    public function getSchedule() {
        $date = null;
        $time = null;
        $tzKiev     = new \DateTimeZone('Europe/Kiev');
        $tzMoscow   = new \DateTimeZone('Europe/Moscow');//Moscow
        $tzEST      = new \DateTimeZone('America/New_York');
        $games = $this->getGames(true);

        $schedule = array(
            "byes" => array(),
            "week" => array()
        );

        if ($games) {
            foreach (NflTeams::$teams as $key => $values) {
                $exists = false;
                if ($this->isActiveThisYear($values) && ($values["city"] != "") && ($values["name"] != "")) {
                    foreach ($games as $game) {
                        $exists = $exists || (strpos($game->getGame(), $key) !== false);
                    }
                    if (!$exists && $this->type == "reg") {
                        $schedule["byes"][] = NflTeams::$teams[$key]["city"] . " " . NflTeams::$teams[$key]["name"];
                    }
                }
            }

            foreach ($games as $game) {
                if (strcmp($game->getDate(), $date) != 0) {
                    $date = $game->getDate();
                    $schedule["week"]["$date"] = array();
                }

                $gtime = $game->getDateTime();
                if (strcmp(strtotime($gtime), $time) != 0) {
                    $time = strtotime($gtime);

                    $timeKyiv   = new \DateTime($gtime, $tzEST);
                    $timeKyiv->setTimezone($tzKiev);
                    $timeMoscow = new \DateTime($gtime, $tzEST);
                    $timeMoscow->setTimezone($tzMoscow);
                    $timeEST    = new \DateTime($gtime, $tzEST);

                    $schedule["week"]["$date"]["$time"] = array(
                        "timeEST"       => $timeEST->format("H:i"),
                        "timeKyiv"      => $timeKyiv->format("H:i"),
                        "timeMoscow"    => $timeMoscow->format("H:i"),
                        "games"         => array()
                    );
                }
                $schedule["week"]["$date"]["$time"]["games"][] = sprintf("%s @ %s", $game->getAway(), $game->getHome());

            }
        }
        $topic = $this
            ->templating
            ->render(
                "NflBundle:Console:schedule.html.twig"
                , array(
                    'schedule' => $schedule
                )
            )
        ;
        file_put_contents(
            sprintf(
                "%s/%d_schedule_%02d.txt"
                , $this->container->getParameter("nfl_path")
                , $this->year
                , $this->week
            )
            , $topic
        );
        return $schedule;
    }

    public function searchGameUrl(Game $game) {
        $currentDate = time();//mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $date = new \DateTime($game->getDatetime(), new \DateTimeZone("America/New_York"));
        $file = $this->getGameUriFile();
        $currentFile = file_get_contents($file);

        if ($currentDate < $date->getTimestamp()) {
            $this->sendGameStatus(GameStatusEvent::GAME_NOT_STARTED, $game);
        } elseif (round(($currentDate - $date->getTimestamp())/3600, 1) < 3) {
            $this->sendGameStatus(GameStatusEvent::GAME_IS_RUNNING, $game);
        } else {
            if (strpos($currentFile, $game->getGameId()) > 0) {
                $this->sendGameStatus(GameStatusEvent::GAME_URL_EXISTS, $game);
            } else {
                $url = $this->findGameUrl($game->getId());
/*/
                $url = $this->findGameUrl(sprintf("%s_1_%d", $game['game_id'], $this->qlty));
                if (strlen($url) == 0) {
                    $url = $this->findGameUrl(sprintf("%s_2_%d", $game['game_id'], $this->qlty));
                }
*/
                if (strlen($url) > 0) {
                    file_put_contents($file, $url."\r\n", FILE_APPEND);
                    $this->sendGameStatus(GameStatusEvent::GAME_URL_FOUND, $game);
                } else {
                    $this->sendGameStatus(GameStatusEvent::GAME_URL_NOT_FOUND, $game);;
                }
            }
        }

    }

    public function addLogo(Game &$game, $logo) {
        $dir = $this->getGameFileDir();
        $mkv = sprintf(
            "%s/%s.mkv"
            , $dir
            , $game->getFileName()
        );

        $tmpDir = sprintf(
            "%s/%s"
            , $this->container->getParameter("nfl_path")
            , $this->container->getParameter("nfl_temp_dir")
        );
        if (!file_exists($mkv)) {
            $this->sendGameStatus(GameStatusEvent::FILE_NOT_EXISTS, $game);
            return;
        } else {
            $this->sendGameStatus(GameStatusEvent::GAME_ADD_LOGO, $game);

            if ($logo == '') {
                $logo = sprintf(
                    "%s/logo.png"
                    , $tmpDir
                );
            }

            Utils::addLogo(
                $mkv
                , $logo
                , $tmpDir
                , $this->container->getParameter("nfl_ffmpeg")
                , $this->container->getParameter("nfl_acodec")
            );
        }
    }

    public function streamGame(Game &$game) {
        $currentFile = file_get_contents($this->getGameUriFile());
        $dir         = $this->getGameFileDir();
        $mkv = sprintf(
            "%s/%s.mkv"
            , $dir
            , $game->getFileName()
        );


        $pattern = preg_quote($game->getGameId(), '/');
        // finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";

        if (preg_match_all($pattern, $currentFile, $matches)) {
            $url = implode("\n", $matches[0]);
            $url = trim(preg_replace('/\s+/', '', $url));

            //render topic template
            $this->renderTemplate($game, $url);

            if (!file_exists($mkv) || ($game->getShift() != false)) {

                //get md5
                $md5 = $this->nflProvider->getGameMD5($game->getId());
                if ($md5 == null) {
                    $this->sendGameStatus(GameStatusEvent::GAME_MD5_NOT_FOUND, $game);
                    return 0;
                }

                $this->sendGameStatus(GameStatusEvent::GAME_STREAMING, $game);

                if ($game->getShift() != false) {
                    Utils::stream(
                        $url . "?" . $md5
                        , sprintf("%s/%s2.mkv", $dir, $game->getFileName())
                        , $game->getShift()
                        , $this->container->getParameter("nfl_ffmpeg")
                        , $this->container->getParameter("nfl_acodec")
                    );
                } else {
                    Utils::stream(
                        $url . "?" . $md5
                        , $mkv
                        , null
                        , $this->container->getParameter("nfl_ffmpeg")
                        , $this->container->getParameter("nfl_acodec")
                    );
                }
                return 1;
            } else {
                $this->sendGameStatus(GameStatusEvent::GAME_FILE_EXISTS, $game);
            }
        } else {
            $this->sendGameStatus(GameStatusEvent::GAME_URL_NOT_FOUND, $game);
        }
        return 0;
    }

    /**
     * private methods
     *
     */
    private function isActiveThisYear($team) {
        if (isset($team["years"])) {
            $result = false;
            foreach ($team["years"] as $interval) {
                $years = explode("/", $interval);
                $from   = $years[0];
                $to     = isset($years[1]) ? $years[1] : date("Y");
                $result = $result || ($this->year >= $from && $this->year <= $to);
            }
            return $result;
        } else
            return true;
    }

    private function getGameFileDir() {
        $dir = sprintf("%s/NFL%d.%s%02d.%s%s"
            , $this->container->getParameter("nfl_path")
            , $this->year
            , $this->type == "pre" ? "PRE" : "W"
            , $this->week
            , $this->week >= 18 ? $this->playoff."." : ""
            , $this->conds ? "CG" : "whole"
        );
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir;
    }

    private function getGameUriFile() {
        $file = sprintf("%s/%s/%s_%d_%02d_m3u8_%d.txt"
            , $this->container->getParameter("nfl_path")
            , $this->container->getParameter("nfl_data_dir")
            , $this->conds ? "conds" : "whole"
            , $this->year
            , $this->week
            , $this->qlty
        );

        if (!file_exists($file)) {
            file_put_contents($file, "");
        }
        return $file;
    }

    private function renderTemplate(&$game, $url) {
        if (!$this->conds){

            //get md5
            $md5 = $this->nflProvider->getGameMD5($game->getId());
            if ($md5 != null) {
                //get video duration
                $this->getVideoInfo($game, $url . "?" . $md5);
            }

            $topic = $this
                ->templating
                ->render(
                    "NflBundle:Console:whole.html.twig"
                    , array(
                        'game' => $game,
                        'nfl'  => $this
                    )
                )
            ;
            file_put_contents(
                sprintf(
                    "%s/%s.txt"
                    , $this->getGameFileDir()
                    , $game->getFileName()
                )
                , $topic
            );
        }
    }

    private function getVideoInfo(Game &$game, $url) {
        $info =  Utils::probe($url, $this->container->getParameter("nfl_ffmpeg"));

        //get video duration
        $search='/Duration: (.*?),/';
        preg_match($search, $info, $matches);

        if (count($matches) > 1) {
            $explode = explode('.', $matches[1]);

            $game->setDuration($explode[0]);
        }
    }

    private function setGameOptions() {
        switch ($this->qlty) {
            case 2400:
            case 1600:
                $this->resolution = "540p";
                break;
            case 4500:
            case 3000:
            default:
                $this->resolution = "720p";
                break;
        };

        switch ($this->week) {
            case 18:
                $this->playoff = "WC";
                break;
            case 19:
                $this->playoff = "DP";
                break;
            case 20:
                $this->playoff = "CC";
                break;
            case 21:
                $this->playoff = "PB";
                break;
            case 22:
                $this->playoff = "SB";
                break;
            default:
                $this->playoff = "";
                break;
        }
    }

    private function getScoresXML() {
        $file = sprintf("%s/%s/%d_%s_%02d.xml"
            , $this->container->getParameter("nfl_path")
            , $this->container->getParameter("nfl_data_dir")
            , $this->year
            , $this->type
            , $this->week
        );

        $url = sprintf("http://smb.cdnak.neulion.com/fs/nfl/nfl/stats/scores/%s/%s_%s.xml"
            , $this->year
            , $this->type
            , $this->week
        );

        $xml = Utils::download($url);

        if (strlen($xml) > 0) {
            file_put_contents($file, $xml);
        } else {
            if (file_exists($file)) {
                $xml = file_get_contents($file);
            } else {
                throw new \Exception("server XMl NOT FOUND");
            }
        }

        return new \SimpleXMLElement($xml);
    }

    private function findGameUrl($gameId) {
        return $this->nflProvider->getGameUrl($gameId, $this->conds ? "C" : "A", $this->qlty);

/*
        $server_start  = is_null($server_start)  ? self::SERVER_START  : $server_start;
        $server_end    = is_null($server_end)    ? self::SERVER_END    : $server_end;

        $urls = [];

        //1. check archive servers
        for ($j = 0; $j < count(self::$SERVER_ARCH); $j++) {
            $urls[] = sprintf(
                'http://nlds%d.%s/nlds_vod/nfl/vod/%s.mp4.m3u8'
                , self::$SERVER_ARCH[$j]
                , self::$SERVER_LIST[0]
                , $game_id
            );
        }

        $url = Utils::sendMultiRequests($urls);

        if (!isset($url) || trim($url)==='') {
            $urls = [];
            for ($i = $server_start; $i <= $server_end; $i++) {
                $urls[] = sprintf(
                    'http://nlds%d.%s/nlds_vod/nfl/vod/%s.mp4.m3u8'
                    , $i
                    , self::$SERVER_LIST[0]
                    , $game_id
                );
            }
            $url = Utils::sendMultiRequests($urls);
        }

        return $url;
*/
    }

    private function sendGameStatus($status, $game) {
        $event = new GameStatusEvent($status, $game);
        $this->dispatcher->dispatch("nfl.game_status", $event);
    }
}