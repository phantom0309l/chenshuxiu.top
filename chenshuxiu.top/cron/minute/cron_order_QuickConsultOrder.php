<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/4
 * Time: 15:57
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

// Debug::$debug = 'Dev';
class cron_order_QuickConsultOrder extends CronBase
{

    private $allcnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = '每分钟, 扫描快速咨询表，查看是否有超时的咨询';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return $this->allcnt > 0;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        $cnt = $this->pushList();

        $this->cronlog_brief = "cnt=" . $cnt;

        return $this->allcnt = $cnt;
    }

    public function pushList() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $brief = 0;

        $to_time = date('Y-m-d H:i:s', strtotime('-10 minute', time()));

        $sql = "SELECT *
                FROM quickconsultorders
                WHERE status = 3
                AND is_pay = 1
                AND is_timeout = 0
                AND time_pay <= :to_time";
        $bind = [
            ":to_time" => $to_time
        ];
        $quickconsultorders = Dao::loadEntityList("QuickConsultOrder", $sql, $bind);
        foreach ($quickconsultorders as $quickconsultorder) {
            // 标记超时
            $quickconsultorder->timeout();

            // 退款至原支付账户
            $quickconsultorder->refund();

            $content = '很抱歉，由于当前发起快速咨询的人数较多，未能在10分钟内处理你的快速咨询，我们会将本次快速咨询的费用原路退还。同时，本次快速咨询依旧有效，我们将会安排专人尽快与你沟通，请保持电话和网络通畅。如有其他问题，可以通过微信与我们交流。';
            $wxuser = $quickconsultorder->wxuser;
            PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);

            $brief++;

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;

        $unitofwork->commitAndInit();

        return count($quickconsultorders);
    }
}

// //////////////////////////////////////////////////////

$process = new cron_order_QuickConsultOrder(__FILE__);
$process->dowork();
