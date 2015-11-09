<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 04.11.2015
 * Time: 11:52
 */

namespace NflBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerAware;

class RatingHandler extends ContainerAware
{
    public $year;
    public $week;

    public $start_date;
    public $end_date;

    const THUUZ_URL = "http://mobile.thuuz.com/2.3/games";

    public function init($year, $week) {
        $this->year = $year;
        $this->week = $week;

        $start = new \DateTime();
        $start->setISODate($this->year, date("W", strtotime($this->container->getParameter("nfl_kick_off"))) + $this->week - 1, 2);

        $this->start_date = $start
            ->format("Y-m-d H:m:s")
        ;

        $this->end_date = $start
            ->modify("+7 days")
            ->format("Y-m-d H:m:s")
        ;
    }

    public function getRating() {
        $res = array(
            "finished"      => array(),
            "future"        => array(),
            "start_date"    => $this->start_date,
            "end_date"      => $this->end_date,
        );

        $finished = $this->getRatingJson(2);
        foreach ($finished->ratings as $game) {
            $res["finished"][] = array(
                "away_team"     => $game->away_team,
                "home_team"     => $game->home_team,
                "gex"           => $game->gex,
                "gex_predict"   => $game->gex_predict
            );
        }
        $future = $this->getRatingJson(4);
        usort($future->ratings, array($this, 'ratingsSortByGexPredict'));

        foreach ($future->ratings as $game) {
            $res["future"][] = array(
                "away_team"     => $game->away_team,
                "home_team"     => $game->home_team,
                "gex_predict"   => $game->gex_predict
            );
        }
        return $res;
    }

    private function getRatingJson($status) {
        $fields = array(
            'type'         => "normal",
            'league_ids'   => 3,
            'status'       => $status,
            'auth_code'    => "B963EB9EB46B0007",
            'start_dt'     => rawurlencode($this->start_date),
            'end_dt'       => rawurlencode($this->end_date)
        );
        $fields_string = "";
        foreach($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        $json = Utils::download(self::THUUZ_URL."?".$fields_string);
        return json_decode($json);
    }

    private function ratingsSortByGexPredict($a,$b) {
        $agex = intval($a->gex_predict);
        $bgex = intval($b->gex_predict);
        if ($agex == $bgex) {
            return 0;
        }
        return ($agex < $bgex) ? 1 : -1;
    }

    private function ratingsSortByDate($a,$b) {
        $adate = strtotime($a->date);
        $bdate = strtotime($b->date);
        if ($adate == $bdate) {
            return 0;
        }
        return ($adate < $bdate) ? -1 : 1;
    }
}