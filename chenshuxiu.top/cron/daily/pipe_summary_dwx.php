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
class Pipe_summary_dwx extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        // $row["title"] = '每天, 18:00 汇总前日患者流信息发送给医生';
        $row["title"] = '每天, 07:45 汇总前日患者流信息发送给医生';
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

        $time = time() - 86400;
        $thedate = date("Y-m-d", $time);
        $fromtime = date("Y-m-d 00:00:00", $time);
        $totime = date("Y-m-d 23:59:59", $time);

        $doctorids = $this->getDoctorIds();
        $i = 0;
        $logcontent = '';
        foreach ($doctorids as $id) {
            $doctor = Doctor::getById($id);

            $content = $this->getContent($doctor, $fromtime, $totime, $thedate);
            if (! $content) {
                continue;
            }

            $url = $this->getUrl($thedate);
            Dwx_kefuMsgService::sendTplMsgToDoctorBySystem($doctor, "PatientMgrNotice", $content, $url);

            $i ++;
            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }

            $logcontent .= $doctor->id . " ";
            echo "\n{$doctor->name} [push]";
        }

        $this->cronlog_brief = $i;
        $this->cronlog_content = $logcontent;

        $unitofwork->commitAndInit();
    }

    private function getDoctorIds () {
        $doctorconfigtpl = DoctorConfigTplDao::getByCode('pipe_list_push');

        // 肿瘤不在这个脚本发，单独写脚本发
        $cancer_diseaseids = Disease::getCancerDiseaseidsStr();

        $sql = "select distinct a.id
            from doctors a
            inner join doctorconfigs b on b.doctorid = a.id
            inner join doctordiseaserefs c on c.doctorid = a.id
            where b.doctorconfigtplid = :doctorconfigtplid and b.status = 1 and c.diseaseid not in ($cancer_diseaseids) ";

        $bind = [];
        $bind[':doctorconfigtplid'] = $doctorconfigtpl->id;

        return Dao::queryValues($sql, $bind);
    }

    private function getUrl ($thedate) {
        $dwx_uri = Config::getConfig("dwx_uri");
        $url = $dwx_uri . "/#/patient/active?thedate={$thedate}";

        return $url;
    }

    private function getContent (Doctor $doctor, $fromtime, $totime, $thedate) {
        $send_type_arr = array(
            'WxTxtMsg' => '患者文本消息',
            'WxPicMsg' => '患者图片消息',
            'Paper' => '患者填写量表');

        $send_data = array(
            'WxTxtMsg' => 0,
            'WxPicMsg' => 0,
            'Paper' => 0);

        $ids = $this->getIds($fromtime, $totime, $doctor);

        // 汇总要发的数据存入$send_data
        foreach ($ids as $id) {
            $pipe = Pipe::getById($id);
            if ($pipe instanceof Pipe) {
                $objtype = $pipe->objtype;

                $send_data[$objtype] += 1;
            }
        }

        // 如果，文本消息，图片消息，量表三者一个都没有，则不发消息推送
        $sum = array_sum($send_data);
        if ($sum == 0) {
            return null;
        }

        $str = "\n请点击详情查看{$thedate}活跃患者：\n";
        foreach ($send_data as $key => $num) {
            $desc = $send_type_arr[$key];
            $str .= $desc . " : " . $num . "\n";
        }

        $first = array(
            "value" => "{$doctor->name}医生您辛苦了，{$thedate}提问的患者详情如下：",
            "color" => "#3366ff");

        $keywords = array(
            array(
                "value" => "{$thedate}",
                "color" => ""),
            array(
                "value" => count($ids),
                "color" => ""));
        $remark = $str;
        $content = WxTemplateService::createTemplateContent($first, $keywords, $remark);

        return $content;
    }

    private function getIds ($fromtime, $totime, Doctor $doctor) {
        // #4130, 协和风湿免疫科, 王迁 也能看 (医生自己和监管的医生)
        $doctorids_str = $doctor->getDoctorIdsStr();

        $sql = "select a.id
            from pipes a
            inner join patients b on b.id=a.patientid
            where b.doctorid in ({$doctorids_str})
            and a.createtime >= :fromtime  and a.createtime <= :totime
            and a.objtype in ('WxTxtMsg','WxPicMsg','Paper')
            order by a.id desc ";

        $bind = [];
        $bind[':fromtime'] = $fromtime;
        $bind[':totime'] = $totime;

        $ids = Dao::queryValues($sql, $bind);

        return $ids;
    }
}

// //////////////////////////////////////////////////////

$process = new Pipe_summary_dwx(__FILE__);
$process->dowork();
