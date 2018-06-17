<?php

class XMLUtil
{

    public static function array2Xml ($array, $root = 'resultSet', $attrs = array()) {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement($root);
        self::attribute($xml, $attrs);
        self::write($xml, $array);
        $xml->endElement();
        return $xml->outputMemory(true);
    }

    public static function array2XmlSection ($array) {
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');
        self::write($xml, $array);
        return $xml->outputMemory(true);
    }

    private static function attribute (XMLWriter $xml, $attrs) {
        if (false == empty($attrs)) {
            foreach ($attrs as $key => $value) {
                $xml->writeAttribute($key, $value);
            }
        }
    }

    /*
     * $resultSet = array( "count" => "3", "error" => array("code" => "0",
     * "message" => "执行成功"), "keyword" => array("hello", "world"), "result" =>
     * array(array("id" => "1", "url" => "http://hello.com"), array("id" => "2",
     * "url" => "http://world.com"), array("id" => "3", "url" =>
     * "http://helloworld.com")), "linkedword" => array("hi", "earth"),
     * "linkedcontent" => array("text", "key" => "linkedkey"), );
     */
    private static function write (XMLWriter $xml, $data, $pkey = null) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (! is_int($key))                 // 如error, result
                {
                    $xml->startElement($key);
                } else
                    if ($key != 0)                     // result的下一级，第一个tag由父级输出，其它tag本处输出
                    {
                        $xml->endElement();
                        $xml->startElement($pkey);
                    }

                self::write($xml, $value, $key);
                if (! is_int($key)) {
                    $xml->endElement();
                }

                continue;
            } else {
                if (! is_int($key))                 // 如count
                {
                    $xml->writeElement($key, $value);
                } else                 // 如keyword
                {
                    if ($key == 0)                     // keyword的第一个，tag由父级输出
                    {
                        $xml->text($value);
                    } else                     // keyword的其它tag本处输出
                    {
                        $xml->endElement();
                        $xml->startElement($pkey);
                        $xml->text($value);
                    }
                }
            }
        }
    }
}
?>