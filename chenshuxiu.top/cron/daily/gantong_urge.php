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

class Gantong_urge
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $yesterday = date('Y-m-d', time() - 86400);

        $sql = "SELECT max(a.id) as xanswersheetid
                FROM xanswersheets a
                INNER JOIN xanswers b ON b.xanswersheetid=a.id
                INNER JOIN xquestions c ON c.id=b.xquestionid
                WHERE c.ename='gantong_hwk_radio'
                GROUP BY a.wxuserid";
        $ids = Dao::queryValues($sql, []);
        $i = 0;
        foreach ($ids as $id) {
            $xansersheet = XAnswerSheet::getById($id);
            $wxuser = $xansersheet->wxuser;

            if(($wxuser instanceof WxUser) && (substr($xansersheet->createtime, 0, 10)==$yesterday)){
                $this->sendmsg($wxuser);
                echo "\n====[{$id}]===\n";
                $i ++;
                if ($i >= 50) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }

        $unitofwork->commitAndInit();
    }

    public function sendmsg ($wxuser) {
        // 得到模板内容
        if ($wxuser->subscribe == 1) {
            $str = "方寸儿童管理服务平台管理员";
            $content = "课程已经统一更新了，我们开始训练吧！";
            $openid = $wxuser->openid;

            $first = array(
                "value" => "",
                "color" => "");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);
            $url = "http://wx.fangcunyisheng.com/gantong/choicelesson?openid={$openid}&menucode=gantong_urge";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Gantong_urge.php]=====");

$process = new Gantong_urge();
$process->dowork();

Debug::trace("=====[cron][end][Gantong_urge.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
