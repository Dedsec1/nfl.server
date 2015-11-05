<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 16:53
 */

namespace NflBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerAware;
use NflBundle\Lib\Utils;
use NflBundle\Lib\NflTeams;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class NflHandler extends ContainerAware
{
    public $year;
    public $week;
    public $type     = 'reg'; //post //pre //pro
    public $conds    = true;
    public $qlty     = 3000; //4500

    public $resolution;
    public $playoff;

    public static $SERVER_LIST 	= ['cdnak.neulion.com']; //'cdnak.neulion.com','cdnl3nl.neulion.com'
    public static $SERVER_ARCH 	= [82, 84];
    const SERVER_START 	= 200;//80
    const SERVER_END 	= 220;//170

    const GAME_NOT_STARTED      = 0;
    const GAME_IS_RUNNING       = 1;
    const GAME_URL_FOUND        = 2;
    const GAME_URL_EXISTS       = 3;
    const GAME_URL_NOT_FOUND    = 4;
    const GAME_FILE_EXISTS      = 5;
    const GAME_STREAMING        = 6;
    const GAME_MD5_NOT_FOUND    = 7;

    protected $nflProvider;

    public function __construct(NflProviderInterface $provider)
    {
        $this->nflProvider = $provider;
    }

    public function init($year, $week, $type, $conds, $qlty)
    {
        if ($year) {
            $this->year = $year;
        } else {
            $this->year = date("Y", strtotime($this->container->getParameter("nfl_kick_off")));
        }
        if ($week) {
            $this->week = $week;
        } else {
            $datediff = time() - strtotime($this->container->getParameter("nfl_kick_off"));

            $this->week = floor($datediff / (60 * 60 * 24 * 7));
            $this->week++;
        }
        $this->type     = $type;
        $this->conds    = $conds;
        $this->qlty     = $qlty;

        $this->setGameOptions();
    }

    public function getGames($sort = false)
    {
        $games = array();
        $scores = $this->getScoresXML();

        if (is_null($scores)) {
            return;
        }

        if (isset($scores->type) && trim($scores->type) !== "") {
            $this->type = strtolower($scores->type);
        }

        foreach ($scores->games->game as $game) {
            $day     = strtotime($game['d']);
            $away    = strtolower($game->away['id']);
            $home    = strtolower($game->home['id']);

            $gindex = 2;
            switch ($this->type) {
                case "pre":
                    $gindex = 1;
                    break;
                case "post":
                case "pro":
                    $gindex = 3;
                    break;
                case "reg":
                default:
                    $gindex = 2;
            };

            $game_id = sprintf("%s/%d/%d_%d_%s_%s_%d_h_%s"
                , date('Y/m/d', $day)
                , $game['id']
                , $gindex
                , $game['id']
                , $away
                , $home
                , $this->year
                , $this->conds ? 'snap2w' : 'whole'
//            , $this->qlty
            );
            $gname = sprintf("NFL%d.W%02d.%s-%s.%s%s"
                , $this->year
                , $this->week
                , NflTeams::$teams[$away] ? NflTeams::$teams[$away]["name"] : $away
                , NflTeams::$teams[$home] ? NflTeams::$teams[$home]["name"] : $home
                , $this->resolution
                , $this->conds ? ".CG" : ""
            );
            $games[] = array(
                "d"         => $game['d'],
                "t"         => $game['t'],
                "time"      => $game['d']." ".$game['t'],

                "game_id"   => $game_id,
                "file_name" => $gname,

                "away"      => NflTeams::$teams[$away]["city"]." ".NflTeams::$teams[$away]["name"],
                "home"      => NflTeams::$teams[$home]["city"]." ".NflTeams::$teams[$home]["name"],
                "game"      => $away."@".$home,
                "id"        => intval($game["id"]),
                "elias"     => strtolower($game["elias"]),
                "score"     => sprintf("%d - %d", $game->away['p'], $game->home['p']),

                "logo_away" => NflTeams::$teams[$away]["logo"],
                "logo_home" => NflTeams::$teams[$home]["logo"],

                //video info
                "duration"  => null
            );
        }

        if ($sort) {
            usort($games, function ($a, $b) {

                //strcmp($a["game"], $b["game"]); //$a["id"] > $b["id"] ? 1 : -1;
                $atime = strtotime($a["time"]);
                $btime = strtotime($b["time"]);
                $cmp = strcmp($atime, $btime);
                if ($cmp == 0) {
                    return $a["elias"] > $b["elias"]
                        ? 1
                        : -1;
                } else {
                    return $cmp;
                }
            });
        }

        return $games;
    }

    public function getGameFileDir() {
        $dir = sprintf("%s/NFL%d.%s%02d.%s%s"
            , $this->container->getParameter("nfl_path")
            , $this->year
            , $this->type == "pre" ? "PS" : "W"
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

    public function searchGameUrl($game) {

        $currentDate = time();//mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $date = new \DateTime($game['time'], new \DateTimeZone("America/New_York"));
        $file = $this->getGameUriFile();
        $currentFile = file_get_contents($file);

        if ($currentDate < $date->getTimestamp()) {
            return self::GAME_NOT_STARTED;
        } elseif (round(($currentDate - $date->getTimestamp())/3600, 1) < 3) {
            return self::GAME_IS_RUNNING;
        } else {
            if (strpos($currentFile, $game['game_id']) > 0) {
                return self::GAME_URL_EXISTS;
            } else {
                $url = $this->findGameUrl(sprintf("%s_1_%d", $game['game_id'], $this->qlty));
                if (strlen($url) == 0) {
                    $url = $this->findGameUrl(sprintf("%s_2_%d", $game['game_id'], $this->qlty));
                }

                if (strlen($url) > 0) {
                    file_put_contents($file, $url."\r\n", FILE_APPEND);
                    return self::GAME_URL_FOUND;
                } else {
                    return self::GAME_URL_NOT_FOUND;
                }
            }
        }

    }

    public function streamGame(&$game, $shift = false, $getInfo = false) {
        $currentFile = file_get_contents($this->getGameUriFile());
        $dir         = $this->getGameFileDir();
        $mkv = sprintf(
            "%s/%s.mkv"
            , $dir
            , $game['file_name']
        );

        $pattern = preg_quote($game['game_id'], '/');
        // finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";

        if (preg_match_all($pattern, $currentFile, $matches)) {
            $url = implode("\n", $matches[0]);
            $url = trim(preg_replace('/\s+/', '', $url));

            if ($getInfo || !file_exists($mkv) || $shift) {
                //get md5
                $md5 = $this->nflProvider->getMD5($game['id']);
                if ($md5 == null) {
                    return self::GAME_MD5_NOT_FOUND;
                }
            }

            if ($getInfo) {
                //get video duration
                $this->getVideoInfo($game, $url . "?" . $md5);
            }

            if (!file_exists($mkv) || $shift) {
                if ($shift) {
                    Utils::stream(
                        $url . "?" . $md5
                        , sprintf("%s/%s2.mkv", $dir, $game['file_name'])
                        , $shift
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
                return self::GAME_STREAMING;
            } else {
                return self::GAME_FILE_EXISTS;
            }
        } else {
            return self::GAME_URL_NOT_FOUND;
        }
    }

    private function getVideoInfo(&$game, $url) {
        $info =  Utils::probe($url, $this->container->getParameter("nfl_ffmpeg"));

        //get video duration
        $search='/Duration: (.*?),/';
        preg_match($search, $info, $matches);
        $explode = explode('.', $matches[1]);

        $game["duration"] = $explode[0];
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

    private function findGameUrl($game_id, $server_start = null, $server_end = null) {
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
    }
}