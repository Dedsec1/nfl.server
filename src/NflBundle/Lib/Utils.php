<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 03.11.2015
 * Time: 16:39
 */

namespace NflBundle\Lib;


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
}