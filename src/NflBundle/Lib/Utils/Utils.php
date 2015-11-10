<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 16:39
 */

namespace NflBundle\Lib\Utils;


class Utils
{
    public static $curl_options = array(
        CURLOPT_SSL_VERIFYPEER  => FALSE,
        CURLOPT_SSL_VERIFYHOST  => FALSE,
        CURLOPT_RETURNTRANSFER  => 1,
        CURLOPT_CONNECTTIMEOUT  => 15,
        CURLOPT_FAILONERROR     => 1,
        CURLOPT_FOLLOWLOCATION  => 1
    );

    public static function download($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, self::$curl_options);

        $retValue = curl_exec($ch);
        curl_close($ch);
        return $retValue;
    }

    public static function sendMultiRequests($urls) {
        $result = "";
        $chs  = array();
        $mh   = curl_multi_init();
        try {
            foreach ($urls as $url) {
                $chs[$url] = curl_init($url);
                curl_setopt_array($chs[$url], self::$curl_options);
                curl_multi_add_handle($mh, $chs[$url]);
            }

            $running = 0;
            do {
                curl_multi_exec($mh, $running);
            } while ($running > 0);
        } catch (Exception $e) {
            print_r($e->getMessage());
        } finally {
            foreach ($chs as $url => $curl) {
                $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($info == 200) {
                    $result = $url;
                }
                curl_multi_remove_handle($mh, $curl);
            }
            curl_multi_close($mh);
        }
        return $result;
    }

    public static function stream($url, $mkv, $shift = null, $ffmpeg, $acodec){
        //print_r($url);
        if ($shift == null) {
            $cmd = sprintf("%s/ffmpeg -i \"%s\" -c copy -c:a %s \"%s\" " //-c:a libvo_aacenc
                , $ffmpeg
                , $url
                , $acodec
                , $mkv
            );
        } else {
            $cmd = sprintf("%s/ffmpeg -ss %s -i \"%s\" -ss 0 -c copy -c:a %s \"%s\" " //-c:a libvo_aacenc
                , $ffmpeg
                , $shift
                , $url
                , $acodec
                , $mkv
            );
        }
//        print_r($cmd);

        if (strtoupper(substr(PHP_OS, 0, 3) === 'WIN')) {
            //print_r("WIN");
            pclose(popen(escapeshellcmd("start cmd.exe /K " . $cmd), "r"));
        } else {
            //print_r("Linux");
            exec($cmd);
        }
    }

    public static function probe($url, $ffmpeg) {
        $cmd = sprintf("%s/ffprobe \"%s\" 2>&1"
            , $ffmpeg
            , $url
        );
        return shell_exec($cmd);
    }

    public static function sendPostRequest($url, $fields = array(), $cookie = null, $cookiejar) {
        $fields_string = "";
        foreach($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        $ch = curl_init($url);
        curl_setopt_array($ch, self::$curl_options);
        curl_setopt_array($ch, array(
            CURLOPT_HEADER          => true,
            CURLOPT_POST            => count($fields),
            CURLOPT_POSTFIELDS      => $fields_string
        ));
        if ($cookie != null) {
            curl_setopt_array($ch, array(
                CURLOPT_COOKIESESSION   => true,
                CURLOPT_COOKIE          => $cookie,
                CURLOPT_COOKIEJAR       => $cookiejar
            ));
        }

        $retValue = curl_exec($ch);

        // Check for errors and display the error message
/*
        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
*/
        curl_close($ch);

        return $retValue;
    }

    public static function sendGetRequest($url, $fields = array(), $cookie = null, $cookiejar = null) {
        $fields_string = "";
        foreach($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string, '&');

        $ch = curl_init();
        curl_setopt_array($ch, self::$curl_options);
        curl_setopt_array($ch, array(
            CURLOPT_HEADER          => true,
            // CURLOPT_HTTPHEADER      => array("Content-Type: application/x-www-form-urlencoded; charset=UTF-8", "Host: nfl2go.com:2015"),
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5',
            CURLOPT_URL             => $url."?".$fields_string
        ));

        if ($cookie != null) {
            curl_setopt_array($ch, array(
                CURLOPT_COOKIESESSION   => true,
                CURLOPT_COOKIE          => $cookie,
                CURLOPT_COOKIEJAR       => $cookiejar
            ));
        }

        $retValue = curl_exec($ch);

        // Check for errors and display the error message
/*
        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
*/

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($retValue, 0, $header_size);
        $body = substr($retValue, $header_size);

        curl_close($ch);
        return array(
            "header" => $header,
            "body"   => $body
        );
    }
}