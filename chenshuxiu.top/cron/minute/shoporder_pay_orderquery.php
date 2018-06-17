<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 每10分钟, 将一小时前的未支付的充值单, 主动查询一下结果
class ShopOrder_pay_orderquery extends CronBase
{

    private $cnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'minute';
        $row["title"] = '每10分钟, 将一小时前的未支付的充值单, 主动查询一下结果';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return $this->cnt > 0;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        // 处理充值单(未支付的 && 未主动查询过 && 一小时前), 最多查询20次
        $time = time() - 3600;
        $time = date('Y-m-d H:i:s', $time);

        $sql = "select id
                from depositeorders
                where recharge_status=0 and version < 21 and orderquery_trade_state = '' and createtime <= '{$time}' ";

        $depositeOrderIds = Dao::queryValues($sql);

        $cnt = count($depositeOrderIds);
        echo "\n==== cnt = {$cnt} ====\n";

        $okCnt = 0;

        foreach ($depositeOrderIds as $i => $depositeOrderId) {

            $unitofwork = BeanFinder::get("UnitOfWork");

            $depositeOrder = DepositeOrder::getById($depositeOrderId);

            $depositeOrder->pay_wxshop->initWxPayConfig();

            echo "\n{$i} / {$cnt} : {$depositeOrder->id} : ";

            try {
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($depositeOrder->fangcun_trade_no);
                $result = WxPayApi::orderQuery($input);

                $depositeOrder->orderquery_time = XDateTime::now();
                $depositeOrder->orderquery_response_content = json_encode($result, JSON_UNESCAPED_UNICODE);
                $depositeOrder->orderquery_result_code = isset($result['result_code']) ? $result['result_code'] : '';
                $depositeOrder->orderquery_trade_state = isset($result['trade_state']) ? $result['trade_state'] : '';
                $depositeOrder->orderquery_trade_state_desc = isset($result['trade_state_desc']) ? $result['trade_state_desc'] : '';

                echo "{$depositeOrder->orderquery_trade_state} : {$depositeOrder->orderquery_trade_state_desc} ";

                // 充值转账, 并尝试处理关联对象
                if ($depositeOrder->orderquery_trade_state == 'SUCCESS') {
                    $depositeOrder->recharge();
                    $depositeOrder->tryProcessObj();

                    echo " : {$depositeOrder->amount} : recharge , tryProcessObj ";
                    $okCnt ++;

                    $this->cronlog_content .= "\n{$depositeOrder->id}:{$depositeOrder->amount}";
                }

                $unitofwork->commitAndInit();
            } catch (Exception $ex) {}

        }

        $this->cronlog_brief = $okCnt;

        $this->cnt = $okCnt;
    }
}

// //////////////////////////////////////////////////////
$process = new ShopOrder_pay_orderquery(__FILE__);
$process->dowork();
