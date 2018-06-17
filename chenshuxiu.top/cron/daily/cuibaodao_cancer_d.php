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
// 首次扫码后第4天、8天、15天、22天…… 之后每7天发送一次
// 这个脚本每天10:00执行一次
class CuiBaoDao_Cancer_d extends CronBase
{

    private function getConfig () {
        $config = array(
            "typestr" => "cuibaodao_cancer[d]",
            "content" => "首次扫码后第4天、8天、15天、22天…… 之后每7天发送一次");
        return $config;
    }

    private function filter ($wxuser, $typestr = "") {
        $send_day_typestr = '';

        if (false == $wxuser instanceof WxUser) {
            return '';
        }
        //方寸儿童管理服务平台关注了方寸院外管理的wxuser
        $ignore_arr = array(556556756,556599116,556602136,556613566,556642666,
        556648806,556723236,556734236,556602136,556799206,556859636,556873456,
        556939286,556993416,557025066,557045096,557082916,557127466,557169466,
        557184496,557197566,557246416,557450746,557492466,557499876,557834776,
        557850206,557857126,557883166,557948016,557951626,557952316,557953176,
        557971046,557979276,557988466,557989886,558005646,558017986,558041436);
        if(in_array($wxuser->id, $ignore_arr)){
            return '';
        }

        $today_thedate = date('Y-m-d', time());
        $wx_createdate = date('Y-m-d', strtotime($wxuser->createtime));

        // 通过comment判断这个催报到类型是不是已经催过了
        $comments = CommentDao::getListByObjtypeObjidTypestr("WxUser", $wxuser->id, $typestr, ' order by createtime asc ');
        if (count($comments) > 0) {
            $comment = array_pop($comments);
            $title_type = $comment->title;

            $comment_createdate = date('Y-m-d', strtotime($comment->createtime));

            $daycnt = (strtotime($today_thedate) - strtotime($comment_createdate)) / (24 * 60 * 60);

            switch ($title_type) {
                case '4':
                    // 第8天
                    if ($daycnt == 4) {
                        $send_day_typestr = '8';
                    }
                    break;
                case '8':
                    // 第15天
                    if ($daycnt == 7) {
                        $send_day_typestr = '15';
                    }
                    break;
                case '15':
                    // 第22天
                    if ($daycnt == 7) {
                        $send_day_typestr = '22';
                    }
                    break;
                case '22':
                    // 第22天之后每7天发一次
                    if ($daycnt == 7) {
                        $send_day_typestr = '7';
                    }
                    break;
                case '7':
                    // 第15天
                    if ($daycnt == 7) {
                        $send_day_typestr = '7';
                    }
                    break;
                default:
                    break;
            }
        } else {
            $daycnt = (strtotime($today_thedate) - strtotime($wx_createdate)) / (24 * 60 * 60);

            if ($daycnt == 4) {
                $send_day_typestr = '4';
            }
        }

        return $send_day_typestr;
    }

    private function getSendContent ($wxuser) {
//         $content = "管理提醒：您尚未报到请及时报到，如有问题请咨询医生或拨打电话010-60648881。";
        $wxshop = $wxuser->wxshop;
        $wx_uri = Config::getConfig("wx_uri");

        $content = "管理提醒：您尚未提交个人信息，请及时提交个人信息。";
        $content .= "\n\n<a href=\"{$wx_uri}/baodao/baodao?openid={$wxuser->openid}\">>点击这里提交个人信息</a>。";

        return $content;
    }

    private function getWxuserIds () {
        $sql = "select a.id
                from wxusers a
                inner join users b on b.id = a.userid
                where a.subscribe=1 and a.wxshopid in (8,15,19,21) and a.ref_pcode='DoctorCard'
                and b.patientid=0 ";
        return Dao::queryValues($sql);
    }

    private function sendmsg ($wxuser, $content) {
        if ($wxuser instanceof WxUser) {
            PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
        }
    }

    private function createComment ($wxuser, $typestr, $content, $title_type) {
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
        $row['title'] = $title_type;
        $row['content'] = $content;
        Comment::createByBiz($row);
    }

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天早上10:00跑，首次扫码后第4天、8天、15天、22天…… 之后每7天发送一次';
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

    /**
     *
     * {@inheritdoc}
     *
     * @see CronBase::doworkImp()
     */
    protected function doworkImp () {
        $config = $this->getConfig();
        $typestr = $config["typestr"];
        $content = $config["content"];

        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getWxuserIds();
        $brief = 0;
        $logcontent = "";
        $i = 0;
        $k = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $wxuser = WxUser::getById($id);

            $title_type = $this->filter($wxuser, $typestr);
            if ($title_type) {
                $k ++;
                $send_content = $this->getSendContent($wxuser);
                $this->sendmsg($wxuser, $send_content);
                $this->createComment($wxuser, $typestr, $content, $title_type);

                $logcontent .= $wxuser->id . ",";
            }
        }

        $brief = $k;

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        $unitofwork->commitAndInit();
    }
}

$process = new CuiBaoDao_Cancer_d(__FILE__);
$process->dowork();
