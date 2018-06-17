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
// 触发条件：2小时后还没报到，且没发送过催报到消息的
// 这个脚本每10分钟执行一次
class CuiBaoDao_Cancer_m extends CronBase
{

    private function getConfig () {
        $starttime = date("Y-m-d H:i:s", time() - 2 * 3600 - 10 * 60); // 两个小时10分钟之前
        $endtime = date("Y-m-d H:i:s", time() - 2 * 3600); // 两个小时之前
        $config = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "typestr" => "cuibaodao_cancer[m]",
            "content" => "2小时后还没报到，且没发送过催报到消息的");
        return $config;
    }

    private function filter ($wxuser, $typestr = "") {
        if (false == $wxuser instanceof WxUser) {
            return true;
        }

        // 通过comment判断这个催报到类型是不是已经催过了
        $comments = CommentDao::getListByObjtypeObjidTypestr("WxUser", $wxuser->id, $typestr);
        if (count($comments) > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function getSendContent ($wxuser) {
        // $content =
        // "由于医生助理人员有限且工作分配需要一定时间，为不耽误病情请您及时报到。如有问题请咨询医生或拨打电话010-60648881。";
        $wxshop = $wxuser->wxshop;
        $wx_uri = Config::getConfig("wx_uri");

        $doctor_name = '';
        if ($wxuser->doctor instanceof Doctor) {
            $doctor_name = $wxuser->doctor->name;
        }

        $content = "您只关注了微信公众号，还未报到， {$doctor_name}医生团队无法确定您是谁。请及时提交个人信息。以便您获得{$doctor_name}医生团队针对性的院外指导。";
        $content .= "\n\n<a href=\"{$wx_uri}/baodao/baodao?openid={$wxuser->openid}\">>点击这里提交个人信息</a>";

        return $content;
    }

    private function getWxuserIds ($starttime, $endtime) {
        $bind = [];
        $bind[":starttime"] = $starttime;
        $bind[":endtime"] = $endtime;
        print_r($bind);
        $sql = "select a.id
                from wxusers a
                inner join users b on b.id = a.userid
                where a.subscribe=1 and a.wxshopid in (12,15,19,21,23)
                and a.createtime >= :starttime and a.createtime < :endtime
                and b.patientid=0 ";
        return Dao::queryValues($sql, $bind);
    }

    private function sendmsg ($wxuser, $content) {
        if ($wxuser instanceof WxUser) {
            PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
        }
    }

    private function createComment ($wxuser, $typestr, $content) {
        if (false == $wxuser instanceof WxUser) {
            return;
        }
        $row = array();
        $row['wxuserid'] = $wxuser->id;
        $row['userid'] = $wxuser->userid;
        $row["doctorid"] = $wxuser->doctorid;
        $row['objtype'] = "WxUser";
        $row['objid'] = $wxuser->id;
        $row['typestr'] = $typestr;
        $row['content'] = $content;
        Comment::createByBiz($row);
    }

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = '每10分钟执行一次,扫码关注后2小时还没报到，且没发送过催报到消息的,发一次催报到消息 ';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return $this->cronlog_brief > 0;
    }

    // 重载
    protected function doworkImp () {
        $config = $this->getConfig();
        $starttime = $config["starttime"];
        $endtime = $config["endtime"];
        $typestr = $config["typestr"];
        $content = $config["content"];

        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getWxuserIds($starttime, $endtime);
        $cnt = count($ids);
        $logcontent = "";
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $wxuser = WxUser::getById($id);
            if ($this->filter($wxuser, $typestr)) {
                continue;
            }

            $send_content = $this->getSendContent($wxuser);
            $this->sendmsg($wxuser, $send_content);
            $this->createComment($wxuser, $typestr, $content);

            $logcontent .= $wxuser->id . " ";
        }

        $this->cronlog_brief = $cnt;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content}";

        $unitofwork->commitAndInit();
    }
}

$process = new CuiBaoDao_Cancer_m(__FILE__);
$process->dowork();
