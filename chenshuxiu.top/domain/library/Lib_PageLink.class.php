<?php

/**
 * 分页类
 * @auth chenshigang
 * @date 2014-10-24
 */
class Lib_PageLink
{

    /**
     * 参数设定
     *
     * @param $current_page int
     *            當前頁數
     * @param $total_page int
     *            總頁數
     * @param $boundary int
     *            頁數臨界值
     * @param $front_range int
     *            前段顯示頁碼數
     * @param $mid_range int
     *            中段顯示頁碼數
     * @param $rear_range int
     *            後段顯示頁碼數
     */
    public $current_page = 1;

    public $total_page = 10;

    public $boundary = 7;

    public $front_range = 1;

    public $mid_range = 5;

    public $rear_range = 1;
    // 每个页的链接 如: domain/list#page=
    public $page_url = 'pagenum';

    public function __construct ($totalNum, $currentPage = 1, $pagesize = 20) {
        if ($totalNum == 0) {
            $totalPage = 1;
        } else {
            $totalPage = ceil($totalNum / $pagesize);
        }
        $this->total_page = $totalPage;
        $this->current_page = $currentPage;
    }

    /**
     * 初始化设置 分页类
     *
     * @param $config type
     */
    function init ($config = array()) {
        if (count($config) > 0) {
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 分頁格式處理，ex.
     * prev 1 ... 8 9 10 11 12 ... 20 next
     * copy from Store_lib.pagintion , 从Store_lib中复制过来
     *
     * @param $current_page int
     *            當前頁數
     * @param $total_page int
     *            總頁數
     * @param $boundary int
     *            頁數臨界值
     * @param $front_range int
     *            前段顯示頁碼數
     * @param $mid_range int
     *            中段顯示頁碼數
     * @param $rear_range int
     *            後段顯示頁碼數
     * @return array 要顯示的頁碼
     */
    public function pagintion_array ($current_page = 1, $total_page = 10, $boundary = 7, $front_range = 1, $mid_range = 5, $rear_range = 1) {
        $pagintion = array();

        $current_page = ($current_page > $total_page) ? $total_page : $current_page;

        // 總頁數小於頁數臨界值，則顯示所有頁碼
        if ($total_page <= $boundary) {
            for ($i = 1; $i <= $total_page; $i ++) {
                $pagintion[] = $i;
            }
        } else {
            $front_end = $front_range; // 前段最後一個頁碼
            $mid_start = $current_page - ceil(($mid_range - 1) / 2); // 中段第一個頁碼
            $mid_end = $current_page + (($mid_range - 1) - ceil(($mid_range - 1) / 2)); // 中段最後一個頁碼
            $rear_start = $total_page - $rear_range + 1; // 後段第一個頁碼
                                                         // 中段第一個頁碼小於等於1，中斷頁碼往左位移
            while ($mid_start <= 1) {
                if ($mid_start < 1)
                    $mid_end += 1;
                $mid_start += 1;
            }

            // 中段第一個頁碼大於等於總頁數，中斷頁碼往右位移
            while ($mid_end >= $total_page) {
                if ($mid_end > $total_page)
                    $mid_start -= 1;
                $mid_end -= 1;
            }

            // 取出需要顯示的頁碼數
            for ($i = 1; $i <= $total_page; $i ++) {
                if ($i <= $front_end || ($i >= $mid_start && $i <= $mid_end) || $i >= $rear_start) {
                    if ($i - (int) end($pagintion) > 1) {
                        $pagintion[] = '...';
                    }

                    $pagintion[] = $i;
                }
            }
        }

        return $pagintion;
    }

    /**
     * 拼装分页的 html ;
     * 样式 for jquery.simplepagination
     *
     * @return string
     */
    function create_html () {
        $html = '<ul class="pagination">';
        // 计算总页数;
        // 计算分页
        $pagintion = $this->pagintion_array($this->current_page, $this->total_page, $this->boundary, $this->front_range, $this->mid_range, $this->rear_range);

        // 上一页
        // 上一页 不可点击
        if ($this->current_page <= 1) {
            $html .= '<li><a href="#">&laquo;</a></li>';
        } else {
            // 上一页可点击
            $page_prev = $this->current_page - 1;
            $url = $this->getUrl($page_prev);
            $html .= '<li><a href="' . $url . '" class="page-link prev" url="' . $url . '" page="' . $page_prev . '">&laquo;</a></li>';
        }

        // 每一页的 链接
        foreach ($pagintion as $page_id) {
            if ($page_id == $this->current_page) {
                $html .= '<li class="active"><span class="current">' . $page_id . '</span></li>';
            } elseif ($page_id == '...') {
                $html .= '<li class="disabled"><span class="ellipse">…</span></li>';
            } else {
                $url = $this->getUrl($page_id);
                $html .= '<li><a href="' . $url . '" class="page-link" page="' . $page_id . '">' . $page_id . '</a></li>';
            }
        }

        // 下一页
        // 下一页 不可点击
        if ($this->current_page >= $this->total_page) {
            $html .= '<li><a href="#">&raquo;</a></li>';
        } else {
            // 下一页 可点击
            $page_next = $this->current_page + 1;
            $url = $this->getUrl($page_next);
            $html .= '<li><a href="' . $url . '" class="page-link next" url="' . $url . '" page="' . $page_next . '">&raquo;</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    private function getUrl ($page) {
        $params = $_GET;
        $params[$this->page_url] = $page;
        $str = http_build_query($params);
        $arr = parse_url($_SERVER['REQUEST_URI']);
        return $arr['path'] . '?' . $str;
    }
}
