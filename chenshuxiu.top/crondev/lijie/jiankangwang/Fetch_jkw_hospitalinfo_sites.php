<?php
/**
 * Created by Atom.
 * User: Jerry
 * Date: 2017/8/2
 * Time: 9:14
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

date_default_timezone_set('UTC');

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
require ROOT_TOP_PATH . '/domain/third.party/QueryList/phpQuery.php';
require ROOT_TOP_PATH . '/domain/third.party/QueryList/QueryList.php';
require '../../../../core/util/PinyinUtil.new.class.php';

use QL\QueryList;

class Fetch_jkw_hospitalinfo_sites
{
    public static $provinces_pinyin = array(
        'shanghai',
        'jiangsu',
        'zhejiang',
        'anhui',
        'fujian',
        'jiangxi',
        'shandong',
        'beijing',
        'tianjin',
        'hebei',
        'shanxi',
        'neimenggu',
        'hubei',
        'hunan',
        'henan',
        'guangdong',
        'guangxi',
        'hainan',
        'liaoning',
        'jilin',
        'heilongjiang',
        'chongqing',
        'sichuan',
        'yunnan',
        'guizhou',
        'xizang',
        'shanxisheng',
        'gansu',
        'qinghai',
        'ningxia',
        'xinjiang'
    );

    public function doWork() {
        $dir = iconv("UTF-8", "GBK", "jiankangwang_data");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, false);
            echo "创建文件夹jiankangwang_data成功\n";
        } else {
            echo "文件夹jiankangwang_data已经存在\n";
        }

        $file_path = "{$dir}/jiankangwang_hospitalinfo_sites.csv";
        $file = fopen($file_path, "a+");
        echo "\n创建文件{$file_path}\n";
        echo "------------开始加载 网站导航------------\n";
        // 搜索页
        $download_urls = fread($file,filesize($file_path));
        $download_urls_arr = explode("\n", $download_urls);

        foreach (self::$provinces_pinyin as $province_pinyin) {
            $url = "http://yyk.99.com.cn/" . $province_pinyin;

            $ql = QueryList::Query($url,
                [
                    "href" => ['.area_list .tablist ul li a', 'href'],
                ]);
            if ($ql->getState() != 200 || empty($ql->getHtml())) {
                echo "The received content is empty!\n";
            }
            $data = $ql->getData(function ($content) {
                return $content['href'];
            });

            if (empty($data)) {
                echo "数据抓取失败\n";
            }

            echo "获取到 " . count($data) . " 条 url\n";

            foreach ($data as $url) {

                if(in_array($url, $download_urls_arr)){
                    echo "------------已经下载过了------------\n";
                    continue;
                }

                fwrite($file, $url . "\n");
                echo "数据写入文件\n";
            }
        }

        fclose($file);
    }
}

$process = new Fetch_jkw_hospitalinfo_sites();
$process->doWork();

echo "抓取完毕！\n";
