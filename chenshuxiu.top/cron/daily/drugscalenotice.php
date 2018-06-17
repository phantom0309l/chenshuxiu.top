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

class DrugScaleNotice extends CronBase
{

    protected static $wcnt = -1;

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:30 按周期, 发送填写服药记录和SNAP-IV评估的提醒';
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

        $sql = "select distinct a.id
                from patients a
                inner join pcards b on b.patientid = a.id
                where a.status = 1 and a.doubt_type = 0 and a.subscribe_cnt > 0 and b.status = 1
                    and b.diseaseid = 1 and b.doctorid != 9 and a.mgtgrouptplid = 0";
        $ids = Dao::queryValues($sql, []);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 5) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                //sunflower项目患者有其他的催用药，催评估规则，此处不催
                if($patient->isInHezuo("Lilly")){
                    echo "\n====礼来合作患者patientid[{$id}]===";
                    continue;
                }

                //生成用药任务
                if($this->needCreateOptask($patient)){
                    $this->createOptask($patient);
                }

                if (false == $this->canSendMsg($patient)) {
                    echo "\n====canNotSendMsg patientid[{$id}]===";
                    continue;
                }

                $this->sendmsg($patient);
                $wcnt = self::$wcnt;
                echo "\n====wcnt[{$wcnt}]===";
                echo "\n====id[{$id}]===" . XDateTime::now();
            }
        }

        $unitofwork->commitAndInit();
    }

    private function needCreateOptask ($patient) {
        $diff = $patient->getDayCntFromBaodao();
        $arr = [28,69,97,125,153];
        //距离报到第28...天
        if (false == in_array($diff, $arr)) {
            return false;
        }

        $createtime = $patient->createtime;
        if($diff == 28){
            $startdate = date('Y-m-d', strtotime($createtime));
            $enddate = date('Y-m-d', strtotime($createtime) + 29 * 86400);
        }

        if($diff == 69 || $diff == 97){
            $startdate = date('Y-m-d', strtotime($createtime) + 28 * 86400);
            $enddate = date('Y-m-d', strtotime($createtime) + 57 * 86400);
        }

        if($diff == 125 || $diff == 153){
            $startdate = date('Y-m-d', strtotime($createtime) + 84 * 86400);
            $enddate = date('Y-m-d', strtotime($createtime) + 113 * 86400);
        }

        //最近4周更新过用药(运营定义的几类药物)，且是在服药状态
        if($this->isDrugSomeMedicine($patient, $startdate, $enddate)){
            return true;
        }

        return false;
    }

    // 患者是否在服用运营定义的药物
    private function isDrugSomeMedicine ($patient, $startdate, $enddate) {
        //择思达，正丁，专注达，阿立哌唑，可乐定透皮贴，硫必利，静灵口服液，多动宁胶囊，智力糖浆，五维赖氨酸颗粒，地牡宁神口服液
        $medicineid_arr = Medicine::$masterMedicines;
        $medicineidstr = implode(',', $medicineid_arr);

        $cond = " and medicineid in ($medicineidstr) ";
        $bind = [];

        $cond .= " and patientid = :patientid ";
        $bind[':patientid'] = $patient->id;

        $cond .= " and createtime >= :startdate ";
        $bind[':startdate'] = $startdate;

        $cond .= " and createtime < :enddate ";
        $bind[':enddate'] = $enddate;

        $cond .= " group by medicineid ";

        $sql = "select * from drugitems where id in (
        select max(id) from drugitems where 1=1 {$cond}
        )";

        $drugitems = Dao::loadEntityList("DrugItem", $sql, $bind);
        foreach ($drugitems as $drugitem) {
            if($drugitem->value > 0){
                return true;
            }
        }
        return false;
    }

    private function createOptask ($patient) {
        $diff = $patient->getDayCntFromBaodao();
        $arr = [
            '28' => 'businessDrug:4week',
            '69' => 'businessDrug:48week_first',
            '97' => 'businessDrug:48week_second',
            '125' => 'businessDrug:1216week_first',
            '153' => 'businessDrug:1216week_second',
        ];

        if (isset($arr[$diff])) {
            $code_subcode = $arr[$diff];
            // 生成任务: 4周基础用药, 8周基础用药
            OpTaskService::createPatientOpTask($patient, $code_subcode);
        }
    }

    public function canSendMsg ($patient) {
        // 距离报到28、56、84(28n)天的患者发消息
        $diff = $patient->getDayCntFromBaodao();

        if ($diff % 7 != 0) {
            return false;
        }

        self::$wcnt = $w = $diff / 7;

        // 不到4周
        if ($w < 4) {
            return false;
        }

        // 25、53、81(25+4n*7)天
        if ($w % 4 == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function sendmsg ($patient) {
        $wxusers = $patient->getWxUsers();
        foreach ($wxusers as $wxuser) {
            if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
                $doctor_name = $patient->doctor->name;
                $str = "{$doctor_name}医生助理";

                $content = $this->getSendContent($patient);

                $first = array(
                    "value" => "",
                    "color" => "#ff6600");
                $keywords = array(
                    array(
                        "value" => $str,
                        "color" => "#aaa"),
                    array(
                        "value" => $content,
                        "color" => "#ff6600"));
                $content = WxTemplateService::createTemplateContent($first, $keywords);

                $openid = $wxuser->openid;

                $url = $this->getSendUrl($patient, $openid);

                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }
        }
    }

    private function getSendContent ($patient) {
        $name = $patient->name;
        $wcnt = self::$wcnt;
        if ($this->onlyUrgeScale($patient)) {
            $str = "{$name}家长，孩子诊后已经有{$wcnt}周了，评估是关注孩子行为问题严重程度的重要参考依据。为了解孩子变化情况，请及时完成症状评估。点击『详情』完成更新！";
        }else {
            $str = "{$name}家长，孩子诊后已经有{$wcnt}周了，用药和孩子情况是治疗中的重要参考依据。为了解孩子用药和变化情况，请及时更新用药记录并完成症状评估。点击『详情』完成更新！";
        }
        return $str;
    }

    private function getSendUrl ($patient, $openid) {
        $wx_uri = Config::getConfig("wx_uri");

        if ($this->onlyUrgeScale($patient)) {
            $url = $wx_uri . "/paper/scale?ename=adhd_iv&openid={$openid}";
        } else {
            $url = $wx_uri . "/drugscale/show?ename=adhd_iv&openid={$openid}";
        }
        return $url;
    }

    private function onlyUrgeScale ($patient) {
        if ($patient->isNoDruging()) {
            $drugsheet = DrugSheetDao::getOneByPatientid($patient->id, " order by thedate asc");
            $nodrugdate = $drugsheet->getCreateDay();

            $diff = XDateTime::getDateDiff(date("Y-m-d", time()), $nodrugdate);
            if(($diff / 7) < 12){
                return true;
            }
        }

        if ($patient->isStopDruging()) {
            $patientmedicineref = PatientMedicineRefDao::getOneByPatient($patient, " and status=0 order by last_drugchange_date desc ");
            $stopdrugdate = $patientmedicineref->last_drugchange_date;

            $diff = XDateTime::getDateDiff(date("Y-m-d", time()), $stopdrugdate);
            if(($diff / 7) < 12){
                return true;
            }
        }
        return false;
    }

}

// //////////////////////////////////////////////////////

$process = new DrugScaleNotice(__FILE__);
$process->dowork();
