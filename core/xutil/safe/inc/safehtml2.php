<?php

/**
 * SafeHTML2 Parser
 * @see reversion from safehtml.php
 */

// define( 'XML_HTMLSAX3', dirname( __FILE__ ) . '/' );

// require_once(XML_HTMLSAX3 . 'HTMLSax3.php');

class SafeHTML2
{

    var $_xhtml = '';

    var $_counter = array();

    var $_stack = array();

    var $_dcCounter = array();

    var $_dcStack = array();

    var $_listScope = 0;

    var $_liStack = array();

    var $_protoRegexps = array();

    var $_cssRegexps = array();

    var $singleTags = array(
        'area',
        'br',
        'img',
        'input',
        'hr',
        'wbr');

    var $deleteTags = array(
        'applet',
        'base',
        'basefont',
        'bgsound',
        'blink',
        'body',
        'embed',
        'frame',
        'frameset',
        'head',
        'html',
        'ilayer',
        'iframe',
        'layer',
        'link',
        'meta',
        'object',
        'style',
        'title',
        'script');

    var $deleteTagsContent = array(
        'script',
        'style',
        'title',
        'xml');

    var $protocolFiltering = 'white';

    var $blackProtocols = array(
        'about',
        'chrome',
        'data',
        'disk',
        'hcp',
        'help',
        'javascript',
        'livescript',
        'lynxcgi',
        'lynxexec',
        'ms-help',
        'ms-its',
        'mhtml',
        'mocha',
        'opera',
        'res',
        'resource',
        'shell',
        'vbscript',
        'view-source',
        'vnd.ms.radio',
        'wysiwyg');

    var $whiteProtocols = array(
        'ed2k',
        'file',
        'ftp',
        'gopher',
        'http',
        'https',
        'irc',
        'mailto',
        'news',
        'nntp',
        'telnet',
        'webcal',
        'xmpp',
        'callto');

    var $protocolAttributes = array(
        'action',
        'background',
        'codebase',
        'dynsrc',
        'href',
        'lowsrc',
        'src');

    var $cssKeywords = array(
        'absolute',
        'behavior',
        'behaviour',
        'content',
        'expression',
        'fixed',
        'include-source',
        'moz-binding');

    var $noClose = array();

    var $closeParagraph = array(
        'address',
        'blockquote',
        'center',
        'dd',
        'dir',
        'div',
        'dl',
        'dt',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'hr',
        'isindex',
        'listing',
        'marquee',
        'menu',
        'multicol',
        'ol',
        'p',
        'plaintext',
        'pre',
        'table',
        'ul',
        'xmp',
        'meta');

    var $tableTags = array(
        'caption',
        'col',
        'colgroup',
        'tbody',
        'td',
        'tfoot',
        'th',
        'thead',
        'tr');

    var $listTags = array(
        'dir',
        'menu',
        'ol',
        'ul',
        'dl');

    var $attributes = array(
        'dynsrc',
        'id',
        'name');

    var $attributesNS = array(
        'xml:lang');

    function SafeHTML () {
        // making regular expressions based on Proto & CSS arrays
        foreach ($this->blackProtocols as $proto) {
            $preg = "/[\s\x01-\x1F]*";
            for ($i = 0; $i < strlen($proto); $i ++) {
                $preg .= $proto{$i} . "[\s\x01-\x1F]*";
            }
            $preg .= ":/i";
            $this->_protoRegexps[] = $preg;
        }

        foreach ($this->cssKeywords as $css) {
            $this->_cssRegexps[] = '/' . $css . '/i';
        }
        return true;
    }

