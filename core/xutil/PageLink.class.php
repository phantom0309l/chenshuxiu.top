<?php

class PageLink
{

    const PAGE_FIRST = "[首页]";

    const PAGE_PREVIOUS = "[上页]";

    const PAGE_NEXT = "[下页]";

    const PAGE_LAST = "[末页]";

    const PAGE_FIRST_EN = "[Front]";

    const PAGE_PREVIOUS_EN = "[Previous]";

    const PAGE_NEXT_EN = "[Next]";

    const PAGE_LAST_EN = "[End]";

    private $showEntitys;

    // 总行数
    private $totalrows;
    // 每页显示行数
    private $rowsperpage;
    // 当前页码
    private $pagenum;
    // 总页数
    private $totalpage;
    // 分页链接的URL
    private $url;
    // url文件名，如：index.htm
    private $_urlfilename;
    // “前一页”链接
    private $prepage = "";
    // “下一页”链接
    private $nextpage = "";
    // “首页”链接
    private $firstpage = "";
    // “末页”链接
    private $lastpage = "";
    // 页码链接
    private $digitalpage = "";
    // 下拉框分页导航控件
    private $selectpage = "";
    // 跳转控件
    private $gotopage = "";

    private $css_classname = '';

    public static function create ($entitys, $rowsperpage, $url, $sep = '/') {
        $pageLink = new PageLink($entitys, $rowsperpage, $url, $sep);
        $pageLink->createPageLink();
        $pageLink->createPageSelect();
        return $pageLink;
    }

    // 注意：url中表示当前页码的参数必须命名为pagenum
    public function __construct ($entitys, $rowsperpage, $url, $sep = '/') {
        if (is_array($entitys) == false)
            $entitys = array();
        $pagenum = XRequest::getValue('pagenum', 1);
        $pagenum = $pagenum ? $pagenum : 1;
        $entityPages = array_chunk($entitys, $rowsperpage);
        $this->showEntitys = $entityPages[$pagenum - 1];
        $totalrows = count($entitys);
        $pageCount = count($entityPages);
        $pagenum = $pagenum <= $pageCount ? $pagenum : $pageCount;

        $this->pagenum = $pagenum;
        $this->totalrows = $totalrows;
        $this->rowsperpage = $rowsperpage;
        $this->totalpage = ceil($totalrows / $rowsperpage);
        $uri = '';
        $url_part = parse_url($url);
        if ($url_part['host'] != '') {
            if ($url_part['scheme'] != '')
                $uri = $uri . $url_part['scheme'] . '://';

            $uri = $uri . $url_part['host'];
        }
        if ($sep == '/') {
            if ($url_part["query"] == '' && $url_part["path"] != '') {
                $tmpurlpath = '';
                $urlpath = $url_part["path"];
                $temp = explode('/', $url);
                $this->_urlfilename = $temp[count($temp) - 1];
                $istart = (count($temp) + 1) % 2;
                for ($i = $istart; $i < count($temp) - 1; $i = $i + 2) {
                    if ($temp[$i] == 'pagenum') {
                        unset($temp[$i]);
                        unset($temp[$i + 1]);
                    }
                    $tmpurlpath = $tmpurlpath . '/' . $temp[$i] . '/' . $temp[$i + 1];
                }
                $tmpurlpath = substr($tmpurlpath, 0, strlen($tmpurlpath) - 1);
                $this->url = $uri . $tmpurlpath . "pagenum/";
            } else {
                $this->url = $uri . "/pagenum/";
            }
        } else {
            if ($url_part["query"] != "") {
                $temp = explode("?", $url);
                $uri = $temp[0];

                $getparm = explode("&", $temp[1]);
                for ($i = 0; $i < count($getparm); $i ++) {
                    $temp = explode("=", $getparm[$i]);
                    if ($temp[0] == "pagenum") {
                        unset($getparm[$i]);
                        break;
                    }
                }
                if (count($getparm) != 0)
                    $this->url = $uri . "?" . implode("&", $getparm) . "&pagenum=";
                else
                    $this->url = $uri . "?pagenum=";
            } else {
                $this->url = $url . "?pagenum=";
            }
        }
    }

    public function createPageLink ($firstlink = "", $prelink = "", $nextlink = "", $lastlink = "") {
        if (Config::getConfig("cp_lang") == "en") {
            if (! isset($firstlink) || empty($firstlink))
                $firstlink = self::PAGE_FIRST_EN;
            if (! isset($prelink) || empty($prelink))
                $prelink = self::PAGE_PREVIOUS_EN;
            if (! isset($nextlink) || empty($nextlink))
                $nextlink = self::PAGE_NEXT_EN;
            if (! isset($lastlink) || empty($lastlink))
                $lastlink = self::PAGE_LAST_EN;
        } else {
            if (! isset($firstlink) || empty($firstlink))
                $firstlink = self::PAGE_FIRST;
            if (! isset($prelink) || empty($prelink))
                $prelink = self::PAGE_PREVIOUS;
            if (! isset($nextlink) || empty($nextlink))
                $nextlink = self::PAGE_NEXT;
            if (! isset($lastlink) || empty($lastlink))
                $lastlink = self::PAGE_LAST;
        }

        $this->firstpage = $firstlink;
        $this->prepage = $prelink;
        $this->nextpage = $nextlink;
        $this->lastpage = $lastlink;

        if ($this->css_classname != '') {
            $a_left = "<a class='" . $this->css_classname . "' href='";
        } else {
            $a_left = "<a href='";
        }

        if (! isset($this->_urlfilename) || empty($this->_urlfilename))
            $urlfileName = "";
        else
            $urlfileName = '/' . $this->_urlfilename;
        if ($this->pagenum != 1 && $this->pagenum != 0) {
            $this->prepage = $a_left . $this->url . ($this->pagenum - 1) . $urlfileName . "'>" . $this->prepage . "</a>";
            $this->firstpage = $a_left . $this->url . (1) . $urlfileName . "'>" . $this->firstpage . "</a>";
        }
        if ($this->pagenum != $this->totalpage && $this->totalpage > 0) {

            $this->nextpage = $a_left . $this->url . ($this->pagenum + 1) . $urlfileName . "'>" . $this->nextpage . "</a>";
            $this->lastpage = $a_left . $this->url . ($this->totalpage) . $urlfileName . "'>" . $this->lastpage . "</a>";
        }

        // return $this->firstpage." ".$this->prepage." ".$this->nextpage."
        // ".$this->lastpage;
    }

