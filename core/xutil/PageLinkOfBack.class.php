<?php

class PageLinkOfBack
{

    const PAGE_FIRST = "首页";

    const PAGE_PREVIOUS = "上页";

    const PAGE_NEXT = "下页";

    const PAGE_LAST = "末页";

    // 总行数
    private $totalrows;
    // 每页显示行数
    private $pagesize;
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
    // “中间数码”链接
    private $midpages = "";
    // 下拉框分页导航控件
    private $selectpage = "";
    // 跳转控件
    private $gotopage = "";

    private $css_classname = '';

    private $newUrl = '';

    // $newUrl="/channel/123"
    public function setNewUrl ($newUrl) {
        $this->newUrl = $newUrl;
    }

    // ($countSql, self::PRE_PAGE_ROWS,
    // "admin.html?op=PageList&tradeid=".$tradeid, '')
    // ($countSql, self::PRE_PAGE_ROWS,
    // "op/PageList/tradeid/{$tradeid}/index.html", '/')
    public static function create ($countSql, $pagesize, $url, $bind = array(), $sep = '', $timeout = 1, $css_classname = "") {
        $pageLink = new PageLinkOfBack($countSql, $pagesize, $url, $sep, $timeout, $bind);

        $pageLink->css_classname = $css_classname;
        $pageLink->createPageLink();
        // $pageLink->createPageSelect();
        $pageLink->createGoto();
        return $pageLink;
    }

