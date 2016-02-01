<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 16:39
 */

namespace NflBundle\Lib\Utils;


class NflTeams
{
    public static $teams = array(
        "ari" => [
            "city"     => "Arizona",
            "name"     => "Cardinals",
            "id"       => "ARI",
            "statCode" => "ARZ",
            "statId"   => "3800",
            "color"    => 11011642,
            "logo"     => "http://funkyimg.com/i/MQU7.jpg"
        ],
        "atl" => [
            "city"     => "Atlanta",
            "name"     => "Falcons",
            "id"       => "ATL",
            "statCode" => "ATL",
            "statId"   => "0200",
            "color"    => 11736102,
            "logo"     => "http://funkyimg.com/i/MQUe.jpg"
        ],
        "bal" => [
            "city"     => "Baltimore",
            "name"     => "Ravens",
            "id"       => "BAL",
            "statCode" => "BLT",
            "statId"   => "0325",
            "color"    => 2039655,
            "logo"     => "http://funkyimg.com/i/MQUp.jpg"
        ],
        "buf" => [
            "city"     => "Buffalo",
            "name"     => "Bills",
            "id"       => "BUF",
            "statCode" => "BUF",
            "statId"   => "0610",
            "color"    => 13374232,
            "logo"     => "http://funkyimg.com/i/MQU3.jpg"
        ],
        "car" => [
            "city"     => "Carolina",
            "name"     => "Panthers",
            "id"       => "CAR",
            "statCode" => "CAR",
            "statId"   => "0750",
            "color"    => 35280,
            "logo"     => "http://funkyimg.com/i/MQUk.jpg"
        ],
        "chi" => [
            "city"     => "Chicago",
            "name"     => "Bears",
            "id"       => "CHI",
            "statCode" => "CHI",
            "statId"   => "0810",
            "color"    => 15546130,
            "logo"     => "http://funkyimg.com/i/MQU1.jpg"
        ],
        "cin" => [
            "city"     => "Cincinnati",
            "name"     => "Bengals",
            "id"       => "CIN",
            "statCode" => "CIN",
            "statId"   => "0920",
            "color"    => 15551274,
            "logo"     => "http://funkyimg.com/i/MQU2.jpg"
        ],
        "cle" => [
            "city"     => "Cleveland",
            "name"     => "Browns",
            "id"       => "CLE",
            "statCode" => "CLV",
            "statId"   => "1050",
            "color"    => 15948057,
            "logo"     => "http://funkyimg.com/i/MQU5.jpg"
        ],
        "dal" => [
            "city"     => "Dallas",
            "name"     => "Cowboys",
            "id"       => "DAL",
            "statCode" => "DAL",
            "statId"   => "1200",
            "color"    => 141917,
            "logo"     => "http://funkyimg.com/i/MQUb.jpg"
        ],
        "den" => [
            "city"     => "Denver",
            "name"     => "Broncos",
            "id"       => "DEN",
            "statCode" => "DEN",
            "statId"   => "1400",
            "color"    => 15547157,
            "logo"     => "http://funkyimg.com/i/MQU4.jpg"
        ],
        "det" => [
            "city"     => "Detroit",
            "name"     => "Lions",
            "id"       => "DET",
            "statCode" => "DET",
            "statId"   => "1540",
            "color"    => 880307,
            "logo"     => "http://funkyimg.com/i/MQUi.jpg"
        ],
        "gb" => [
            "city"     => "Green Bay",
            "name"     => "Packers",
            "id"       => "GB",
            "statCode" => "GB",
            "statId"   => "1800",
            "color"    => 1648675,
            "logo"     => "http://funkyimg.com/i/MQUj.jpg"
        ],
        "hou" => [
            "city"     => "Houston",
            "name"     => "Texans",
            "id"       => "HOU",
            "statCode" => "HST",
            "statId"   => "2120",
            "color"    => 3387,
            "logo"     => "http://funkyimg.com/i/MQUu.jpg"
        ],
        "ind" => [
            "city"     => "Indianapolis",
            "name"     => "Colts",
            "id"       => "IND",
            "statCode" => "IND",
            "statId"   => "2200",
            "color"    => 10577,
            "logo"     => "http://funkyimg.com/i/MQUa.jpg"
        ],
        "jac" => [
            "city"     => "Jacksonville",
            "name"     => "Jaguars",
            "id"       => "JAC",
            "statCode" => "JAX",
            "statId"   => "2250",
            "color"    => 13475880,
            "logo"     => "http://funkyimg.com/i/MQUg.jpg"
        ],
        "kc" => [
            "city"     => "Kansas City",
            "name"     => "Chiefs",
            "id"       => "KC",
            "statCode" => "KC",
            "statId"   => "2310",
            "color"    => 11146788,
            "logo"     => "http://funkyimg.com/i/MQU9.jpg"
        ],
        "mia" => [
            "city"     => "Miami",
            "name"     => "Dolphins",
            "id"       => "MIA",
            "statCode" => "MIA",
            "statId"   => "2700",
            "color"    => 18762,
            "logo"     => "http://funkyimg.com/i/MQUc.jpg"
        ],
        "min" => [
            "city"     => "Minnesota",
            "name"     => "Vikings",
            "id"       => "MIN",
            "statCode" => "MIN",
            "statId"   => "3000",
            "color"    => 16496673,
            "logo"     => "http://funkyimg.com/i/MQUw.jpg"
        ],
        "ne" => [
            "city"     => "New England",
            "name"     => "Patriots",
            "id"       => "NE",
            "statCode" => "NE",
            "statId"   => "3200",
            "color"    => 12456471,
            "logo"     => "http://funkyimg.com/i/MQUm.jpg"
        ],
        "no" => [
            "city"     => "New Orleans",
            "name"     => "Saints",
            "id"       => "NO",
            "statCode" => "NO",
            "statId"   => "3300",
            "color"    => 11568449,
            "logo"     => "http://funkyimg.com/i/MQUr.jpg"
        ],
        "nyg" => [
            "city"     => "New York",
            "name"     => "Giants",
            "id"       => "NYG",
            "statCode" => "NYG",
            "statId"   => "3410",
            "color"    => 9289,
            "cityName" => "NY Giants",
            "logo"     => "http://funkyimg.com/i/MQUf.jpg"
        ],
        "nyj" => [
            "city"     => "New York",
            "name"     => "Jets",
            "id"       => "NYJ",
            "statCode" => "NYJ",
            "statId"   => "3430",
            "color"    => 1589030,
            "cityName" => "NY Jets",
            "logo"     => "http://funkyimg.com/i/MQUh.jpg"
        ],
        "oak" => [
            "city"     => "Oakland",
            "name"     => "Raiders",
            "id"       => "OAK",
            "statCode" => "OAK",
            "statId"   => "2520",
            "color"    => 11776947,
            "logo"     => "http://funkyimg.com/i/MQUn.jpg"
        ],
        "phi" => [
            "city"     => "Philadelphia",
            "name"     => "Eagles",
            "id"       => "PHI",
            "statCode" => "PHI",
            "statId"   => "3700",
            "color"    => 142129,
            "logo"     => "http://funkyimg.com/i/MQUd.jpg"
        ],
        "pit" => [
            "city"     => "Pittsburgh",
            "name"     => "Steelers",
            "id"       => "PIT",
            "statCode" => "PIT",
            "statId"   => "3900",
            "color"    => 16756753,
            "logo"     => "http://funkyimg.com/i/MQUt.jpg"
        ],
        "sd" => [
            "city"     => "San Diego",
            "name"     => "Chargers",
            "id"       => "SD",
            "statCode" => "SD",
            "statId"   => "4400",
            "color"    => 16563220,
            "logo"     => "http://funkyimg.com/i/MQU8.jpg"
        ],
        "sf" => [
            "city"     => "San Francisco",
            "name"     => "49ers",
            "id"       => "SF",
            "statCode" => "SF",
            "statId"   => "4500",
            "color"    => 11897932,
            "logo"     => "http://funkyimg.com/i/MQTZ.jpg"
        ],
        "sea" => [
            "city"     => "Seattle",
            "name"     => "Seahawks",
            "id"       => "SEA",
            "statCode" => "SEA",
            "statId"   => "4600",
            "color"    => 1849940,
            "logo"     => "http://funkyimg.com/i/MQUs.jpg"
        ],
        "stl" => [
            "city"     => "St. Louis",
            "name"     => "Rams",
            "id"       => "STL",
            "statCode" => "SL",
            "statId"   => "2510",
            "color"    => 5942,
            "logo"     => "http://funkyimg.com/i/MQUo.jpg"
        ],
        "tb" => [
            "city"     => "Tampa Bay",
            "name"     => "Buccaneers",
            "id"       => "TB",
            "statCode" => "TB",
            "statId"   => "4900",
            "color"    => 12854592,
            "logo"     => "http://funkyimg.com/i/MQU6.jpg"
        ],
        "ten" => [
            "city"     => "Tennessee",
            "name"     => "Titans",
            "id"       => "TEN",
            "statCode" => "TEN",
            "statId"   => "2100",
            "color"    => 4423360,
            "logo"     => "http://funkyimg.com/i/MQUv.jpg"
        ],
        "was" => [
            "city"     => "Washington",
            "name"     => "Redskins",
            "id"       => "WAS",
            "statCode" => "WAS",
            "statId"   => "5110",
            "color"    => 6293792,
            "logo"     => "http://funkyimg.com/i/MQUq.jpg"
        ],
        "afc" => [
            "city"     => "AFC",
            "name"     => "",
            "id"       => "AFC",
            "logoId"   => "AFC",
            "statCode" => "AFC",
            "statId"   => "8600",
            "color"    => 10363184,
            "logo"     => ""
        ],
        "nfc" => [
            "city"     => "NFC",
            "name"     => "",
            "id"       => "NFC",
            "logoId"   => "NFC",
            "statCode" => "NFC",
            "statId"   => "8700",
            "color"    => 11867,
            "logo"     => ""
        ],
        "crt" => [
            "city"     => "",
            "name"     => "Team Carter",
            "id"       => "CRT",
            "logoId"   => "CRT",
            "statCode" => "CRT",
            "statId"   => "8600",
            "color"    => 10363184,
            "logo"     => ""
        ],
        "irv" => [
            "city"     => "",
            "name"     => "Team Irvin",
            "id"       => "IRV",
            "logoId"   => "IRV",
            "statCode" => "IRV",
            "statId"   => "8700",
            "color"    => 11867,
            "logo"     => ""
        ],
        "ric" => [
            "city"     => "",
            "name"     => "Team Rice",
            "id"       => "RIC",
            "logoId"   => "RIC",
            "statCode" => "RIC",
            "statId"   => "8700",
            "color"    => 11867,
            "logo"     => ""
        ]
    );
}