<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 16:53
 */

namespace NflBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerAware;

class NflHandler extends ContainerAware
{
    public $year;
    public $week;
    public $type     = 'reg'; //post //pre //pro
    public $conds    = true;
    public $qlty     = 3000; //4500

    public $resolution;
    public $playoff;

    public function init($year, $week, $type, $conds, $qlty)
    {
        $this->year = $year;
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
}