<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 04.11.2015
 * Time: 15:00
 */

namespace NflBundle\Lib\Provider;


interface  NflProviderInterface
{
    public function getGameMD5($gameId);

    public function getGameUrl($gameId, $type, $qty);

    public function login(&$cookie, $print = false);

}