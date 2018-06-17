<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
$patientid = $argv[1];

class Patient_hezuo_pass
{
    public function dopush ($patientid) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $patient = Patient::getById($patientid);

        $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid);
        if($patient_hezuo instanceof Patient_hezuo){
            echo "\nhadCreate";
        }

        if($patient instanceof Patient){
            //生成patient_hezuo
            $row = array();
            $row["patientid"] = $patient->id;
            $row["startdate"] = date("Y-m-d");
            $row["company"] = "Lilly";
            $row["status"] = 1;
            $patient_hezuo = Patient_hezuo::createByBiz($row);

            //此处患者变更至微信（礼来）分组
            $createwxuser = $patient->createuser->createwxuser;
            WxApi::MvWxuserToGroup($createwxuser, 134);
            PushMsgService::sendTxtMsgWhenPassSunflower($createwxuser);

            $doctor = $patient->doctor;
            $first_patient_hezuo = $this->getPateint_hezuoByDoctorid($doctor->id);
            $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid("Lilly", $doctor->id, " and status = 1 order by id ");

            //之前没有入过合作患者，此次是当前医生第一个合作患者入组
            if(false ==  $first_patient_hezuo instanceof Patient_hezuo){
                //找到合作医生
                if($doctor_hezuo instanceof Doctor_hezuo){
                    //记录下合作医生的第一个合作患者入组时间
                    $date = date("Y-m-d");
                    $doctor_hezuo->first_patient_date = $date;

                    if($doctor_hezuo->canSendFirstPatientMsg()){
                        //入第一个合作患者，给礼来接口推送提醒消息
                        $content = "{first: '您好，您有一位患者',keywords: ['{$patient->name}', '{$date}'],remark: '点此查看'}";
                        $lillyservice = new LillyService();
                        $send_status = $lillyservice->sendTemplate(1, $doctor_hezuo->doctor_code, $content);

                        if(200 == $send_status){
                            Debug::warn("礼来合作医生{$doctor->name}入组第一个患者，返回status:[{$send_status}]推送至礼来接口的提醒消息成功！");
                        }else {
                            Debug::warn("礼来合作医生{$doctor->name}入组第一个患者，返回status:[{$send_status}]推送至礼来接口的提醒消息失败！");
                        }
                    }
                }
            }

            $patient_hezuo->pgroup_subtypestrs = "PracticalTraining,ABCTraining,AdvancedTraining";
        }

        $unitofwork->commitAndInit();
        echo "\n====[{$patient->name}]========入组成功！！！" . XDateTime::now();
    }

    private function getPateint_hezuoByDoctorid($doctorid)
    {
        $sql = "select b.* from patients a
            inner join patient_hezuos b on b.patientid=a.id
            where a.doctorid = :doctorid order by b.id";
        $bind = array(':doctorid' => $doctorid);

        return Dao::loadEntity('Patient_hezuo', $sql, $bind);
    }

}

// //////////////////////////////////////////////////////

$process = new Patient_hezuo_pass(__FILE__);
$process->dopush($patientid);
Debug::flushXworklog();
