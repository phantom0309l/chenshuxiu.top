<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/6/22
 * Time: 11:00
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

date_default_timezone_set('UTC');

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
require '../QueryList/phpQuery.php';
require '../QueryList/QueryList.php';
require '../../../../../core/util/PinyinUtil.new.class.php';

use QL\QueryList;

class Fetch_sitemap
{
    public $proxys = [
        [],
        ["221.216.94.77", "808"],
        ["115.231.175.68", "8081"],
    ];

    public static $refs = [
    ];

    public function doWork() {
        $dir = iconv("UTF-8", "GBK", "jianke_data");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, false);
            echo "创建文件夹jianke_data成功\n";
        } else {
            echo "文件夹jianke_data已经存在\n";
        }

        $file_path = "{$dir}/jianke_sitemap.txt";
        $file = fopen($file_path, "a+");
        echo "\n创建文件{$file_path}\n";
        echo "------------开始加载 网站导航------------\n";
        // 搜索页
        $url = "https://www.jianke.com/help/sitemap.html";
        $ql = QueryList::Query($url,
            [
                "href" => ['.medicine .medicine_column .map_com h3 a', 'href'],
            ]);
        if ($ql->getState() != 200 || empty($ql->getHtml())) {
            echo "The received content is empty!\n";
        }
        $data = $ql->getData(function ($content) {
            $href = $content['href'];
            return "https:{$href}";
        });
        if (empty($data)) {
            echo "数据抓取失败\n";
        }
        echo "获取到 " . count($data) . " 条 url\n";

        foreach ($data as $url) {
            echo "\n\n";
            fwrite($file, $url);
            fwrite($file, "\n");
            echo "数据写入文件\n";
        }
        fclose($file);
    }
}

$process = new Fetch_sitemap();
$process->doWork();

echo "\n";