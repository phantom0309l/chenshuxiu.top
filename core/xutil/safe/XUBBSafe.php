<?php

/**
 * Xwork UBBSafe, strip dangerous code from UBB code
 *
 */
class XUBBSafe
{

    /**
     * UBB string maps
     *
     * @var array
     */
    var $str_maps = array(
        "[b]" => '<b>',
        "[/b]" => '</b>',
        "[i]" => '<i>',
        "[/i]" => '</i>',
        "[u]" => '<u>',
        "[/u]" => '</u>',
        "[left]" => '<div align=left>',
        "[/left]" => '</div>',
        "[center]" => '<div align=center>',
        "[/center]" => '</div>',
        "[right]" => '<div align=right>',
        "[/right]" => '</div>',
        "[indent]" => '<div>&nbsp;&nbsp;&nbsp;&nbsp;',
        "[/indent]" => '</div>',
        "[br]" => '<br>',
        "[/font]" => "</font>",
        "[/size]" => "</font>",
        "[/color]" => "</font>",
        "\n" => "<br>\n");

    /**
     * UBB preg maps
     *
     * @var array
     */
    var $preg_maps = array(
        "/\[quote\](.*?)\[\/quote\]/ies" => "XUBBSafe::handler_quote( '\\1' )",
        "/\[font=([^\[\<]+?)\]/ie" => '"<font face=\"" . addslashes( "\1" ) . "\">"',
        "/\[size=(\d+?)\]/ie" => '"<font size=\"" . addslashes( "\1" ) . "\">"',
        "/\[size=(\d+(px|pt|in|cm|mm|pc|em|ex|%)+?)\]/ie" => '"<font style=\"font-size: " . addslashes( "\1" ) . "\">"',
        "/\[color=([^\[\<]+?)\]/ie" => '"<font color=\"" .addslashes( "\1" ) . "\">"',
        "/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies" => 'XUBBSafe::handler_img( "\1" )',
        "/\[img=(\d{1,3})[x|\,](\d{1,3})\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies" => 'XUBBSafe::handler_img( "\3", "\1", "\2" )',
        "/\[url\]\s*(www.|https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/|callto:\/\/|bctp:\/\/|ed2k:\/\/){1}([^\[\"']+?)\s*\[\/url\]/ie" => 'XUBBSafe::handler_url( "\1\2" )',
        "/\[url=www.([^\[\"']+?)\](.*?)\[\/url\]/ies" => 'XUBBSafe::handler_url( "http://www.\1", "\2" )',
        "/\[url=(https?|ftp|gopher|news|telnet|rtsp|mms|callto|ed2k){1}:\/\/([^\[\"']+?)\](.*?)\[\/url\]/ies" => 'XUBBSafe::handler_url( "\1://\2", "\3" )',
        "/\[email\]\s*([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\s*\[\/email\]/ie" => '"<a href=\"mailto:" . addslashes( "\1" ) . "@" . addslashes( "\2" ) . "\">" . htmlspecialchars( "\1" ) . "@" . htmlspecialchars( "\2" ) . "</a>"',
        "/\[email=([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\](.+?)\[\/email\]/ies" => '"<a href=\"mailto:" . addslashes( "\1" ) . "@" . addslashes( "\2" ) . "\">" . htmlspecialchars( "\3" ) . "</a>"',
        "/\[swf\]\s*([^\[\<\r\n]+?)\s*\[\/swf\]/ies" => "XUBBSafe::handler_swf( '\\1' )",
        "/\[flash\]\s*([^\[\<\r\n]+?)\s*\[\/flash\]/ies" => "XUBBSafe::handler_swf( '\\1' )");

    /**
     * User UBB tag handlers
     *
     * @var unknown_type
     */
    var $usr_handlers = array(
        'quote' => '',
        'img' => '',
        'url' => '',
        'swf' => '');

    /**
     * Initialize function
     *
     * @param $handlers array
     * @return XHtmlSafe
     */
    function XUBBSafe ($handlers = array()) {
        // Register every handler
        foreach ($handlers as $tag => $handler) {
            $tag = strtolower($tag);

            if (isset($this->usr_handlers[$tag])) {
                if (is_array($handler) && is_object($handler[0])) {
                    $this->usr_handlers[$tag] = $handler;
                } else
                    if (function_exists($handler))                     // global function
                    {
                        $this->usr_handlers[$tag] = $handler;
                    }
            }
        }

        // splite pattern and replacement
        if (! isset($GLOBALS['g_XUBBSafe_patterns'])) {
            $GLOBALS['g_XUBBSafe_from'] = array_keys($this->str_maps);
            $GLOBALS['g_XUBBSafe_to'] = array_values($this->str_maps);
            $GLOBALS['g_XUBBSafe_patterns'] = array_keys($this->preg_maps);
            $GLOBALS['g_XUBBSafe_replacement'] = array_values($this->preg_maps);
        }
    }

