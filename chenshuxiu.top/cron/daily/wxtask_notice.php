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

class Wxtask_notice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 20:00 wxtask 提醒';
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

        $now = date("Y-m-d H:i:s", time());
        $ids = Dao::queryValues("select id from wxtasks where endtime > '{$now}'");
        $i = 0;
        foreach ($ids as $id) {
            $wxtask = WxTask::getById($id);
            $this->sendmsg($wxtask);
            echo "\n====[{$id}]===\n";
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }

    public function sendmsg ($wxtask) {
        // 得到模板内容
        if ($wxtask instanceof WxTask) {
            $wxuser = $wxtask->wxuser;
            $starttime = $wxtask->starttime;
            $pos = $this->getPos($starttime);
            $wxtasktplid = $wxtask->wxtasktplid;

            $wxtasktplitem = WxTaskTplItemDao::getOneBy($wxtasktplid, $pos);

            $str = "方寸课堂管理员";
            $content = $wxtasktplitem->title;
            $openid = $wxuser->openid;

            $first = array(
                "value" => "马上打卡，领取今日纪念卡（70、80后的专属回忆）\n",
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            $curritem = $wxtask->getCurrItem();
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/wxtask/one?wxtaskitemid={$curritem->id}&openid={$openid}";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }

    public function getPos ($starttime) {
        $s = strtotime($starttime);
        $e = time();
        $head1 = date("Y-m-d", $s);
        $head2 = date("Y-m-d", $e);
        $onedaytime = 24 * 60 * 60;
        $day = 1 + (strtotime($head2) - strtotime($head1)) / $onedaytime;
        return $day;
    }
}

// //////////////////////////////////////////////////////

$process = new Wxtask_notice(__FILE__);
$process->dowork();
