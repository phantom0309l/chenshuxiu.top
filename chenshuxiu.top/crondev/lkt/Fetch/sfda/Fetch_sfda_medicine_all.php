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
//include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
//include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

//date_default_timezone_set('UTC');

//TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
require '../QueryList/phpQuery.php';
require '../QueryList/QueryList.php';
require '../../../../../core/util/PinyinUtil.new.class.php';

use QL\QueryList;

class Fetch_sfda_medicine_all
{
    public $proxys = [
        [],
        ["221.216.94.77", "808"],
        ["115.231.175.68", "8081"],
    ];

    public function fetchMedicineName() {
        $file_path = "sfda_names.txt";
        $file = fopen($file_path, "a+");
        echo "创建文件{$file_path}\n";
        // 共1243页
        // TODO: - for ($i = 1; $i <= 1243; $i++) {
        for ($i = 1; $i <= 1243; $i++) {
            echo "------------开始加载 Search 第 {$i} 页------------\n";
            // 搜索页
            $search_url = "http://app2.sfda.gov.cn/datasearchp/gzcxSearch.do?formRender=cx&page={$i}";
            $search_ql = QueryList::Query($search_url,
                [
                    "name" => ['table:eq(15) td[style="padding-left:15px;color:#000000;font-size:12px"]', 'text'],
                ]);
            $search_data = $search_ql->getData(function ($content) {
                return $content["name"];
            });
            foreach ($search_data as $search_item) {
                echo "写入{$search_item}\n";
                fwrite($file, $search_item);
                fwrite($file, "\n");
            }
            sleep(1);
        }
        fclose($file);
    }

    public function fetchMedicineListUrl() {
        $index = 0;
        $page = 1;

        $error_file_path = "sfda_listurls_error.txt";
        if (file_exists($error_file_path)) {
            echo "读取 sfda_listurls_error.txt\n";
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
                $index = $error["index"];
                $page = $error["page"];
            }
        }