    public function createPageImage ($firstimg, $preimg, $nextimg, $lastimg) {
        $this->firstpage = $firstimg;
        $this->prepage = $preimg;
        $this->nextpage = $nextimg;
        $this->lastpage = $lastimg;

        if ($this->pagenum != 1 && $this->pagenum != 0) {
            $this->prepage = "<a href='" . $this->url . ($this->pagenum - 1) . '/' . $this->_urlfilename . "'>" . $this->prepage . "</a>";
            $this->firstpage = "<a href='" . $this->url . (1) . '/' . $this->_urlfilename . "'>" . $this->firstpage . "</a>";
        }
        if ($this->pagenum != $this->totalpage && $this->pagenum != 0) {
            $this->nextpage = "<a href='" . $this->url . ($this->pagenum + 1) . '/' . $this->_urlfilename . "'>" . $this->nextpage . "</a>";
            $this->lastpage = "<a href='" . $this->url . ($this->totalpage) . '/' . $this->_urlfilename . "'>" . $this->lastpage . "</a>";
        }
    }

    public function getShowEntitys () {
        if (empty($this->showEntitys)) {
            $this->showEntitys = array();
        }
        return $this->showEntitys;
    }

    // 生成分页按钮
    public function createPageButton () {
        // ....
    }

    // 生成分页导航select控件
    // $selecthtml-用于分页导航的select控件的html码（不要结束标记</select>）,如<select width="100"
    // style="***" name="sex">
    public function createPageSelect ($selecthtml = "<select>") {
        $temp = explode(">", $selecthtml);
        $selecthtml = $temp[0] . " onchange='gotopage(this)'>";

        for ($i = 1; $i <= $this->totalpage; $i ++) {
            if ($i != $this->pagenum)
                $this->selectpage .= "<option value='$i'>$i</option>";
            else
                $this->selectpage .= "<option value='$i' selected>$i</option>";
        }

        $this->selectpage = $selecthtml . $this->selectpage . "</select>";
        $this->selectpage .= "<script>function gotopage(obj){window.location=\"" . $this->url . "\"+obj.options[obj.selectedIndex].value.toString()}</script>";
    }

    public function getGotoScript () {
        $result = "<script>function pagelink_goto(page){var p=/^[-,+]{0,1}[0-9]{0,}$/;if(!p.exec(page)) return false; var pagetotal=" . $this->totalpage .
                 ";if (page < 0 || page > pagetotal) return false; window.location=\"" . $this->url . "\"+page+\"/" . $this->_urlfilename . "\";  }</script>";
        return $result;
    }

    public function getStartRowNum () {
        if ($this->totalrows <= 0)
            return 0;
        return ($this->getPageNum() - 1) * $this->getRowsPerpage() + 1;
    }

    public function getEndRowNum () {
        return min($this->getPageNum() * $this->getRowsPerpage(), $this->getTotalRows());
    }

    public function getTotalRows () {
        return $this->totalrows;
    }

    public function getRowsPerpage () {
        return $this->rowsperpage;
    }

    public function getPageNum () {
        return $this->pagenum;
    }

    public function getTotalPage () {
        return $this->totalpage;
    }

    // 获取"前页"导航链接
    public function getPrePage () {
        return $this->prepage;
    }

    // 获取"下页"导航链接
    public function getNextPage () {
        return $this->nextpage;
    }

    // 获取"首页"导航链接
    public function getFirstPage () {
        return $this->firstpage;
    }

    // 获取"末页"导航链接
    public function getLastPage () {
        return $this->lastpage;
    }

    // 获取分页导航下拉框
    public function getSelectPage () {
        return "转到" . $this->selectpage . "页";
    }

    public function getGotoPage () {
        return $this->gotopage;
    }

    public function setStyle ($value) {
        $this->css_classname = $value;
    }

    public function getPageRange ($array) {
        $startIndex = ($this->pagenum - 1) * $this->rowsperpage;
        $endIndex = min($this->pagenum * $this->rowsperpage - 1, count($array) - 1);
        for ($i = $startIndex; $i <= $endIndex; $i ++) {
            $subArray[] = $array[$i];
        }
        return $subArray;
    }
}

