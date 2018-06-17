<?php

// require_once( 'safehtml-1.3.7/classes/safehtml2.php' );

/**
 * Xwork HtmlSafe, relative to SafeHTML, provide more strip profiles
 */
class XHtmlSafe extends SafeHTML2
{

    /**
     * default strip profile
     *
     * @var array
     */
    var $profile = array(
        'del_tag' => false)    // really delete tag or just htmlspecialchars it
    ;

    /**
     * Initialize function
     *
     * @param $profile array
     * @return XHtmlSafe
     */
    function XHtmlSafe ($profile = array()) {
        foreach ($profile as $key => $val)
            $this->profile[$key] = $val;
        parent::SafeHTML();
    }

    /**
     * Parse Html code, strip dangerous segment
     *
     * @param $html string
     * @return string html
     */
    function parse ($html) {
        // TODO fix by shijp : 临时方案
        if (is_array($html)) {
            return $html;
        }

        // If there are no HTML tags, ignore it
        if (false === strpos($html, '<'))
            return $html;
        if (! preg_match("/<(?=[a-zA-Z\/])/", $html))
            return $html;

        $this->clear();
        return parent::parse($html);
    }

    /**
     * Enter description here...
     *
     * @param $lt unknown_type
     * @return bool
     */
    function deleteTags (&$lt) {
        if (! $this->profile['del_tag']) {
            $lt = '&lt;';
            return false;
        }

        return true;
    }
}