        $file_path = "sfda_names.txt";
        if (file_exists($file_path)) {
            $names_file = file($file_path);

            $file = fopen("sfda_listurls.txt", "a+");
            echo "创建文件{$file}\n";

            $proxy_index = 0;
            $proxy = $this->proxys[$proxy_index];

            $i = 0;
            foreach ($names_file as $name) {  // 按行读取
                if (empty($name) || $name == "" || $i < $index) {
                    $i++;
//                    echo "跳过药品 {$name}";
                    continue;
                }
                // TODO: - $all_page = 1;
                $all_page = 1;
                if ($i == $index) {
                    $all_page = $page;
                }
                // TODO: - while (1 == 1) {
                while (1 == 1) {
                    try {
                        echo "开始加载 {$i} SearchAll-{$name}-第 {$all_page} 页\n";
                        // 某个药品的所有列表
                        $name = trim($name);
                        $encode_name = urlencode($name);
                        $all_url = "http://app2.sfda.gov.cn/datasearchp/all.do?tableName=TABLE25&name={$encode_name}&page={$all_page}";
                        $all_ql = QueryList::Query($all_url,
                            [
                                "href" => ['.msgtab a:even', 'href'],
                            ], '', $proxy);
                        if ($all_ql->getState() != 200 || empty($all_ql->getHtml())) {
                            echo $all_url;
                            echo "\n";
                            throw new Exception("The received content is empty!");
                        }
                        $all_data = $all_ql->getData(function ($content) {
                            return "http://app2.sfda.gov.cn" . $content["href"];
                        });
                        if (empty($all_data)) {
                            // 最后一页取不到数据，跳出
                            echo "\n---------------{$name}最后一页，跳出while---------------\n";
                            break;
                        }
                        // 详情
                        foreach ($all_data as $all_item) {
                            fwrite($file, $all_item);
                            fwrite($file, "\n");
                            echo "写入文件 {$all_item}\n";
                        }
                        if (count($all_data) < 15) {
                            // 最后一页数据不足15条，跳出
                            echo "\n---------------{$name}最后一页数据不足15条，跳出while---------------\n";
                            break;
                        }
                        $all_page++;
                        sleep(1);
                    } catch (Exception $e) {
                        $error_file = fopen($error_file_path, "w+");
                        echo "创建error文件{$error_file}\n";

                        $json = json_encode(["name" => $name, "index" => $i, "page" => $all_page], JSON_UNESCAPED_UNICODE);
                        fwrite($error_file, $json);
                        fclose($error_file);

                        echo "\n---------------捕获异常---------------\n";
//                        print_r($e);
                        echo "\n";
                        echo $json;

                        echo "\n--------------- 1 分钟后重试---------------\n";
                        sleep(60);
                        continue;

//                        echo "\n---------------切换代理---------------\n";
//                        $proxy_index++;
//                        if ($proxy_index >= count($this->proxys)) {
//                            $proxy_index = 0;
//                        }
//                        $proxy = $this->proxys[$proxy_index];
//                        if (!empty($proxy)) {
//                            echo "\n---------------代理：{$proxy[0]}:{$proxy[1]}---------------\n";
//                        } else {
//                            echo "\n---------------本机IP，不使用代理---------------\n";
//                        }
//                        continue;
                    }
                }
                $i++;
            }
            fclose($file);
        }
    }

    public function fetchMedicineDetail() {
        $refs = [
            "批准文号" => "piwenhao",
            "产品名称" => "name_common",
            "英文名称" => "name_common_en",
            "商品名" => "name_brand",
            "剂型" => "type_jixing",
            "规格" => "size_chengfen",
            "生产单位" => "company_name",
            "产品类别" => "type_chanpin",
            "批准日期" => "pizhun_date",
            "原批准文号" => "piwenhao_old",
            "药品本位码" => "benweima",
            "药品本位码备注" => "benweima_remark",
        ];

        // 创建文件夹
        $dir = iconv("UTF-8", "GBK", "sfda_data");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, false);
            echo '创建文件夹sfda_data成功';
        } else {
            echo '需创建的文件夹sfda_data已经存在';
        }

        $error_index = 0;
        $error_url = "";

        $error_file_path = "{$dir}/sfda_detail_error.txt";
        if (file_exists($error_file_path)) {
            echo "读取 sfda_detail_error.txt\n";
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
                $error_index = $error["index"];
                $error_url = $error["url"];
            }
        }

        $file_path = "sfda_listurls.txt";
        if (file_exists($file_path)) {
            $urls_file = file($file_path);

            $file = fopen("{$dir}/sfda_details_" . number_format($error_index / 50000) . ".txt", "a+");
            echo "创建文件{$file}\n";

            $proxy_index = 0;
            $proxy = $this->proxys[$proxy_index];

            $i = 0;
            foreach ($urls_file as $url) {  // 按行读取
                $url = trim($url);
                if (empty($url) || $url == "" || !$this->check_url($url) || $i < $error_index) {
                    $i++;
                    echo "跳过 {$url}\n";
                    continue;
                }
                if ($error_index != $i && $i % 50000 == 0) {   // 5w条数据分一个文件
                    fclose($file);
                    $file = fopen("{$dir}/sfda_details_" . number_format($i / 50000) . ".txt", "a+");
                    echo "创建文件{$file}\n";
                }
                // TODO: - while (1 == 1) {
                while (1 == 1) {
                    try {
                        echo "开始加载 {$i} $url\n";
                        // 加载详情页
                        $index1_ql = QueryList::Query($url,
                            [
                                "th" => [".msgtab:first th", 'text'],
                                "td" => [".msgtab:first td", 'text']
                            ], "", $proxy);
                        if ($index1_ql->getState() != 200 || empty($index1_ql->getHtml())) {
                            echo $url;
                            echo "\n";
                            throw new Exception("The received content is empty!");
                        }
                        $index1_data = $index1_ql->getData(function ($content) use ($refs) {
                            if (isset($refs[$content["th"]])) {
                                return [$refs[$content["th"]], $content["td"]];
                            }
                            return [];
                        });
                        echo "开始构建实体数据\n";

                        $row = array();
                        foreach ($index1_data as $item) {
                            if (!empty($item)) {
                                $row[$item[0]] = $item[1];
                            }
                        }
                        // 数组为空，取不到数据，判定为无效url
                        if (empty($row)) {
                            break;
                        }

                        $row["sfda_id"] = $this->getSfda_id($url);
                        $row["remark"] = "";
                        $row["_index_"] = $i;
                        $json = json_encode($row, JSON_UNESCAPED_UNICODE);
                        fwrite($file, $json);
                        fwrite($file, "\n");
                        echo "数据写入文件\n";
                        echo $json;
                        echo "\n\n";
                        sleep(1);
                        break;
                    } catch (Exception $e) {
                        $error_file = fopen($error_file_path, "w+");
                        echo "创建error文件{$error_file}\n";

                        $json = json_encode(["index" => $i, "url" => $url], JSON_UNESCAPED_UNICODE);
                        fwrite($error_file, $json);
                        fclose($error_file);

                        echo "\n---------------捕获异常---------------\n";
//                        print_r($e);
                        echo $json;

                        echo "\n--------------- 1 分钟后重试---------------\n";
                        sleep(60);
                        continue;

//                        echo "\n---------------切换代理---------------\n";
//                        $proxy_index++;
//                        if ($proxy_index >= count($this->proxys)) {
//                            $proxy_index = 0;
//                        }
//                        $proxy = $this->proxys[$proxy_index];
//                        if (!empty($proxy)) {
//                            echo "\n---------------代理：{$proxy[0]}:{$proxy[1]}---------------\n";
//                        } else {
//                            echo "\n---------------本机IP，不使用代理---------------\n";
//                        }
//                        continue;
                    }
                }
                $i++;
            }
            fclose($file);
        }
    }

    public function check_url($url) {
        if (!preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $url)) {
            return false;
        }
        return true;
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

    public function dowork() {
        // 创建文件夹
        $dir = iconv("UTF-8", "GBK", "sfda_data");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, false);
            echo '创建文件夹sfda_data成功';
        } else {
            echo '需创建的文件夹sfda_data已经存在';
        }

        $start_date = XDateTime::now();

        $refs = [
            "批准文号" => "piwenhao",
            "产品名称" => "name_common",
            "英文名称" => "name_common_en",
            "商品名" => "name_brand",
            "剂型" => "type_jixing",
            "规格" => "size_chengfen",
            "生产单位" => "company_name",
            "产品类别" => "type_chanpin",
            "批准日期" => "pizhun_date",
            "原批准文号" => "piwenhao_old",
            "药品本位码" => "benweima",
            "药品本位码备注" => "benweima_remark",
        ];

