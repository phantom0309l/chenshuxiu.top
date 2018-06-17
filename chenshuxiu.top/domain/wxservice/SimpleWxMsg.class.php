<?php

class SimpleWxMsg implements WxMsgBase
{

    private $title;

    private $picurl;

    private $content;

    private $url;

    public function __construct ($title, $picurl, $content, $url) {
        $this->title = $title;
        $this->picurl = $picurl;
        $this->content = $content;
        $this->url = $url;
    }

    public static function create ($title, $picurl, $content, $url) {
        return new self($title, $picurl, $content, $url);
    }

    // wx api begin
    public function getTitle4wx () {
        return $this->title;
    }

    public function getPicUrl4wx () {
        return $this->picurl;
    }

    public function getContent4wx () {
        return $this->content;
    }

    public function getUrl4wx () {
        return $this->url;
    }
}
