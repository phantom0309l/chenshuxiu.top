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

class Fetch_sfda_medicine extends CronBase
{
    public $proxys = [
        [],
        ["221.216.94.77", "808"],
        ["115.231.175.68", "8081"],
    ];

    public static $refs = [
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

    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 05:31 抓取sfda国产药品';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        $max_sfda_id = Sfda_medicineDao::getMaxSfdaid('0');
        echo "max_sfda_id: {$max_sfda_id}\n";

        for ($i = 1; $i <= 100; $i++) {
            $sfda_id = $max_sfda_id + $i;
            echo "\n开始加载第{$i}条数据 sfda_id={$sfda_id}\n";
            $this->getContent($sfda_id);
            echo "\n------------ sleep 10s ------------\n";
            sleep(10);
        }
    }

    private function getContent($sfda_id) {
        try {
            $sfda_medicine = Sfda_medicineDao::getBySfdaid($sfda_id);
        } catch (Exception $e) {
            BeanFinder::clearBean("UnitOfWork");
            BeanFinder::clearBean("DbExecuter");
            $dbExecuter = BeanFinder::get('DbExecuter');
            $dbExecuter->reConnection();

            $sfda_medicine = Sfda_medicineDao::getBySfdaid($sfda_id);
        }
        if ($sfda_medicine instanceof Sfda_medicine) {
            echo "\nsfda_id：{$sfda_id} 重复，跳过\n";
            return;
        }

        $url = 'http://app1.sfda.gov.cn/datasearch/face3/content.jsp?tableId=25&tableName=TABLE25&tableView=%E5%9B%BD%E4%BA%A7%E8%8D%AF%E5%93%81&Id=' . $sfda_id;
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
            $row["sfda_id"] = $sfda_id;

            foreach ($content_data as $item) {
                if (!empty($item)) {
                    $keys = Sfda_medicine::getKeysDefine();
                    $key = $item['key'];
                    if (in_array($key, $keys)) {
                        $row[$key] = $item['value'];
                    }
                }
            }

            $row["en_json"] = '';
            $row["is_en"] = 0;

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
            echo "\n插入 sfda_id：{$sfda_id} ，" . $medicine->name_common . "\n";
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

$process = new Fetch_sfda_medicine(__FILE__);
$process->dowork();