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

class Lilly_patient_push_article extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:00 Push_Article 推送礼来患教文章';
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

        $today = date('Y-m-d');

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " select id from patient_hezuos where company='Lilly' ";

        $ids = Dao::queryValues($sql);

        $i = 0;
        $courses = CourseDao::getListByGroupstr("lilly");
        $course = $courses[0];

        foreach ($ids as $id) {
            if($i == 1000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $patient_hezuo = Patient_hezuo::getById($id);

            $patient = $patient_hezuo->patient;
            if(false == $patient instanceof Patient){
                continue;
            }

            $diff = $patient_hezuo->getDayCntFromCreate();

            //根据患者的入组时间推算要推送的课文的排序号
            $pos = $this->getLessonPos($diff);

            if($pos == 0){
                continue;
            }

            $courselessonref = CourseLessonRefDao::getByCourseAndPos($course, $pos);
            $lesson = $courselessonref->lesson;

            $wxuser = $patient->getMasterWxUser(1);
            $openid = $wxuser->openid;

            if ($wxuser instanceof WxUser && $wxuser->subscribe == 1) {
                echo "\n\n--------- " . $wxuser->id;

                $str = "向日葵关爱行动";
                $sendContent = "多动症文章第".$pos."篇：".$lesson->title;
                $first = array(
                    "value" => "",
                    "color" => "");
                $keywords = array(
                    array(
                        "value" => $str,
                        "color" => "#aaa"),
                    array(
                        "value" => $sendContent,
                        "color" => "#ff6600"));
                $content = WxTemplateService::createTemplateContent($first, $keywords);
                $url = Config::getConfig("wx_uri") . "/lillyarticle/one?openid={$openid}&courselessonrefid={$courselessonref->id}&menucode=lillyarticle_{$courselessonref->pos}";
                PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
            }

            $i++;
        }

        $unitofwork->commitAndInit();
    }

    private function getLessonPos ($diff) {
        $pos = 0;
        switch ($diff) {
            case '2':
                $pos = 1;
                break;
            case '6':
                $pos = 2;
                break;
            case '10':
                $pos = 3;
                break;
            case '14':
                $pos = 4;
                break;
            case '28':
                $pos = 5;
                break;
            case '56':
                $pos = 6;
                break;
            case '84':
                $pos = 7;
                break;
            case '112':
                $pos = 8;
                break;
            case '140':
                $pos = 9;
                break;
            case '168':
                $pos = 10;
                break;
        }
        return $pos;
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_patient_push_article(__FILE__);
$cnt = $process->dowork();