    /**
     * Parse UBB code, strip dangerous segment
     *
     * @param $ubb string
     * @return string ubb
     */
    function ubb2html ($ubb) {
        // If there are no HTML or UBB tag, ignore it
        if ((false === strpos($ubb, '<')) && (false === strpos($ubb, '['))) {
            return $ubb;
        }

        // strip HTML tags
        $ubb = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
                str_replace(array(
                    '&',
                    '"',
                    '<',
                    '>'), array(
                    '&amp;',
                    '&quot;',
                    '&lt;',
                    '&gt;'), $ubb));

        // str_replace
        $ubb = str_ireplace($GLOBALS['g_XUBBSafe_from'], $GLOBALS['g_XUBBSafe_to'], $ubb);

        return preg_replace($GLOBALS['g_XUBBSafe_patterns'], $GLOBALS['g_XUBBSafe_replacement'], $ubb);
    }

    /**
     * Default handler of quote tag
     *
     * @param
     *            string	src
     * @return string
     */
    function handler_quote ($src) {
        $handler = & $this->usr_handlers['quote'];

        if (empty($handler))         // default handler
        {
            return "<div style='width:98%;border:1px solid #ccc;padding:3px;'><span class='gray'>$src</span></div>";
        } else         // user handler
        {
            return is_array($handler) ? $handler[0]->{ $handler[1] }($src) : $handler($src);
        }
    }

    /**
     * Default handler of img tag
     *
     * @param $src string
     * @param $width int
     * @param $height int
     * @return string
     */
    function handler_img ($src, $width = null, $height = null) {
        $handler = & $this->usr_handlers['img'];

        // Prefix protocol scheme
        static $s_img_schemes = array(
            'http:/',
            'ftp://',
            'https:');

        if (! in_array(strtolower(substr($src, 0, 6)), $s_img_schemes)) {
            $src = 'http://' . $src;
        }

        // check the suffix, prevent the "logout" attack
        $src = str_replace(array(
            'submit',
            'logout'), '', $src);

        // slash the src
        $src = addslashes($src);

        if (empty($handler))         // default handler
        {
            $width = is_null($width) ? '' : 'width="' . addslashes($width) . '"';
            $height = is_null($height) ? '' : 'height="' . addslashes($height) . '"';
            return "<img src=\"$src\" $width $height></img>";
        } else         // user handler
        {
            return is_array($handler) ? $handler[0]->{ $handler[1] }($src, $width, $height) : $handler($src, $width, $height);
        }
    }

    /**
     * Default handler of url tag
     *
     * @param $src string
     * @param $anchor string
     * @return string
     */
    function handler_url ($src, $anchor = '') {
        $handler = & $this->usr_handlers['url'];

        // slash the src
        $src = addslashes($src);

        if (empty($handler))         // default handler
        {
            $anchor = empty($anchor) ? $src : $anchor;
            return "<a href=\"$src\">$anchor</a>";
        } else         // user handler
        {
            return is_array($handler) ? $handler[0]->{ $handler[1] }($src, $anchor) : $handler($src, $anchor);
        }
    }

    /**
     * Default handler of swf tag
     *
     * @param $src string
     * @param $anchor string
     * @return string
     */
    function handler_swf ($src) {
        $handler = & $this->usr_handlers['swf'];

        // slash the src
        $src = addslashes($src);

        if (empty($handler))         // default handler
        {
            return "<EMBED src=\"$src\" width=\"460\" height=\"390\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" play=\"false\"></EMBED>";
        } else         // user handler
        {
            return is_array($handler) ? $handler[0]->{ $handler[1] }($src) : $handler($src);
        }
    }
}

// PHP4 Compt
if (! function_exists('str_ireplace')) {

    function str_ireplace ($search, $replace, $subject) {
        if (is_array($search)) {
            array_walk($search, 'str_ireplace__make_pattern');
        } else {
            $search = '/' . preg_quote($search, '/') . '/i';
        }

        return preg_replace($search, $replace, $subject);
    }

    function str_ireplace__make_pattern (&$pat, $key) {
        $pat = '/' . preg_quote($pat, '/') . '/i';
    }
}

