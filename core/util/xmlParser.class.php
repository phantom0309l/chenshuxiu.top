<?php
/**
 *
 * @Copyright	(c) All rights reserved
 * @Author		mengjie <mengjie1977@gmail.com>
 * @Version		$Id: class_xml.php,v 1.3 2008/01/10 10:18:31 mengjie Exp $
 */

if (! function_exists('xml_set_element_handler')) {
    $extension_dir = ini_get('extension_dir');
    if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
        $extension_file = 'php_xml.dll';
    } else {
        $extension_file = 'xml.so';
    }
    if ($extension_dir and file_exists($extension_dir . '/' . $extension_file)) {
        ini_set('display_errors', true);
        dl($extension_file);
    }
}

@ini_set('memory_limit', - 1);

class xmlParser
{

    var $xml_parser;

    var $error_no = 0;

    var $xmldata = '';

    var $cdata = '';

    var $parseddata = array();

    var $stack = array();

    var $tag_count = 0;

    var $include_first_tag = false;

    // 最终得到什么编码的数据
    var $toCharset = 'UTF-8';

    function xmlParser ($xml, $path = '') {
        if ($xml !== false) {
            $this->xmldata = $xml;
        } else {
            if (empty($path)) {
                $this->error_no = 1;
            } else
                if (! ($this->xmldata = @file_get_contents($path))) {
                    $this->error_no = 2;
                }
        }
    }

    function &parse ($encoding = 'ISO-8859-1', $emptydata = true) {
        if (empty($this->xmldata) or $this->error_no > 0) {
            return false;
        }

        if (! ($this->xml_parser = xml_parser_create($encoding))) {
            return false;
        }

        xml_parser_set_option($this->xml_parser, XML_OPTION_SKIP_WHITE, 0);
        xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_character_data_handler($this->xml_parser, array(
            &$this,
            'handle_cdata'));
        xml_set_element_handler($this->xml_parser, array(
            &$this,
            'handle_element_start'), array(
            &$this,
            'handle_element_end'));

        xml_parse($this->xml_parser, $this->xmldata);
        $err = xml_get_error_code($this->xml_parser);

        if ($emptydata) {
            $this->xmldata = '';
            $this->stack = array();
            $this->cdata = '';
        }

        if ($err) {
            return false;
        }

        xml_parser_free($this->xml_parser);

        return $this->parseddata;
    }

    function parse_xml () {
        $this->xmldata = preg_replace('#[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]#', '', $this->xmldata);

        if (preg_match('#(<?xml.*encoding=[\'"])(.*?)([\'"].*?>)#m', $this->xmldata, $match)) {
            $in_encoding = strtoupper($match[2]);
            if ($in_encoding == 'ISO-8859-1') {
                $in_encoding = 'WINDOWS-1252';
            } else
                if ($in_encoding == 'GB2312') {
                    $in_encoding = 'GBK';
                }

            if (PHP_VERSION >= '5' and ($in_encoding != 'UTF-8' or strtoupper($this->toCharset) != 'UTF-8')) {
                $this->xmldata = str_replace($match[0], "$match[1]ISO-8859-1$match[3]", $this->xmldata);
            }
        } else {
            $in_encoding = 'UTF-8';

            if (PHP_VERSION >= '5') {
                if (strpos($this->xmldata, '<?xml') === false) {
                    $this->xmldata = '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n" . $this->xmldata;
                } else {
                    $this->xmldata = preg_replace('#(<?xml.*)(\?>)#', '\\1 encoding="ISO-8859-1" \\2', $this->xmldata);
                }

                $in_encoding = 'ISO-8859-1';
            }
        }

        $orig_string = $this->xmldata;

        $target_encoding = (strtolower($this->toCharset) == 'iso-8859-1' ? 'WINDOWS-1252' : $this->toCharset);
        $xml_encoding = (($in_encoding != 'UTF-8' or strtoupper($this->toCharset) != 'UTF-8') ? 'ISO-8859-1' : 'UTF-8');
        $iconv_passed = false;

        if (strtoupper($in_encoding) !== strtoupper($target_encoding)) {
            if (function_exists('iconv') and $encoded_data = iconv($in_encoding, $target_encoding . '//TRANSLIT', $this->xmldata)) {
                $iconv_passed = true;
                $this->xmldata = & $encoded_data;
            }

            if (! $iconv_passed and function_exists('mb_convert_encoding') and
                     $encoded_data = @mb_convert_encoding($this->xmldata, $target_encoding, $in_encoding)) {
                        $this->xmldata = & $encoded_data;
                    }
                }

                if ($this->parse($xml_encoding)) {
                    return true;
                } else
                    if ($iconv_passed and $this->xmldata = iconv($in_encoding, $target_encoding . '//IGNORE', $orig_string)) {
                        if ($this->parse($xml_encoding)) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
            }

            function handle_cdata (&$parser, $data) {
                $this->cdata .= $data;
            }

            function handle_element_start (&$parser, $name, $attribs) {
                $this->cdata = '';

                foreach ($attribs as $key => $val) {
                    if (preg_match('#&[a-z]+;#i', $val)) {
                        $val = str_replace(array(
                            '&lt;',
                            '&gt;',
                            '&quot;',
                            '&amp;'), array(
                            '<',
                            '>',
                            '"',
                            '&'), $val);
                        $attribs["$key"] = $val;
                    }
                }

                array_unshift($this->stack, array(
                    'name' => $name,
                    'attribs' => $attribs,
                    'tag_count' => ++ $this->tag_count));
            }

            function handle_element_end (&$parser, $name) {
                $tag = array_shift($this->stack);
                if ($tag['name'] != $name) {
                    return;
                }

                $output = $tag['attribs'];

                if (trim($this->cdata) !== '' or $tag['tag_count'] == $this->tag_count) {
                    if (sizeof($output) == 0) {
                        $output = $this->unescape_cdata($this->cdata);
                    } else {
                        $this->add_node($output, 'value', $this->unescape_cdata($this->cdata));
                    }
                }

                if (isset($this->stack[0])) {
                    $this->add_node($this->stack[0]['attribs'], $name, $output);
                } else {
                    if ($this->include_first_tag) {
                        $this->parseddata = array(
                            $name => $output);
                    } else {
                        $this->parseddata = $output;
                    }
                }
                $this->cdata = '';
            }

            function error_string () {
                if ($errorstring = @xml_error_string($this->error_code())) {
                    return $errorstring;
                } else {
                    return 'unknown';
                }
            }

            function error_line () {
                if ($errorline = @xml_get_current_line_number($this->xml_parser)) {
                    return $errorline;
                } else {
                    return 0;
                }
            }

            function error_code () {
                if ($errorcode = @xml_get_error_code($this->xml_parser)) {
                    return $errorcode;
                } else {
                    return 0;
                }
            }

            function add_node (&$children, $name, $value) {
                if (! is_array($children) or ! in_array($name, array_keys($children))) {
                    $children[$name] = $value;
                } else
                    if (is_array($children[$name]) and isset($children[$name][0])) {
                        $children[$name][] = $value;
                    } else {
                        $children[$name] = array(
                            $children[$name]);
                        $children[$name][] = $value;
                    }
            }

            function unescape_cdata ($xml) {
                static $find, $replace;

                if (! is_array($find)) {
                    $find = array(
                        '?[CDATA[',
                        ']]?',
                        "\r\n",
                        "\n");
                    $replace = array(
                        '<![CDATA[',
                        ']]>',
                        "\n",
                        "\r\n");
                }

                return str_replace($find, $replace, $xml);
            }
        }

        ?>
