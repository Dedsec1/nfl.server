<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 04.11.2015
 * Time: 15:02
 */

namespace NflBundle\Lib\Provider;

use Symfony\Component\DependencyInjection\ContainerAware;
use NflBundle\Lib\Utils\Utils;

class N2GoProvider extends ContainerAware implements NflProviderInterface
{
    public function getGameMD5($gameId) {
        $res = $this->getWatchUrl($gameId, "C");

        //print_r($res);
        if ($res != null) {
            $pos = strpos($res, "m3u8?");
            if ($pos !== false) {
                return substr($res, $pos + 5);
            } else {
                return null;
            }
        } else
            return null;
    }

    public function getGameUrl($gameId, $type, $qty) {
        $res = $this->getWatchUrl($gameId, $type);

        //print_r($res);
        if ($res != null) {
            $pos = strpos($res, "m3u8?");
            if ($pos !== false) {
                $res = substr($res, 0, $pos + 4);
                return str_replace("ipad", $qty, $res);
            } else {
                return null;
            }
        } else
            return null;
    }

    private function getWatchUrl($gameId, $type) {
        $url = null;

        $cookie = file_get_contents(
            sprintf("%s/%s/nfl2go_cookie.txt"
                , $this->container->getParameter("nfl_path")
                , $this->container->getParameter("nfl_data_dir")
            )
        );

        if ($cookie != null) {
            $url = $this->sendWatchRequest($gameId, $type, $cookie);
        }


        if (($url == null) || ($cookie = null)) {

            $cookie = $this->getCookie();
            //print_r($cookie);
            if ($cookie != null) {
                $url = $this->sendWatchRequest($gameId, $type, $cookie);
                file_put_contents(
                    sprintf("%s/%s/nfl2go_cookie.txt"
                        , $this->container->getParameter("nfl_path")
                        , $this->container->getParameter("nfl_data_dir")
                    ), $cookie);
            }
        }

        return $url;
    }

    private function sendWatchRequest($gameId, $type, $cookie) {
        $url =  Utils::sendPostRequest(
            "http://app.nfl2go.com/Player/Watch" //GetGame
            , array(
                'code'         => $gameId,
                'type'         => $type,
            )
            , array()
            , $cookie
            , sprintf("%s/%s/cookie.txt"
                , $this->container->getParameter("nfl_path")
                , $this->container->getParameter("nfl_data_dir")
            )
        );
        //print_r($url);

        if (strpos($url['body'], "m3u8?") === false) {
            return null;
        } else
            return $url['body'];
    }

    private function getCookie() {
        $token = null;
        $pattern = "/Set-Cookie: (.*?);/is";
        $loginForm = Utils::sendGetRequest("http://nfl2go.com/Account/Login");
        preg_match_all($pattern, $loginForm['header'], $matches);
        array_shift($matches);

        $cookie =  implode("\n", $matches[0]);


        $DOM = new \DOMDocument();
        $DOM->loadHTML($loginForm['body']);

        $inputs = $DOM->getElementsByTagName("input");
        foreach( $inputs as $node ) {
            $name = $node->getAttribute('name');
            if ($name == "__RequestVerificationToken") {
                $token = $node->getAttribute('value');
            }
        }

        if ($token != null) {
            $res = Utils::sendPostRequest(
                "https://nfl2go.com/Account/Login"
                , array(
                    "__RequestVerificationToken" => $token,
                    "Username"                   => "sbabych@gmail.com",
                    "Password"                   => "256Welcome!"
                )
                , array(
                    "Origin: https://nfl2go.com",
                    "Referer: https://nfl2go.com/Account/Login",
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko"
                )
                , $cookie
                , sprintf("%s/%s/cookie.txt"
                    , $this->container->getParameter("nfl_path")
                    , $this->container->getParameter("nfl_data_dir")
                )
            );
            preg_match_all($pattern, $res['header'], $matches);
            array_shift($matches);

            return $cookie.";".implode(";", $matches[0]);

        } else
            return $cookie;
    }
}