//        $unitofwork = BeanFinder::get("UnitOfWork");

        $total_count = 1;
        // 共1243页
        // TODO: - for ($i = 1; $i <= 1243; $i++) {
        for ($i = 1; $i <= 1243; $i++) {
            echo "\n开始加载 Search 第 {$i} 页";
            // 搜索页
            $search_url = "http://app2.sfda.gov.cn/datasearchp/gzcxSearch.do?formRender=cx&page={$i}";
            $search_ql = QueryList::Query($search_url,
                [
                    "name" => ['table:eq(15) td[style="padding-left:15px;color:#000000;font-size:12px"]', 'text'],
                ]);
            $search_data = $search_ql->getData(function ($content) {
                return $content["name"];
            });
            foreach ($search_data as $search_item) {
                // TODO: - $all_page = 0;
                $all_page = 121;
                // TODO: - while (1 == 1) {
                while (1 == 1) {
                    $all_page++;
                    echo "\n开始加载 SearchAll-{$search_item}-第 {$all_page} 页";
                    // 某个药品的所有列表
                    $all_url = "http://app2.sfda.gov.cn/datasearchp/all.do?tableName=TABLE25&formRender=cx&searchcx=&name={$search_item}&page={$all_page}";
                    $all_ql = QueryList::Query($all_url,
                        [
                            "href" => ['.msgtab a:even', 'href'],
                        ]);
                    $all_data = $all_ql->getData(function ($content) {
                        return "http://app2.sfda.gov.cn" . $content["href"];
                    });
                    if (empty($all_data)) { // 最后一页取不到数据，跳出
                        echo "\n---------------{$search_item}最后一页，跳出while---------------";
                        break;
                    }
                    // 详情
                    foreach ($all_data as $all_item) {
                        echo "\n开始加载 Index1 详情页";
                        echo $all_item;
                        echo "\n";
                        $index1_ql = QueryList::Query($all_item,
                            [
                                "th" => [".msgtab:first th", 'text'],
                                "td" => [".msgtab:first td", 'text']
                            ]);
                        $index1_data = $index1_ql->getData(function ($content) use ($refs) {
                            if (isset($refs[$content["th"]])) {
                                return [$refs[$content["th"]], $content["td"]];
                            }
                            return [];
                        });
                        echo "\n开始构建实体数据";

                        $row = array();
                        $row["sfda_id"] = $this->getSfda_id($all_item);
                        foreach ($index1_data as $item) {
                            if (!empty($item)) {
                                $row[$item[0]] = $item[1];
                            }
                        }
                        $row["remark"] = "";
                        $json = json_encode($row, JSON_UNESCAPED_UNICODE);
//                        fwrite($file, "\n");
//                        fwrite($file, $json);
                        echo $json;
                        echo "\n";
                        echo "数据写入文件";
                        /*
                        echo "\n---------------{$row["name_common"]} - {$row["company_name"]} 实体数据构建完成---------------";
                        echo "\ncrateByBiz";
                        $medicine = Sfda_medicine::createByBiz($row);
                        echo "\n实体创建完成";
                        $unitofwork->commitAndInit();
                        */
                        echo "\n";
                        $date = XDateTime::now();
                        echo "---------------第 {$total_count} 条 {$date}---------------";
                        $total_count++;
                        sleep(1);
                    }
                    echo "\nSearchAll-{$search_item}-第 {$all_page} 页结束";
                    echo "\n";
                }
//                fclose($file);
            }
            echo "\nSearch 第 {$i} 页结束";
            echo "\n";
        }
        echo "\n";
        echo "start：" . $start_date . "-end：" . XDateTime::now();
        echo "\n";
    }

    public function test($refs) {
        $url = 'http://app2.sfda.gov.cn/datasearchp/index1.do?tableId=24&tableName=TABLE25&tableView=%E5%9B%BD%E4%BA%A7%E8%8D%AF%E5%93%81&Id=83488';
        $ql = QueryList::Query($url,
            [
                "th" => [".msgtab:first th", 'text'],
                "td" => [".msgtab:first td", 'text']
            ]);
        $ql_data = $ql->getData(function ($content) use ($refs) {
            if (isset($refs[$content["th"]])) {
                return [$refs[$content["th"]], $content["td"]];
            }
            return [];
        });

        $row = array();

        $row["sfda_id"] = $this->getSfda_id($url);

        foreach ($ql_data as $a) {
            if (!empty($a)) {
                $row[$a[0]] = $a[1];
            }
        }
        $row["remark"] = "";
        echo $row;
        exit;
    }

}

$process = new Fetch_sfda_medicine_all();
//$process->dowork();
//$process->fetchMedicineName();
//$process->fetchMedicineListUrl($process->proxys[0]);
//$process->fetchMedicineListUrl();
$process->fetchMedicineDetail();

echo "\n";