    function _writeAttrs ($attrs) {
        if (is_array($attrs)) {
            foreach ($attrs as $name => $value) {

                $name = strtolower($name);

                if (strpos($name, 'on') === 0) {
                    continue;
                }
                if (strpos($name, 'data') === 0) {
                    continue;
                }
                if (in_array($name, $this->attributes)) {
                    continue;
                }
                if (! preg_match("/^[a-z0-9]+$/i", $name)) {
                    if (! in_array($name, $this->attributesNS)) {
                        continue;
                    }
                }

                if (($value === TRUE) || (is_null($value))) {
                    $value = $name;
                }

                if ($name == 'style') {

                    // removes insignificant backslahes
                    $value = str_replace("\\", '', $value);

                    // removes CSS comments
                    while (1) {
                        $_value = preg_replace("!/\*.*?\*/!s", '', $value);
                        if ($_value == $value)
                            break;
                        $value = $_value;
                    }

                    // replace all & to &amp;
                    $value = str_replace('&amp;', '&', $value);
                    $value = str_replace('&', '&amp;', $value);

                    $value = $this->gbk_full2half($value);

                    foreach ($this->_cssRegexps as $css) {
                        if (preg_match($css, $value)) {
                            continue 2;
                        }
                    }
                    foreach ($this->_protoRegexps as $proto) {
                        if (preg_match($proto, $value)) {
                            continue 2;
                        }
                    }
                }

                $tempval = preg_replace('/&#(\d+);?/me', "chr('\\1')", $value); // "'
                $tempval = preg_replace('/&#x([0-9a-f]+);?/mei', "chr(hexdec('\\1'))", $tempval);

                if ((in_array($name, $this->protocolAttributes)) && (strpos($tempval, ':') !== false)) {
                    if ($this->protocolFiltering == 'black') {
                        foreach ($this->_protoRegexps as $proto) {
                            if (preg_match($proto, $tempval))
                                continue 2;
                        }
                    } else {
                        $_tempval = explode(':', $tempval);
                        $proto = $_tempval[0];
                        if (! in_array($proto, $this->whiteProtocols)) {
                            continue;
                        }
                    }
                }

                $value = str_replace("\"", "&quot;", $value);
                $this->_xhtml .= ' ' . $name . '="' . $value . '"';
            }
        }
        return true;
    }

    function _openHandler (&$parser, $o_name, $attrs) {
        $name = strtolower($o_name);
        $lt = '<';

        if (in_array($name, $this->deleteTagsContent)) {
            array_push($this->_dcStack, $name);
            $this->_dcCounter[$name] = isset($this->_dcCounter[$name]) ? $this->_dcCounter[$name] + 1 : 1;
        }
        if (count($this->_dcStack) != 0) {
            return true;
        }

        if (in_array($name, $this->deleteTags)) {
            if (true === $this->deleteTags($lt))
                return true;
        }

        if (! preg_match("/^[a-z0-9]+$/i", $name)) {
            if (preg_match("!(?:\@|://)!i", $name)) {
                $this->_xhtml .= '&lt;' . $o_name . '&gt;';
            }
            return true;
        }

        if (in_array($name, $this->singleTags)) {
            $this->_xhtml .= $lt . $o_name;
            $this->_writeAttrs($attrs);
            $this->_xhtml .= ' />';
            return true;
        }

        // TABLES: cannot open table elements when we are not inside table
        if ((isset($this->_counter['table'])) && ($this->_counter['table'] <= 0) && (in_array($name, $this->tableTags))) {
            return true;
        }

        // PARAGRAPHS: close paragraph when closeParagraph tags opening
        if ((in_array($name, $this->closeParagraph)) && (in_array('p', $this->_stack))) {
            $this->_closeHandler($parser, 'p');
        }

        // LISTS: we should close <li> if <li> of the same level opening
        if ($name == 'li' && count($this->_liStack) && $this->_listScope == $this->_liStack[count($this->_liStack) - 1]) {
            $this->_closeHandler($parser, 'li');
        }

        // LISTS: we want to know on what nesting level of lists we are
        if (in_array($name, $this->listTags)) {
            $this->_listScope ++;
        }
        if ($name == 'li') {
            array_push($this->_liStack, $this->_listScope);
        }

        $this->_xhtml .= $lt . $o_name;
        $this->_writeAttrs($attrs);
        $this->_xhtml .= '>';
        array_push($this->_stack, $name);
        $this->_counter[$name] = isset($this->_counter[$name]) ? $this->_counter[$name] + 1 : 1;
        return true;
    }

    function _closeHandler (&$parser, $o_name) {

        $name = strtolower($o_name);

        if (isset($this->_dcCounter[$name]) && ($this->_dcCounter[$name] > 0) && (in_array($name, $this->deleteTagsContent))) {
            while ($name != ($tag = array_pop($this->_dcStack))) {
                $this->_dcCounter[$tag] --;
            }

            $this->_dcCounter[$name] --;
        }

        if (count($this->_dcStack) != 0) {
            return true;
        }

        if ((isset($this->_counter[$name])) && ($this->_counter[$name] > 0)) {
            while ($name != ($tag = array_pop($this->_stack))) {
                $this->_closeTag($tag);
            }

            $this->_closeTag($o_name);
        }
        return true;
    }

    function _closeTag ($o_tag) {
        $tag = strtolower($o_tag);

        if (! in_array($tag, $this->noClose)) {
            $this->_xhtml .= '</' . $o_tag . '>';
        }

        $this->_counter[$tag] --;

        if (in_array($tag, $this->listTags)) {
            $this->_listScope --;
        }

        if ($tag == 'li') {
            array_pop($this->_liStack);
        }
        return true;
    }

    function _dataHandler (&$parser, $data) {
        if (count($this->_dcStack) == 0) {
            $this->_xhtml .= $data;
        }
        return true;
    }

