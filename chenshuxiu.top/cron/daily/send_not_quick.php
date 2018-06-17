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
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
// Debug::$debug = 'Dev';
class Send_not_quick extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = "每天19:05, 4月1日后入组的患者如果没有购买快速通行证的，在入组后第30天的下午7点钟自动发送提醒消息";
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $title = "能让提问1小时回复的“快速通行证”是什么？";
        $img_url = "https://photo.fangcunyisheng.com/f/9f/f9fd3cf66ce66e2b7eb4e7cade1cde81.png";
        $content = "";
        $wx_uri = Config::getConfig("wx_uri");

        $brief = 0;
        $logcontent = '';

        $ids = $this->getIds();

        foreach ($ids as $id) {
            $patient = Patient::getById($id);

            $wxusers = WxUserDao::getListByPatient($patient);
            foreach ($wxusers as $wxuser) {
                $url = "{$wx_uri}/lesson/justforshow?lessonid=628217816&gh={$wxuser->wxshop->gh}";
                WxApi::trySendKefuMewsMsg($wxuser, $url, $title, $content, $img_url);
            }

            $brief ++;
            $logcontent .= "{$id->id},";

            echo "{$brief} {$id}\n";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_content = $logcontent;
        $this->cronlog_brief = "cnt={$brief}";

        $unitofwork->commitAndInit();
    }

    public function getIds () {
        $starttime = date('Y-m-d', strtotime("-1 month", time()));
        $endtime = date('Y-m-d', strtotime("+1 day" , strtotime($starttime)));

        $cancer_diseaseids = Disease::getCancerDiseaseidsStr();

        $sql = "select id
                from patients
                where diseaseid in ({$cancer_diseaseids}) and createtime >= '2018-04-01' and createtime >= '{$starttime}' and createtime < '$endtime'
                and id not in (
                    select patientid
                    from serviceorders
                    where is_pay = 1
                    group by patientid
                )";

        echo "\n" . $sql . "\n";
        return Dao::queryValues($sql);
    }
}

// //////////////////////////////////////////////////////

$process = new Send_not_quick(__FILE__);
$process->dowork();
