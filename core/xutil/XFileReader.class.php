<?php

class XFileReader
{

    public static function getXlsFile ($file) {
        $reader = new Spreadsheet_Excel_Reader();
        $reader->setUTFEncoder('iconv');
        $reader->setOutputEncoding('UTF-8');
        $reader->read($file);
        return $reader->sheets[0]["cells"];
    }

    public static function getCsvFile ($file) {
        $fp = fopen($file, 'r');

        if (! $fp)
            return false;

        $rows = array();
        while (! feof($fp)) {
            $line = fgetcsv($fp);
            $rows[] = $line;
        }
        fclose($fp);
        return $rows;
    }

    public static function getTxtFile ($file, $spchar = "\t") {
        $data = file($file);
        $datalist = array();
        if (! $data)
            return false;
        for ($i = 0; $i < count($data); $i ++) {
            $tmp = split($spchar, trim($data[$i]));
            $datalist[] = $tmp;
        }
        return $datalist;
    }
}