    function _escapeHandler (&$parser, $data) {
        return true;
    }

    function getXHTML () {
        while ($tag = array_pop($this->_stack)) {
            $this->_closeTag($tag);
        }

        return $this->_xhtml;
    }

    function clear () {
        $this->_xhtml = '';
        $this->_counter = array();
        $this->_stack = array();
        $this->_dcCounter = array();
        $this->_dcStack = array();
        $this->_listScope = 0;
        $this->_liStack = array();

        return true;
    }

    function parse ($doc) {
        // Replacer of '<' symbols
        $LT = "``lt''";

        // Save all '<' symbols
        $doc = preg_replace("/<(?=[^a-zA-Z\/\!\?\%])/", $LT, $doc);

        // Save all '/**/' script comment
        $doc = preg_replace('/\/\*(.*?)\//', ' ', $doc);

        // Web documents shouldn't contains \x00 symbol
        $doc = str_replace("\x00", '', $doc);

        // Opera6 bug workaround
        $doc = str_replace("\xC0\xBC", $LT, $doc);

        // UTF-7 encoding ASCII decode
        $doc = $this->repackUTF7($doc);

        // Instantiate the parser
        $parser = new XML_HTMLSax3();

        // Set up the parser
        $parser->set_object($this);

        $parser->set_element_handler('_openHandler', '_closeHandler');
        $parser->set_data_handler('_dataHandler');
        $parser->set_escape_handler('_escapeHandler');

        $parser->parse($doc);

        $res = $this->getXHTML();

        // Recover all $LT replacer to '<' symbols
        $res = str_replace($LT, '<', $res);

        return $res;

    }

    function repackUTF7 ($str) {
        return preg_replace_callback('!\+([0-9a-zA-Z/]+)\-!', array(
            $this,
            'repackUTF7Callback'), $str);
    }

    function repackUTF7Callback ($str) {
        $str = base64_decode($str[1]);
        $str = preg_replace_callback('/^((?:\x00.)*)((?:[^\x00].)+)/', array(
            $this,
            'repackUTF7Back'), $str);
        return preg_replace('/\x00(.)/', '$1', $str);
    }

    function repackUTF7Back ($str) {
        return $str[1] . '+' . rtrim(base64_encode($str[2]), '=') . '-';
    }

    function deleteTags (&$lt) {
        return true;
    }

    /**
     * Convert GBK char from Full-Jiao to Half-Jiao
     *
     * @param $Str string
     * @return string
     */
    function gbk_full2half ($Str) {
        static $s_map_f2h = Array(
            '０' => '0',
            '１' => '1',
            '２' => '2',
            '３' => '3',
            '４' => '4',
            '５' => '5',
            '６' => '6',
            '７' => '7',
            '８' => '8',
            '９' => '9',

            'Ａ' => 'A',
            'Ｂ' => 'B',
            'Ｃ' => 'C',
            'Ｄ' => 'D',
            'Ｅ' => 'E',
            'Ｆ' => 'F',
            'Ｇ' => 'G',
            'Ｈ' => 'H',
            'Ｉ' => 'I',
            'Ｊ' => 'J',
            'Ｋ' => 'K',
            'Ｌ' => 'L',
            'Ｍ' => 'M',
            'Ｎ' => 'N',
            'Ｏ' => 'O',
            'Ｐ' => 'P',
            'Ｑ' => 'Q',
            'Ｒ' => 'R',
            'Ｓ' => 'S',
            'Ｔ' => 'T',
            'Ｕ' => 'U',
            'Ｖ' => 'V',
            'Ｗ' => 'W',
            'Ｘ' => 'X',
            'Ｙ' => 'Y',
            'Ｚ' => 'Z',

            'ａ' => 'a',
            'ｂ' => 'b',
            'ｃ' => 'c',
            'ｄ' => 'd',
            'ｅ' => 'e',
            'ｆ' => 'f',
            'ｇ' => 'g',
            'ｈ' => 'h',
            'ｉ' => 'i',
            'ｊ' => 'j',
            'ｋ' => 'k',
            'ｌ' => 'l',
            'ｍ' => 'm',
            'ｎ' => 'n',
            'ｏ' => 'o',
            'ｐ' => 'p',
            'ｑ' => 'q',
            'ｒ' => 'r',
            'ｓ' => 's',
            'ｔ' => 't',
            'ｕ' => 'u',
            'ｖ' => 'v',
            'ｗ' => 'w',
            'ｘ' => 'x',
            'ｙ' => 'y',
            'ｚ' => 'z',
            '－' => '-');

        return preg_replace("/([\xA3][\xB0-\xB9\xC1-\xDA\xE1-\xFA])/e", "\$s_map_f2h['\\1']", $Str);
    }
}