    // 注意：url中表示当前页码的参数必须命名为pagenum
    private function __construct ($countSql, $pagesize, $url, $sep = '/', $timeout = 1, $bind = array()) {
        $pagenum = XRequest::getValue('pagenum', 1);
        $pagenum = $pagenum ? $pagenum : 1;

        // TODO 总数的缓存如何解决呢? url缓存如何失效？ sqlcache是否必要呢?
        $totalrows = XRequest::getValue('totalrows', 0);
        if (is_numeric($countSql))
            $totalrows = $countSql;
        else
            $totalrows = BeanFinder::get("DbExecuter")->queryValue($countSql, $bind);

        $totalpage = ceil($totalrows / $pagesize);

        // 如果是 -1 则到最后一页,TODO 此做法已废弃
        // $pagenum = ($pagenum == -1 ? $totalpage : $pagenum);

        $pagenum = $pagenum <= $totalpage ? $pagenum : $totalpage;

        $this->pagenum = $pagenum;
        $this->totalrows = $totalrows;
        $this->pagesize = $pagesize;
        $this->totalpage = $totalpage;
        $uri = '';
        $url_part = parse_url($url);
        if (! empty($url_part['host'])) {
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
                $this->url = $uri . $tmpurlpath . "totalrows/{$totalrows}/pagenum/";
            } else {
                $this->url = $uri . "/totalrows/{$totalrows}/pagenum/";
            }
        } else {
            if (! empty($url_part["query"])) {
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
                    $this->url = $uri . "?" . implode("&", $getparm) . "&totalrows=$totalrows&pagenum=";
                else
                    $this->url = $uri . "?totalrows={$totalrows}&pagenum=";
            } else {
                $this->url = $url . "?totalrows={$totalrows}&pagenum=";
            }
        }
    }

    public function createPageLink ($firstlink = "", $prelink = "", $nextlink = "", $lastlink = "") {
        if (! isset($firstlink) || empty($firstlink))
            $firstlink = self::PAGE_FIRST;
        if (! isset($prelink) || empty($prelink))
            $prelink = self::PAGE_PREVIOUS;
        if (! isset($nextlink) || empty($nextlink))
            $nextlink = self::PAGE_NEXT;
        if (! isset($lastlink) || empty($lastlink))
            $lastlink = self::PAGE_LAST;

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
        } else {
            $this->prepage = "";
            $this->firstpage = "";
        }

        if ($this->pagenum != $this->totalpage && $this->totalpage > 0) {

            $this->nextpage = $a_left . $this->url . ($this->pagenum + 1) . $urlfileName . "'>" . $this->nextpage . "</a>";
            $this->lastpage = $a_left . $this->url . ($this->totalpage) . $urlfileName . "'>" . $this->lastpage . "</a>";
        } else {
            $this->nextpage = "";
            $this->lastpage = "";
        }

        // 中间数码部分
        $begPage = $this->pagenum - 4;
        $endPage = $this->pagenum + 4;
        if ($begPage < 1)
            $begPage = 1;
        if ($endPage > $this->totalpage)
            $endPage = $this->totalpage;

        for ($i = $begPage; $i <= $endPage; $i ++) {
            if ($this->pagenum == $i) {
                $this->midpages .= "<strong> $i </strong>";
            } else {
                $this->midpages .= $a_left . $this->url . $i . $urlfileName . "'>" . $i . "</a> ";
            }
        }

        // return $this->firstpage." ".$this->prepage." ".$this->nextpage."
        // ".$this->lastpage;
    }

    // 生成分页导航select控件
    // $selecthtml-用于分页导航的select控件的html码（不要结束标记</select>）,如<select width="100"
    // style="***" name="sex">
    public function createPageSelect ($selecthtml = "<select>") {
        $temp = explode(">", $selecthtml);
        $selecthtml = $temp[0] . " onchange='gotopage(this)'>";

        for ($i = 1; $i <= $this->totalpage; $i ++) {
            if ($i != $this->pagenum)
                $this->selectpage .= "<option value='{$i}'>{$i}</option>";
            else
                $this->selectpage .= "<option value='{$i}' selected>{$i}</option>";
        }

        $this->selectpage = $selecthtml . $this->selectpage . "</select>";
        $this->selectpage .= "<script>function gotopage(obj){window.location=\"" . $this->url . "\"+obj.options[obj.selectedIndex].value.toString()}</script>";
    }

    // 创建goto控件
    public function createGoto ($ctr_id_fix = "") {
        if ($this->totalpage > 100)
            $width = "36px";
        else
            $width = "24px";

        $this->gotopage = "跳至 <input type=text id='gotopage{$ctr_id_fix}' style='width:{$width};' value='{$this->pagenum}'> <input type=button value=go onclick='return pagelink_goto(document.getElementById(\"gotopage{$ctr_id_fix}\"))'>";
        $this->gotopage .= " <script>function pagelink_goto(obj){var p=/^[-,+]{0,1}[0-9]{0,}$/;if(!p.exec(obj.value)) return false; var pagetotal=" .
                 $this->totalpage . ";if (obj.value < 1 || obj.value > pagetotal || obj.value=={$this->pagenum} ) return false; window.location=\"" . $this->url .
                 "\"+obj.value+\"" . $this->_urlfilename . "\";  }</script>";
    }

    public function getStartRowNum () {
        if ($this->totalrows <= 0)
            return 0;
        return ($this->getPageNum() - 1) * $this->getpagesize() + 1;
    }

    public function getEndRowNum () {
        return min($this->getPageNum() * $this->getpagesize(), $this->getTotalRows());
    }

    public function getTotalRows () {
        return $this->totalrows;
    }

    public function getPagesize () {
        return $this->pagesize;
    }

    // 获取"当前页码"
    public function getPageNum () {
        return $this->pagenum;
    }
    // 获取"总页数"
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

    // 获取数字页面链接
    public function getMidPages () {
        return $this->midpages;
    }

    // 获取分页导航下拉框
    public function getSelectPage () {
        if (! empty($this->selectpage))
            return "转到" . $this->selectpage . "页";
        else
            return "";
    }
    // 获取跳转框
    public function getGotoPage ($ctr_id_fix = "") {
        if ($ctr_id_fix) {
            $this->createGoto($ctr_id_fix);
        }
        return $this->gotopage;
    }

    // 当页的第一个序号
    public function getFirstNoOfThePage () {
        return ($this->pagenum - 1) * $this->pagesize + 1;
    }

    public function setPageNum ($pagenum = 1) {
        $this->pagenum = $pagenum;
    }
}
