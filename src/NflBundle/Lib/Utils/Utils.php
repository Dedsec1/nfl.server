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
        CURLOPT_FOLLOWLOCATION  => 1,
        CURLINFO_HEADER_OUT     => true
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

    public static function addLogo($mkv, $logo, $tmpDir, $ffmpeg, $acodec)
    {
        $chunkFile  = sprintf("%s/logo.mkv", $tmpDir);
        $endFile    = sprintf("%s/game.mkv", $tmpDir);
        $outputFile = sprintf("%s/output.mkv", $tmpDir);

        if (file_exists($chunkFile)) {
            unlink($chunkFile);
        }
        if (file_exists($endFile)) {
            unlink($endFile);
        }
        if (file_exists($outputFile)) {
            unlink($outputFile);
        }

        $cmd = sprintf(
            "%s/ffmpeg -t 20 -i \"%s\" -i \"%s\" -filter_complex \"overlay=main_w-overlay_w-10:main_h-overlay_h-10\" -acodec copy %s"
            , $ffmpeg
            , $mkv
            , $logo
            , $chunkFile
        );
        shell_exec($cmd);

        $cmd = sprintf(
            "%s/ffmpeg -ss 21 -i \"%s\" -c:v copy -acodec copy %s"
            , $ffmpeg
            , $mkv
            , $endFile
        );
        shell_exec($cmd);


        $cmd = sprintf(
            "%s/ffmpeg -f concat -i %s/list.txt -c copy %s"
            , $ffmpeg
            , $tmpDir
            , $outputFile
        );
        shell_exec($cmd);


        if (file_exists($outputFile)) {
            unlink($mkv);
            rename($outputFile, $mkv);
        }
        if (file_exists($chunkFile)) {
            unlink($chunkFile);
        }
        if (file_exists($endFile)) {
            unlink($endFile);
        }

/*
        $cmd = sprintf(
            //"%s/ffmpeg -i \"%s\" -vf \"movie=%s [wm]; [in][wm] overlay=main_w-overlay_w-10:main_h-overlay_h-10:enable=between(t\\,0\\,30) [out]\" -acodec copy out.mkv"
            "%s/ffmpeg -i \"%s\" -i \"%s\" -filter_complex \"overlay=main_w-overlay_w-10:main_h-overlay_h-10:enable=between(t\\,0\\,30)\" %s"
            //"%s/ffmpeg -i chunk.mkv -i \"%s\" -filter_complex \"overlay=main_w-overlay_w-10:main_h-overlay_h-10\"  watermark.mkv"
            , $ffmpeg
            , $mkv
            , $logo
            , $chunkFile
        );

        shell_exec($cmd);
*/

/*
        if (strtoupper(substr(PHP_OS, 0, 3) === 'WIN')) {
            //print_r("WIN");
            pclose(popen(escapeshellcmd("start cmd.exe /K " . $cmd), "r"));
        } else {
            //print_r("Linux");
            exec($cmd);
        }
*/
    }

    public static function stream($url, $mkv, $shift = null, $ffmpeg, $acodec, $logo = ""){
        //print_r($url);
        if ($shift == null) {
            $cmd = sprintf("%s/ffmpeg -v info -stats -i \"%s\" -c:a %s %s \"%s\" " //-c:a libvo_aacenc
                , $ffmpeg
                , $url
                , $acodec
                , $logo == "" ?
                    "-c:v copy" :
                    "-vcodec libx264 -vf \"movie=".$logo." [wm]; [in][wm] overlay=main_w-overlay_w-10:main_h-overlay_h-10 [out]\" "
                , $mkv
            );
        } else {
            $cmd = sprintf("%s/ffmpeg -v info -stats  -ss %s -i \"%s\" -ss 0 -c:v copy -c:a %s \"%s\" " //-c:a libvo_aacenc
                , $ffmpeg
                , $shift
                , $url
                , $acodec
                , $mkv
            );
        }
        //print_r($cmd);

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

    public static function sendPostRequest($url, $fields = array(), $headers = array(), $cookie = null, $print = false)
    {
        $fields_string = http_build_query($fields, '', '&');

        $ch = curl_init($url);
        curl_setopt_array($ch, self::$curl_options);
        curl_setopt_array($ch, array(
            CURLOPT_HEADER      => true,
            CURLOPT_POST        => 1,
            CURLOPT_POSTFIELDS  => $fields_string,
            CURLOPT_HTTPHEADER  => $headers
        ));
        if ($cookie != null) {
            curl_setopt_array($ch, array(
                CURLOPT_COOKIESESSION   => true,
                CURLOPT_COOKIE          => $cookie
            ));
        }

        $retValue = curl_exec($ch);
        if ($print) {
            print_r("POST request to ".$url." sent \r\n");
            print_r("request fields are:  ".$fields_string."\r\n");
            print_r("cookie are:  ".$cookie."\r\n");
            //print_r($retValue."\r\n");

            $sentHeaders = curl_getinfo($ch);
            print_r($sentHeaders);
        }

        // Check for errors and display the error message
/*
        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
*/
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($retValue, 0, $header_size);
        $body = substr($retValue, $header_size);

        curl_close($ch);

        return array(
            "code"   => $code,
            "header" => $header,
            "body"   => $body
        );
    }

    public static function sendGetRequest($url, $fields = array(), $headers = array(), $cookie = null, $cookiejar = null) {
        $fields_string = http_build_query($fields, '', '&');

        $ch = curl_init();
        curl_setopt_array($ch, self::$curl_options);
        curl_setopt_array($ch, array(
            CURLOPT_HEADER          => true,
            CURLOPT_URL             => $url."?".$fields_string,
            CURLOPT_HTTPHEADER      => $headers
        ));

        if ($cookie != null) {
            curl_setopt_array($ch, array(
                CURLOPT_COOKIESESSION   => true,
                CURLOPT_COOKIE          => $cookie
            ));
        }
        //print_r("GET (".$url.") request send \r\n");
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