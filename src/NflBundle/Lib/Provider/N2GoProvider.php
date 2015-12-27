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
    public function getMD5($gameId) {
        $md5 = null;

        $cookie = file_get_contents(
            sprintf("%s/%s/nfl2go_cookie.txt"
                , $this->container->getParameter("nfl_path")
                , $this->container->getParameter("nfl_data_dir")
            )
        );

        if ($cookie != null) {
            $md5 = $this->getN2GoMD5($cookie, $gameId);
        }

        if (($md5 == null) || ($cookie = null)) {
            $cookie = $this->getN2GoCookie();
            if ($cookie != null) {
                $md5 = $this->getN2GoMD5($cookie, $gameId);
                file_put_contents(
                    sprintf("%s/%s/nfl2go_cookie.txt"
                        , $this->container->getParameter("nfl_path")
                        , $this->container->getParameter("nfl_data_dir")
                ), $cookie);
            }
        }

        return $md5;
    }

    private function getN2GoMD5($cookie, $gameId) {
        if ($cookie != null) {
            $res = Utils::sendPostRequest(
                "http://app.nfl2go.com/Player/GetGame"
                , array(
                    'game'         => $gameId,
                    'type'         => "C",//$this->conds ? "C" : "A",
                )
                , $cookie
                , sprintf("%s/%s/cookie.txt"
                    , $this->container->getParameter("nfl_path")
                    , $this->container->getParameter("nfl_data_dir")
                )
            );
        }
        //print_r($res);
        $pos = strpos($res, "m3u8?");
        if ($pos !== false) {
            return substr($res, $pos + 5);
        } else
            return null;
    }

    private function getN2GoCookie() {
        $pattern = "/Set-Cookie: (.*?);/is";
        $loginForm = Utils::sendGetRequest("http://app.nfl2go.com/Account/Login");
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
                "http://app.nfl2go.com/Account/Login?ReturnUrl=%2F"
                , array(
                    "__RequestVerificationToken" => $token,
                    "Email"                      => "sbabych@gmail.com",
                    "Password"                   => "9Welcome!"
                )
                , $cookie
                , sprintf("%s/%s/cookie.txt"
                    , $this->container->getParameter("nfl_path")
                    , $this->container->getParameter("nfl_data_dir")
                )
            );

            preg_match_all($pattern, $res, $matches);
            array_shift($matches);


            return $cookie.";".implode(";", $matches[0]);

        } else
            return $cookie;
    }
}