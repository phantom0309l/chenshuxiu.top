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

// Debug::$debug = 'Dev';
class StockItemNotice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 16:10 库存警戒值提醒';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ename = "StockItemNotice";
        $auditorPushMsgTpl = AuditorPushMsgTplDao::getByEname($ename);
        if(false == $auditorPushMsgTpl instanceof AuditorPushMsgTpl){
            Debug::warn("没有找到监控消息类型[ename:{$ename}]");
            return;
        }

        $wxshop = WxShop::getById(WxShop::WxShopId_ADHD);
        $title = date("Y-m-d");
        $content = "库存警戒提醒";
        $url = $this->getSendUrl($wxshop);

        XContext::setValue("sendOpsTxtMessage", true);
        $auditorPushMsgTplRefs = AuditorPushMsgTplRefDao::getListByAuditorPushMsgTplIdAndCan_ops($auditorPushMsgTpl->id, $wxshop->id);
        foreach ($auditorPushMsgTplRefs as $auditorPushMsgTplRef) {
            // 取到运营
            $auditor = $auditorPushMsgTplRef->auditor;
            if($auditor->isLeave()){
                continue;
            }
            $userid = $auditor->userid;
            $wxusers = WxUserDao::getListByUserIdAndWxShopId($userid, 1);
            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->isOpsOpen()) {
                    $pushMsg = PushMsgService::sendNoticeToWxUserBySystem($wxuser, $title, $content, $url);
                    if ($pushMsg instanceof PushMsg) {
                        $pushMsg->is_monitor_msg = 1; // 标记为监控消息
                    }
                }
            }
        }
        XContext::setValue("sendOpsTxtMessage", false);
        $unitofwork->commitAndInit();
    }

    private function getSendUrl ($wxshop) {
        $wx_uri = Config::getConfig("wx_uri");
        $gh = $wxshop->gh;
        $url = $wx_uri . "/wxmall/StockItemNoticeList?gh={$gh}";
        return $url;
    }
}

// //////////////////////////////////////////////////////

$process = new StockItemNotice(__FILE__);
$process->dowork();
