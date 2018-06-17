<?php

class Csv
{

    private $fp;

    public function __construct ($file) {
        $this->fp = fopen($file, "w");
        if ($this->fp == false) {
            print "Cant't open $file to write!\n";
            return;
        }
    }

    public function write ($infos, $quotation = false) {
        if (! is_array($infos))
            return false;
        $str = '';
        foreach ($infos as $info) {
            $info = str_replace(array(
                '"',
                '&nbsp;'), array(
                "",
                ""), $info);
            $info = preg_replace('|[\s]+|', ' ', $info);
            $info = preg_replace('|<style>[^<]+</style>|i', '', $info);
            $info = preg_replace('|<[^>]+>|', '', $info);
            if (is_string($info) && $quotation)
                $str .= '"' . $info . '",';
            else
                $str .= $info . ',';
        }
        if (! empty($str)) {
            $str = substr($str, 0, - 1);
            fwrite($this->fp, "$str\n");
        }
    }

    public function close () {
        fclose($this->fp);
    }

    public static function transStr ($str) {
        $str = iconv("utf-8", "gbk", $str);
        return str_replace(",", "��", $str);
    }

    // 只记录单条数据,error_log
    public static function log ($filename, $infos) {
        if (! is_array($infos))
            return false;
        $str = self::infos2str($infos);
        if (! empty($str)) {
            error_log($str . "\n", 3, $filename);
            return true;
        }
        return false;
    }

    public static function infos2str ($infos) {
        $strs = array();
        foreach ($infos as $info) {
            if (! isset($info)) {
                $info = 0;
            } else {
                $info = str_replace(array(
                    ",",
                    '"',
                    '&nbsp;'), array(
                    "",
                    "",
                    ""), $info);
                $info = preg_replace('|[\s]+|', ' ', $info);
                $info = preg_replace('|<style>[^<]+</style>|i', '', $info);
                $info = preg_replace('|<[^>]+>|', '', $info);
            }

            $strs[] = $info;
        }
        return implode(",", $strs);
    }

    public static function preg ($info) {
        if (! isset($info)) {
            $info = 0;
        } else {
            $info = str_replace(array(
                ",",
                '"',
                '&nbsp;'), array(
                "",
                "",
                ""), $info);
            $info = preg_replace('|[\s]+|', ' ', $info);
            $info = preg_replace('|<style>[^<]+</style>|i', '', $info);
            $info = preg_replace('|<[^>]+>|', '', $info);
        }

        return $info;
    }

    public static function infos2str_simple ($infos) {
        return implode(",", $infos);
    }
}
?>