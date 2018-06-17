<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/4/21
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

class Fetch_sfda_medicine_en_all
{
    public $proxys = [
        [],
        ["221.216.94.77", "808"],
        ["115.231.175.68", "8081"],
    ];

    public static $refs = [
        "注册证号" => "piwenhao",
        "原注册证号" => "piwenhao_old",
        "注册证号备注" => "zhucezhenghaobeizhu",
        "分包装批准文号" => "fenbaozhuangpizhunwenhao",
        "公司名称（中文）" => "company_name",
        "公司名称（英文）" => "company_name_en",
        "地址（中文）" => "address",
        "地址（英文）" => "address_en",
        "国家/地区（中文）" => "country",
        "国家/地区（英文）" => "country_en",
        "产品名称（中文）" => "name_common",
        "产品名称（英文）" => "name_common_en",
        "商品名（中文）" => "name_brand",
        "商品名（英文）" => "name_brand_en",
        "剂型（中文）" => "type_jixing",
        "规格（中文）" => "size_chengfen",
        "包装规格（中文）" => "size_pack",
        "生产厂商（中文）" => "oem",
        "生产厂商（英文）" => "oem_en",
        "厂商地址（中文）" => "oem_address",
        "厂商地址（英文）" => "oem_address_en",
        "厂商国家/地区（中文）" => "oem_country",
        "厂商国家/地区（英文）" => "oem_country_en",
        "发证日期" => "pizhun_date",
        "有效期截止日" => "end_date",
        "分包装企业名称" => "fenbaozhuang_company_name",
        "分包装企业地址" => "fenbaozhuang_company_address",
        "分包装文号批准日期" => "fenbaozhuangpizhun_date",
        "分包装文号有效期截止日" => "fenbaozhuangwenhaoyouxiaoqijiezhi_date",
        "产品类别" => "type_chanpin",
        "药品本位码" => "benweima",
        "药品本位码备注" => "benweima_remark",
    ];

    public function doWork() {
        $dir = iconv("UTF-8", "GBK", "sfda_data");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, false);
            echo '创建文件夹sfda_data成功';
        } else {
            echo '需创建的文件夹sfda_data已经存在';
        }

        $error_page = 1;
        $error_index = 0;
        $error_sfda_id = null;

        $error_file_path = "{$dir}/sfda_medicines_en.error.txt";
        if (file_exists($error_file_path)) {
            echo "读取 sfda_medicines_en.error.txt\n";
            $ef = fopen($error_file_path, "r");
            $error = [];
            while (!feof($ef)) {
                $line = fgets($ef);
                $error = json_decode(trim($line), true);
                break;
            }
            print_r($error);
            echo "\n";
            fclose($ef);
            if (!empty($error)) {
                $error_page = $error["page"];
                $error_index = $error["index"];
                $error_sfda_id = $error["sfda_id"];
            }
        }

        $file_path = "{$dir}/sfda_medicines_en.txt";
        $file = fopen($file_path, "a+");
        echo "创建文件{$file_path}\n";
        // 共265页
        $maxPage = 265;
        $totalCount = $error_index;
        $i = $error_page;
        while (1 == 1) {
            echo "------------开始加载 Search 第 {$i} 页------------\n";
            // 搜索页
            $search_url = "http://app1.sfda.gov.cn/datasearch/face3/search.jsp?curstart={$i}&tableId=36&State=1&bcId=124356651564146415214424405468&State=1&State=1&tableName=TABLE36&State=1&viewtitleName=COLUMN361&State=1&viewsubTitleName=COLUMN354,COLUMN355,COLUMN356,COLUMN823&State=1&tableView=%25E8%25BF%259B%25E5%258F%25A3%25E8%258D%25AF%25E5%2593%2581&State=1";
            $search_ql = QueryList::Query($search_url,
                [
                    "href" => ['tr a', 'href'],
                ]);
            if ($search_ql->getState() != 200 || empty($search_ql->getHtml())) {
                echo "The received content is empty!\n";
                echo "\n--------------- 30 秒钟后重试---------------\n";
                sleep(30);
                continue;
            }
            $search_data = $search_ql->getData(function ($content) {
                $href = $content['href'];
                preg_match("/(?<=\').*?(?=\')/", $href, $matches);
                if (!empty($matches)) {
                    return "http://app1.sfda.gov.cn/datasearch/face3/" . $matches[0];
                }
                return "";
            });
            if (empty($search_data)) {
                echo "数据抓取失败，重新加载 Search 页面\n";
                echo "\n--------------- 30 秒钟后重试---------------\n";
                sleep(30);
                continue;
            }
            echo "获取到 " . count($search_data) . " 条 Content url\n";

            foreach ($search_data as $url) {
                if ($error_sfda_id != null && $this->getSfda_id($url) != $error_sfda_id) {
                    echo "跳过\n";
                    continue;
                }
                $error_sfda_id = null;
                echo "第 {$i} 页 第{$totalCount}个药品\n";
                $this->getContent($url, $file);
                $totalCount++;
                sleep(6);
            }
            $i++;
            if ($i > $maxPage) {
                break;
            }
        }
        echo "共抓取到 {$totalCount} 条数据";
        fclose($file);
    }

    public function getContent($url, $file) {
        echo "开始加载 Content 页面：{$url}\n";
        while (1 == 1) {
            $content_ql = QueryList::Query($url,
                [
                    "key" => ['tr td[width=17%]', 'text'],
                    "value" => ['tr td[width=83%]', 'text'],
                ]);
            if ($content_ql->getState() != 200 || empty($content_ql->getHtml())) {
                echo "The received content is empty!\n";
                echo "\n--------------- 30 秒钟后重试---------------\n";
                sleep(30);
                continue;
            }
            $content_data = $content_ql->getData(function ($content) {
                if (isset(self::$refs[$content['key']])) {
                    return [
                        "key" => self::$refs[$content["key"]],
                        "value" => $content["value"]
                    ];
                }
                return [];
            });

            if (empty($content_data)) {
                echo "数据抓取失败，重新加载 Content 页面\n";
                echo "\n--------------- 30 秒钟后重试---------------\n";
                sleep(30);
                continue;
            }

            $row = [];
            $row["sfda_id"] = $this->getSfda_id($url);

            $en_arr = [];
            foreach ($content_data as $item) {
                if (!empty($item)) {
                    $keys = Sfda_medicine::getKeysDefine();
                    $key = $item['key'];
                    if (in_array($key, $keys)) {
                        $row[$key] = $item['value'];
                    }
                    $en_arr[$key] = $item['value'];
                }
            }

            $row["en_json"] = json_encode($en_arr, JSON_UNESCAPED_UNICODE);
            $row["is_en"] = 1;

            $json = json_encode($row, JSON_UNESCAPED_UNICODE);
            echo "\n\n";
            fwrite($file, $json);
            fwrite($file, "\n");
            echo "数据写入文件\n";
            echo $json;
            echo "\n\n";
            break;
        }
    }

    public function getSfda_id($url) {
        $sfda_id = "";
        $querystr = parse_url($url, PHP_URL_QUERY);
        $querys = explode("&", $querystr);
        foreach ($querys as $query) {
            $data = explode("=", $query);
            if ($data[0] == "Id") {
                $sfda_id = $data[1];
                break;
            }
        }
        return $sfda_id;
    }
}

$process = new Fetch_sfda_medicine_en_all();
$process->doWork();

echo "\n";