<?php
/**Successfully*/

namespace NflBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use NflBundle\Lib\Utils\Utils;

class TestCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:test')
            ->setDescription('Test')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
/*
        $cookie = "";
        $output->writeln("<comment>Checking login....</comment>");
        if ($this->nflHandler->checkLogin($cookie)) {
            $output->writeln("<fg=cyan>Succesfully logged in</>");
            $output->writeln("<info>".$cookie."</info>");
        } else {
            $output->writeln("<error>cannot login smth goes wrong</error>");
        }
*/
        $output->writeln("<comment>Checking getting URL...</comment>");
        $url = $this->nflHandler->checkFindGameURL();
        if ($url != null) {
            $output->writeln("<fg=cyan>Found game URL succesfully</>");
            $output->writeln("<info>".$url."</info>");
        } else {
            $output->writeln("<error>cannot find game URL</error>");
        }

/*
        $cookie = "__RequestVerificationToken=7u37mOLMFXIZSwEYQLJx4xOQI_IkXwGyu5x39IfXaCO9SH6WiDoLF0WypP8SDvPgZyVc42O3Kjsg2ENlDvsWOz7QgG0D3h7d8YgtavuFJTs1; globalquality=9; Scores=0; Setting_ScreensX=2; Setting_ScreensY=2; Setting_MosaicQuality=6; Setting_MosaicPlayer=2; Setting_MosaicSet=1; Setting_Quality=9; ASP.NET_SessionId=jnyrs1zkmuknil2giyg2lv2c; Setting_HideScores=true; .AspNet.ApplicationCookie=NOAjYsFjUVVXjhp3img2vkyi2sPwJjUWtllz1l1jtubH3BvMgvccxGYLkmSpYGUfw6laPhDyP76aMsa9Gq0FTAGAAXEHNnUYHDrJfLirOZBLX0DlkQh4pcOjSYl0BdSPxCrdRiW920W8zA2wDXDlWPfFVbIiwU9eMHsp7jHSb73VEHma5G0zJyVrNMPrnNB8Hqt0WHtl9UpeaLPybQmMKuuK_kyYewVyx27ffAP9bdesDpXQMQwRlAg5FKrb0QB7Vl94NHP0BfAwUTDIA9faeXJBdEhvhw2PBBsiFVlzUghWhrtYZqhfta9pEXSjSHwgFearqIXHnny_k8baN5o1yrr5QgZQe84iipGWCLlrS7YgCnGlh1z4WIFGB7XgWeYXiXNyBmvk6NrlJiYWTNv5-pmugjWdpsO1YYwXgbEeqNFXoKuMxcBdqWWaWAMEQu_3k4BYPCbKm4yexw-hpjm-kNfAzjRepm_VwY-LKa68wKdE1cNjypUrWCI-ORxxQXEk46Qk4eLgu8l-OF5pW7wAHHlH_eoCorKs1iDn4UQzOPs";
        $new = array(
            array(".AspNet.ApplicationCookie=", ".AspNet.ApplicationCookie=", ".AspNet.ApplicationCookie=9KQP9VUuvg3Ln7Ulu2MFpDXimqhk9ee1xW-skulvLnrh5hRwQa4o-Dq-HAmYQtId8PKCJPxHv1B1LVInGnpCuKTMGor8GzNPumu0wIhomZ8nXnnwfHuSvuwsWOMkao_W6-_1z-t4eRI-yzj_G4NmHbEg41BjsHoIvpFXhFqxH9EsXGHnFNS9UOHIg6sSlS757HfRD9J5QPY3Ph2BUxIIZJ01XXBPpWLExuZzU9q1ij29eeMuNlpSGDqSVK6xfojOPaXLgnFhhyh9hj3bpPAZXcDmGZS34O5ZfUyAhzddA6qsXxs3IPvErJXFy84QfUQB70k1vLjx9WoXQAtCGprtnVJB9hjFb-tQDc9uJ5etWVt_G6iWIo0n5xWsd0u_m1960EXaXlJgoYGv5LBpx_QVIuuztHNEM3lGUa7msm3k8DY9dG5TugnWv6dUrRv0NrqIEu9P8DxQ_zVn-kG8ARoYnl8jyq19JjGQNUgXPc8O0DZ_pBbaHx1FLL_4RAY6sNGIWDgxNtUm0UHOtJ7oStjWE9BOlGnVcDzeWc_7njofS5g")
        );
        print_r($new);
        preg_match_all('/(.*?)=\s?(.*?)(;|$)/', $cookie, $matches);
        $headers = array_combine(array_map('trim', $matches[1]), $matches[2]);
        print_r($headers);

        foreach ($new[0] as $value) {
            $temp = explode("=", $value);
            print_r($temp);
            if ($temp[1] != "") {
                $headers[$temp[0]] = $temp[1];
            }
        }
        print_r($headers);

        $cookie = http_build_query($headers, '', ';');
        print_r($cookie);
*/
/*
        $token = null;
        $pattern = "/Set-Cookie: (.*?);/is";
        $loginForm = Utils::sendGetRequest("http://nfl2go.com/Account/Login", array(), array(
              "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko"
            , "Accept-Encoding: gzip, deflate"
            , "Host: nfl2go.com"
            , "X-Requested-With: XMLHttpRequest"
        ));
        preg_match_all($pattern, $loginForm['header'], $matches);
        array_shift($matches);


        $cookie = implode(";", $matches[0]);

        $DOM = new \DOMDocument();
        $DOM->loadHTML($loginForm['body']);

        $inputs = $DOM->getElementsByTagName("input");
        foreach( $inputs as $node ) {
            $name = $node->getAttribute('name');
            if ($name == "__RequestVerificationToken") {
                $token = $node->getAttribute('value');
            }
        }


        //$cookie = "ASP.NET_SessionId=f1tvzzusm1w0cm5qz0zosjjp;ASP.NET_SessionId=f1tvzzusm1w0cm5qz0zosjjp;__RequestVerificationToken=2XW4NYhkBI8-OloBoaZdEa8F9LLZcaXHfcWOs9A8gIGlNRkyh0sJucO8iq9YKZJk0R7xxTMEFlkEhXYDgaY7XBoEA_jJjxGn-LxFGV8R56g1";
        //$token  = "1299u3KBq0YeTes0RfiMDkdMqXwcCOVjZSwjT_NTFmuq1hNkDpdzrsF4UrzT-izjyzPVvh7o4cewq-fjxkeIbX1boLvGjFWntlyOWfte9sE1";

        $fields_string = http_build_query(array(
            "Username"                   => "sbabych@gmail.com",
            "Password"                   => "256Welcome!",
            "__RequestVerificationToken" => $token

        ), '', '&');
        print_r($fields_string);
        print_r("\r\n");
        print_r("\r\n");
        print_r("\r\n");

        $ch = curl_init("https://nfl2go.com/Account/Login");
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER  => FALSE,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $fields_string,
            CURLOPT_HEADER          => true,
            CURLOPT_COOKIESESSION   => true,
            CURLOPT_COOKIE          => $cookie,
            CURLINFO_HEADER_OUT     => true,
            CURLOPT_HTTPHEADER      => array(
                "X-Requested-With: XMLHttpRequest"
                , "Origin: https://nfl2go.com"
                , "Referer: https://nfl2go.com/Account/Login"
                , "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko"
                , "Content-Type: application/x-www-form-urlencoded"
            )
        ));
        $retValue = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($retValue, 0, $header_size);
        $body = substr($retValue, $header_size);

        $headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
        print_r($headerSent);
        print_r("\r\n");
        print_r("\r\n");
        print_r("\r\n");
        print_r($header);
        print_r("\r\n");
        print_r($body);
        curl_close($ch);

*/
        return 0;
    }

}