<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 04.11.2015
 * Time: 15:00
 */

namespace NflBundle\Lib;


interface  NflProviderInterface
{
    public function getMD5($gameId);
}