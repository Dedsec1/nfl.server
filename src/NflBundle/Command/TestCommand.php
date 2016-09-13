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
        $cookie = "";
        $output->writeln("<comment>Checking login....</comment>");
        if ($this->nflHandler->checkLogin($cookie)) {
            $output->writeln("<fg=cyan>Succesfully logged in</>");
            $output->writeln("<info>".$cookie."</info>");
        } else {
            $output->writeln("<error>cannot login smth goes wrong</error>");
        }


        $output->writeln("<comment>Checking getting URL...</comment>");
        $url = $this->nflHandler->checkFindGameURL();
        if ($url != null) {
            $output->writeln("<fg=cyan>Found game URL succesfully</>");
            $output->writeln("<info>".$url."</info>");
        } else {
            $output->writeln("<error>cannot find game URL</error>");
        }
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