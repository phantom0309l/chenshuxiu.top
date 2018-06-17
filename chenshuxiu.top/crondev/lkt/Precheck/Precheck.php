<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/5/18
 * Time: 13:13
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
//include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
//include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

//date_default_timezone_set('UTC');

//TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
require 'QueryList/phpQuery.php';
require 'QueryList/QueryList.php';
require '../../../core/util/PinyinUtil.new.class.php';

use QL\QueryList;

class Precheck
{
    public $base_url = "http://audit.fangcunhulian.cn/";

    public $source_url = "http://audit.fangcunhulian.cn/";

    public $checked_urls = [];

    public $dev_user = "likunting";

    public $cookie = 'cookie_precheck.txt';

    public function doWork() {
        $this->login_cn();
        while (true) {
            $this->run($this->source_url);
        }
    }

    private function run($run_url) {
        if (empty($run_url) || in_array($run_url, $this->checked_urls)) {   // 有效且未经检查
            return;
        }
        $this->checked_urls[] = $run_url;
        $ql = $this->varify_url($run_url);
        if (!empty($ql)) {
            $urls = $this->extract_urls($ql);
            foreach ($urls as $url) {
                $this->run($url);
            }
        }
    }

    // 登录
    public function login_cn() {
        $url = "http://www.fangcunhulian.cn/login/loginpost/";

        $post = [
            "username" => "likunting",
            "password" => "lktlkt",
        ];

        $curl = curl_init();//初始化curl模块
        curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
        curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
        curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie); //设置Cookie信息保存在指定的文件中
        curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息
        curl_exec($curl);//执行cURL
        curl_close($curl);//关闭cURL资源，并且释放系统资源
    }

    // 验证url
    private function varify_url($url) {
        echo "\n";
        echo $url;
        $ql = QueryList::Query($url,
            [
                "href" => ['a', 'href'],
            ], '', null, $this->cookie);
        if ($ql->getState() == 200) {
            echo " --------\033[0;32m" . "valid" . "\x1B[0m--------";
            return $ql;
        } else {
            echo " --------\033[0;31m" . "invalid" . "\x1B[0m--------";
            return null;
        }
    }

    // 提取urls
    private function extract_urls(QueryList $ql) {
        $data = $ql->getData(function ($content) {
            $href = $content["href"];
            if (preg_match('/javascript/i', $href)) {   // 忽略无用的href
                return null;
            }
            if (preg_match('/logout/i', $href)) { //   忽略退出
                return null;
            } elseif (preg_match('/^((http|https)?:\/\/)[^\s]+fangcun/', $href)) {
                return $href;
            } else {
                return $this->base_url . $href;
            }
        });

        return $data;
    }
}

$work = new Precheck();
$work->doWork();