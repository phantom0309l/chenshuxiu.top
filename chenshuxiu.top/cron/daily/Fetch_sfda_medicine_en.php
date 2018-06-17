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
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

require ROOT_TOP_PATH . '/domain/third.party/QueryList/phpQuery.php';
require ROOT_TOP_PATH . '/domain/third.party/QueryList/QueryList.php';

use QL\QueryList;

class Fetch_sfda_medicine_en extends CronBase
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

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 06:01 抓取sfda进口药品';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $max_sfda_id = Sfda_medicineDao::getMaxSfdaid(1);
        echo "max_sfda_id: {$max_sfda_id}\n";
        $max_sfda_id = $max_sfda_id - 1000000;

        for ($i = 1; $i <= 100; $i++) {
            $sfda_id = $max_sfda_id + $i;
            echo "\n开始加载第{$i}条数据 sfda_id={$sfda_id}\n";
            $this->getContent($sfda_id);
            echo "\n------------ sleep 10s ------------\n";
            sleep(10);
        }
    }

    public function getContent($sfda_id) {
        $sfda_id_fix = $sfda_id + 1000000;
        try {
            $sfda_medicine = Sfda_medicineDao::getBySfdaid($sfda_id_fix);
        } catch (Exception $e) {
            BeanFinder::clearBean("UnitOfWork");
            BeanFinder::clearBean("DbExecuter");
            $dbExecuter = BeanFinder::get('DbExecuter');
            $dbExecuter->reConnection();

            $sfda_medicine = Sfda_medicineDao::getBySfdaid($sfda_id_fix);
        }
        if ($sfda_medicine instanceof Sfda_medicine) {
            echo "\nsfda_id：{$sfda_id_fix} 重复，跳过\n";
            return;
        }

        $url = 'http://app1.sfda.gov.cn/datasearch/face3/content.jsp?tableId=36&tableName=TABLE36&tableView=%E8%BF%9B%E5%8F%A3%E8%8D%AF%E5%93%81&Id=' . $sfda_id;
        echo "{$url}\n";

        $retry_count = 0;
        // 因为现在有补漏脚本了，所以最多重试3次
        while ($retry_count < 3) {
            if ($retry_count > 0) {
                echo "\n--------------- 30 秒钟后重试---------------\n";
                sleep(30);
            }
            $retry_count++;

            $content_ql = QueryList::Query($url,
                [
                    "key" => ['tr td[width=17%]', 'text'],
                    "value" => ['tr td[width=83%]', 'text'],
                ]);
            if ($content_ql->getState() != 200 || empty($content_ql->getHtml())) {
                echo "The received content is empty!\n";
                continue;
            }
            $content_data = $content_ql->getData(function ($content) {
                $key = self::$refs[$content["key"]];
                if (isset($key)) {
                    return [
                        "key" => $key,
                        "value" => $content["value"]
                    ];
                }
                return [];
            });

            if (empty($content_data)) {
                if ($this->isValid($content_ql)) {
                    echo "数据抓取失败，重新加载 Content 页面\n";
                    continue;
                } else {
                    echo "无效sfda_id {$sfda_id} \n";
                    break;
                }
            }

            $row = [];
            $row["sfda_id"] = $sfda_id_fix;

            $en_data = [];
            foreach ($content_data as $item) {
                if (!empty($item)) {
                    $keys = Sfda_medicine::getKeysDefine();
                    $key = $item['key'];
                    if (in_array($key, $keys)) {
                        $row[$key] = $item['value'];
                    }
                    $en_data[$key] = $item['value'];
                }
            }

            $row["en_json"] = json_encode($en_data, JSON_UNESCAPED_UNICODE);
            $row["is_en"] = 1;

            try {
                $medicine = Sfda_medicine::createByBiz($row);
                BeanFinder::get("UnitOfWork")->commitAndInit();
            } catch (Exception $e) {
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();

                $medicine = Sfda_medicine::createByBiz($row);
                BeanFinder::get("UnitOfWork")->commitAndInit();
            }
            echo "\n插入 sfda_id：{$sfda_id_fix} ，" . $medicine->name_common . "\n";
            break;
        }
    }

    private function isValid($ql) {
        $data = $ql->setQuery([
            "text" => ['span', 'text'],
        ])->getData(function ($content) {
            return $content['text'];
        });
        if (empty($data)) {
            return true;
        } else {
            $bool = false;
            foreach ($data as $item) {
                if ($item == "没有相关信息") {
                    $bool = false;
                    break;
                } else {
                    $bool = true;
                }
            }
            return $bool;
        }
    }
}

// //////////////////////////////////////////////////////

$process = new Fetch_sfda_medicine_en(__FILE__);
$process->dowork();