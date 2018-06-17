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

class Gantong_notice
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT id FROM patients
            WHERE status=1 AND
            (id IN (102218315,108012263,104018337,
            108428177,104015361,107815265,
            108038721,106295423,103843019,
            105175211,101679259,106910637,
            105342861,106611807,105179011,
            104214741,107015713,103255117,97) or doctorid=111 or doctorid=11)";

//        $sql = "select id from patients WHERE id IN (108522029, 112011595)";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            $wxuser = $patient->getMasterWxUser(1);
            if($wxuser instanceof WxUser && $wxuser->subscribe == 1){
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
        if ($wxuser instanceof WxUser && $wxuser->subscribe == 1) {
            $str = "方寸儿童管理服务平台管理员";
            $content = $wxuser->user->patient->name."家长您好，我们的感统训练课程已上线。您终于可以在家和孩子一起做感统训练啦！";
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
            $url = "http://wx.fangcunyisheng.com/gantong/choicelesson?openid={$openid}&menucode=gantong_notice";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Gantong_notice.php]=====");

$process = new Gantong_notice();
$process->dowork();

Debug::trace("=====[cron][end][Gantong_notice.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
