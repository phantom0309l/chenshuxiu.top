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

class Send_treatment_notice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 18:30 每天 发送 就诊须知';
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

        $today = date('Y-m-d');
        $thedate = date('Y-m-d', time() - 3600 * 24 * 5);

        $sql = "select a.id from patients a
            inner join doctors b on b.id=a.doctorid
            where a.status=1 and b.is_treatment_notice=1
            and left(a.createtime, 10) = :thedate ";

        $bind = [];
        $bind[':thedate'] = $thedate;

        $ids = Dao::queryValues($sql, $bind);

        foreach( $ids as $id ){
            $patient = Patient::getById($id);
            $doctor = $patient->doctor;

            if($patient->isInHezuo("Lilly")){
                continue;
            }
            echo "\n" . $patient->name ;

            $lesson =  $doctor->getTreatmentLesson();

            if(false == $lesson instanceof Lesson){
                Debug::warn("{$doctor->name}医生没有绑定就诊须知！{$doctor->id}");
                continue;
            }

            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/lesson/justForShow?lessonid={$lesson->id}";

            $wxusers = WxUserDao::getListByPatient($patient);

            foreach($wxusers as $wxuser){
                if ($wxuser instanceof WxUser && $wxuser->subscribe == 1 && $wxuser->wxshopid == 1) {
                    $first = array(
                        "value" => "{$doctor->name}医生门诊就诊须知",
                        "color" => "");
                    $keyword2 = "{$patient->name}您好。" . $lesson->brief;

                    $keywords = array(
                        array(
                            "value" => "医生助理",
                            "color" => "#ff6600"),
                        array(
                            "value" => $keyword2,
                            "color" => "#ff6600"));
                    $content = WxTemplateService::createTemplateContent($first, $keywords);
                    PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
                }
            }
        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Send_treatment_notice(__FILE__);
$process->dowork();
