<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Leku_order_compare
{

    public function dowork() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $data = $this->getData();

        //因通过快递单号查不到shoporder的数据
        $temp1 = [];
        //因快递费用不匹配产生的数据
        $temp2 = [];

        $shoporderids = [];

        $zeros = [];

        //符合规则要求的数组
        $in_rule_arr = [];

        //符合规则且运费匹配上的
        $in_rule_priceeq_arr = [];

        //没有匹配上运费的
        $in_rule_pricenoeq_arr = [];

        foreach ($data as $str) {
            if (empty($str)) {
                continue;
            }
            $arr = explode(",", $str);

            $express_company = $arr[3];
            $express_no = $arr[4];
            $index = $arr[5];
            $express_price = array_slice($arr, -2, 1);
            $express_price = $express_price[0];

            $shopOrder = ShopOrderDao::getShopOrderByExpress_no($express_no);
            if ($shopOrder instanceof shopOrder) {
                $express_price_real_yuan = $shopOrder->getExpress_price_real_yuan();
                if ($express_price_real_yuan == 0) {
                    $zeros[] = $shopOrder->id;
                    echo "\n========express_price[{$express_price}]============\n";
                    //$shopOrder->express_price_real = $express_price * 100;
                }
                if ($express_price_real_yuan > 0 && $express_price > $express_price_real_yuan) {
                    $shoporderids[] = $shopOrder->id;
                    $temp2[] = $index;
                }
                //根据规则填写实际运费
                $shopOrderItems = $shopOrder->getShopOrderItems();
                if (count($shopOrderItems) == 1) {
                    $shopOrderItem = $shopOrderItems[0];
                    $shopproductid = $shopOrderItem->shopproductid;
                    $cnt = $shopOrderItem->cnt;
                    $sf_express_price = $this->getSFExpress_price($shopproductid, $cnt);
                    if ($sf_express_price != null) {
                        $in_rule_arr[] = $index;
                        if ($sf_express_price == $express_price) {
                            $in_rule_priceeq_arr[] = $index;
                        } else {
                            $in_rule_pricenoeq_arr[] = $index . "_" . $shopOrder->id . "_" . $sf_express_price . "_" . $express_price;
                        }
                    }
                }

            } else {
                $temp1[] = $express_company . "__" . $express_no;
            }
        }
        echo "\n\n";
        $temp1 = implode(",", $temp1);
        $temp2 = implode(",", $temp2);
        //$shoporderids = implode(",", $shoporderids);
        $zeros = implode(",", $zeros);
        $in_rule_arr = implode(",", $in_rule_arr);
        $in_rule_priceeq_arr = implode(",", $in_rule_priceeq_arr);
        $in_rule_pricenoeq_arr = implode(",", $in_rule_pricenoeq_arr);
        echo "\n======temp1[{$temp1}]========\n";
        //echo "\n======temp2[{$temp2}]========\n";
        //echo "\n======shoporderids[{$shoporderids}]========\n";
        //echo "\n======zeros[{$zeros}]========\n";
        //echo "\n======in_rule_arr[{$in_rule_arr}]========\n";
        //echo "\n======in_rule_priceeq_arr[{$in_rule_priceeq_arr}]========\n";
        echo "\n======in_rule_pricenoeq_arr[{$in_rule_pricenoeq_arr}]========\n";
        $unitofwork->commitAndInit();
    }

    private function getData() {
        $str = file_get_contents("leku_order.csv");
        $d = explode("\n", $str);
        return $d;
    }

    private function getSFExpress_price($shopproductid, $cnt) {
        $arr = $this->ruleArr();
        $a = $arr[$shopproductid];
        if (empty($a)) {
            return null;
        } else {
            $b = $a[$cnt];
            if (empty($b)) {
                return null;
            } else {
                return $b;
            }
        }
    }

    private function ruleArr() {
        $arr = array(
            //静灵
            305494016 => array(
                1 => 23,
                2 => 23,
                3 => 28,
                4 => 28,
            ),
            //葵花
            282709756 => array(
                4 => 23,
                5 => 23,
                6 => 23,
            ),
            //择思达 10mg
            282796166 => array(
                1 => 18,
                2 => 18,
                3 => 18,
                4 => 18,
                5 => 18,
                6 => 18,
                7 => 18,
                8 => 18,
                9 => 18,
                10 => 18,
            ),
            //择思达 25mg
            282702206 => array(
                1 => 18,
                2 => 18,
                3 => 18,
                4 => 18,
                5 => 18,
                6 => 18,
                7 => 18,
                8 => 18,
                9 => 18,
                10 => 18,
            ),
            //择思达 40mg
            282796036 => array(
                1 => 18,
                2 => 18,
                3 => 18,
                4 => 18,
                5 => 18,
                6 => 18,
                7 => 18,
                8 => 18,
                9 => 18,
                10 => 18,
            ),
            //多动宁 40mg
            297483166 => array(
                1 => 18,
                2 => 18,
                3 => 18,
                4 => 18,
                5 => 18,
                6 => 18,
                7 => 18,
                8 => 18,
            ),
            //安律凡
            287698256 => array(
                1 => 18,
                2 => 18,
                3 => 18,
                4 => 18,
                5 => 18,
                6 => 18,
                7 => 18,
                8 => 18,
                9 => 18,
                10 => 18,
            ),
            //可乐定
            293577176 => array(
                1 => 18,
                2 => 18,
                3 => 18,
                4 => 18,
                5 => 18,
                6 => 18,
                7 => 18,
                8 => 18,
                9 => 18,
                10 => 18,
            ),
        );
        return $arr;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Shunfeng_order_compare.php]=====");

$process = new Leku_order_compare();
$process->dowork();

Debug::trace("=====[cron][end][Shunfeng_order_compare.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
