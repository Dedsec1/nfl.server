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
    public function getGameMD5($gameId, $type) {
        print_r("getGameMD5  \r\n");
        $res = $this->getWatchUrl($gameId, $type);

        //print_r($res);
        if ($res != null) {
            $pos = strpos($res, "m3u8?");
            if ($pos !== false) {
                return substr($res, $pos + 5);
            } else {
                //print_r($res."\r\n");
                print_r("WRONG RESPONSE  \r\n");
                return null;
            }
        } else
            return null;
    }

    public function getGameUrl($gameId, $type, $qty) {
        print_r("getGameUrl  \r\n");
        $res = $this->getWatchUrl($gameId, $type);

        //print_r($res);
        if ($res != null) {
            $pos = strpos($res, "m3u8?");
            if ($pos !== false) {
                $res = substr($res, 0, $pos + 4);
                return str_replace("ipad", $qty, $res);
            } else {
                print_r("WRONG RESPONSE  \r\n");
                //print_r($res."\r\n");
                return null;
            }
        } else
            return null;
    }

    public function login(&$cookie, $print = false) {
/*
        $token = null;
        $pattern = "/Set-Cookie: (.*?);/is";
        $loginForm = Utils::sendGetRequest("http://nfl2go.com/Account/Login", array(), array(
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36"
            , "Accept-Encoding: gzip, deflate"
            , "Host: nfl2go.com"
            , "X-Requested-With: XMLHttpRequest"
        ));
        preg_match_all($pattern, $loginForm['header'], $matches);
        array_shift($matches);

        $cookie =  implode(";", $matches[0]);

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
                    "Username"                   => $this->container->getParameter("nfl2go_login"),
                    "Password"                   => $this->container->getParameter("nfl2go_pass")
                )
                , array(
                    "X-Requested-With: XMLHttpRequest"
                    , "Origin: https://nfl2go.com"
                    , "Referer: https://nfl2go.com/Account/Login"
                    , "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36"
                    , "Content-Type: application/x-www-form-urlencoded"
                )
                , $cookie
                , $print
            );

            if ($res['body'] === "Ok:") {
                preg_match_all($pattern, $res['header'], $matches);
                array_shift($matches);

                $cookie .= ";" . implode(";", $matches[0]);
                file_put_contents(
                    sprintf("%s/%s/nfl2go_cookie.txt"
                        , $this->container->getParameter("nfl_path")
                        , $this->container->getParameter("nfl_data_dir")
                    ), $cookie
                );
                return true;
            } else {
                return false;
            }
        }
*/
        print_r("!!!!!Try to get new COOKIE!!!! \r\n");
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
            print_r("SENDING WATCH REQUEST 1 \r\n");
            $url = $this->sendWatchRequest($gameId, $type, $cookie);
        }

        if (strpos($url['body'], "m3u8?") === false) {
           if ($this->getCookie($url, $cookie)) {
               print_r("SENDING WATCH REQUEST 2 \r\n");
               $url = $this->sendWatchRequest($gameId, $type, $cookie);
           }
        }

        return $url['body'];
/*
        if (($url == null) || ($cookie = null)) {
            $this->login($cookie);
            if ($cookie != null) {
                print_r("SENDING WATCH REQUEST 2 \r\n");
                $url = $this->sendWatchRequest($gameId, $type, $cookie);
            }
        }

        return $url;
*/
    }

    private function getCookie($response, &$cookie) {
        print_r("CHECK IF NEW COOKIE \r\n");

        $pattern = "/Set-Cookie: (.*?);/is";
        $count = preg_match_all($pattern, $response['header'], $matches);
        if ($count > 0) {
            array_shift($matches);

            preg_match_all('/(.*?)=\s?(.*?)(;|$)/', $cookie, $values);
            $headers = array_combine(array_map('trim', $values[1]), $values[2]);

            foreach ($matches[0] as $value) {
                $temp = explode("=", $value);
                if ($temp[1] != "") {
                    $headers[$temp[0]] = $temp[1];
                }
            }
            $cookie = http_build_query($headers, '', ';');

            print_r($cookie."\r\n");
            file_put_contents(
                sprintf("%s/%s/nfl2go_cookie.txt"
                    , $this->container->getParameter("nfl_path")
                    , $this->container->getParameter("nfl_data_dir")
                ), $cookie
            );
            return true;
        } else {
            return false;
        }
    }

    private function sendWatchRequest($gameId, $type, $cookie) {
        $url =  Utils::sendPostRequest(
            "http://app.nfl2go.com/Player/Watch" //GetGame
            , array(
                'code'         => $gameId,
                'type'         => $type,
            )
            , array(
                 "Origin: http://app.nfl2go.com"
                , "Referer: http://app.nfl2go.com/Player"
                , "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36"
            )
            , $cookie
        );
        //print_r($url);

        if ($url["code"] == 200) {
            return $url;
        } else {
            return null;
        }
    }
}