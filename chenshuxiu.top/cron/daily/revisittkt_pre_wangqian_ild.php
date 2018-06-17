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

class Revisittkt_pre_wangqian_ild extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 12:00 复诊前第3天, 发送给患者, 填写量表: 复诊已做检查项目明确';
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

        $day_cnt = 3; // 复诊日期=今天+3天

        $day_from = date('Y-m-d', time() + 3600 * 24 * $day_cnt); // 今天+3天
        $day_to = date('Y-m-d', time() + 3600 * 24 * ($day_cnt + 1)); // 今天+4天

        // revisittkt 有效 且 运营通过
        $sql = "SELECT rt.*
                FROM revisittkts rt
                INNER JOIN schedules sh ON rt.scheduleid = sh.id
                WHERE sh.doctorid = 32 AND sh.diseaseid = 2 AND rt.status = 1 AND rt.auditstatus = 1
                AND rt.thedate >= :day_from AND rt.thedate < :day_to ";

        $bind = [];
        $bind[':day_from'] = $day_from;
        $bind[':day_to'] = $day_to;

        $revisittkts = Dao::loadEntityList('RevisitTkt', $sql, $bind);

        foreach ($revisittkts as $revisittkt) {

            $wx_uri = Config::getConfig('wx_uri');
            $url = "{$wx_uri}/paper/wenzhen/?papertplid=274498246";

            $wxuser = $revisittkt->wxuser;
            if (false == $wxuser instanceof WxUser) {
                $wxuser = $revisittkt->patient->getMasterWxUser(6);
            }

            $first = array(
                "value" => '复诊已做检查项目明确',
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => "{$revisittkt->doctor->name}医生随访团队",
                    "color" => "#999999"),
                array(
                    "value" => '在您复诊前需要对您的检查项目进行了解与明确。请您尽快填写。',
                    "color" => "#ff6600"));

            $content = WxTemplateService::createTemplateContent($first, $keywords);

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, 'doctornotice', $content, $url);
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Revisittkt_pre_wangqian_ild(__FILE__);
$process->dowork();
