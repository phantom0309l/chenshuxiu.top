<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class push_patient_edu extends CronBase
{
    private $lessonidsByCourseid = [];

    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'week';
        $row["title"] = '每周六, 19:15 为患者发送文章';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        // 定义 tag.name 数组
        $map = [
            680895016 => [
                '多动症',
                '多动症倾向'
            ]
        ];

        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        foreach ($map as $courseid => $diagNames) {
            // 获取所有要推送消息的 patientids
            $patientids = $this->getPatientids($diagNames);
            $course = Course::getById($courseid);
            if(false === $course instanceof Course){
                Debug::warn("无 courseid:{$courseid} 的课程");
                continue;
            }

            foreach ($patientids as $patientid) {
                $i++;
                if ($i >= 100) {
                    $i = 0;
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }

                $patient = Patient::getById($patientid);
                if ($patient instanceof Patient) {
                    // 获取到 某个patient 在 courseid 课程下将要推送的 lessonid
                    $lessonid_will_send = $this->getLessonidWillSend($course, $patient->id);

                    if (!empty($lessonid_will_send)) {
                        $lesson = LessonDao::getEntityById('Lesson', $lessonid_will_send);
                        if ($lesson instanceof Lesson) {
                            $wxusers = WxUserDao::getListByPatient($patient);
                            foreach ($wxusers as $wxuser) {
                                $this->sendWxMsg($wxuser, $patient, $lesson, $courseid);
                            }
                        }
                    }
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    // 根据参数 $diagNames patientids
    // 过滤掉 正在sunflower项目中的患者
    private function getPatientids(Array $diagNames) {
        $sql = "SELECT DISTINCT a.objid FROM tagrefs a
                  LEFT JOIN tags b ON a.tagid=b.id
                WHERE a.objtype='Patient' 
                  AND a.objid NOT IN (
                      SELECT patientid FROM patient_hezuos c
                      WHERE c.company='Lilly' 
                        AND c.status=1
                        AND c.patientid IS NOT NULL
                  )
                  AND b.typestr='patientDiagnosis' ";

        if (!empty($diagNames)) {
            $diagNameStr = implode("','",$diagNames);
            $sql .= " AND b.name IN ('{$diagNameStr}')";
        }else {
            return [];
        }

        $patientids = Dao::queryValues($sql);
        return $patientids;
    }

    // 获取将要发送的 lessonid
    private function getLessonidWillSend(Course $course, $patientid) {
        if (empty($this->lessonidsByCourseid[$course->id])) {
            $this->lessonidsByCourseid[$course->id] = $course->getLessonids();
        }
        $lessonids_sended = PatientEduRecordDao::getLessonidsByPatientidCourseid($patientid, $course->id);
        $lessonid_will_send = reset(array_diff($this->lessonidsByCourseid[$course->id], $lessonids_sended));
        return $lessonid_will_send;
    }

    // 发送 WxMsg
    private function sendWxMsg(WxUser $wxuser, Patient $patient, Lesson $lesson, $courseid) {
        $title = $lesson->title;
        $content = $lesson->brief;
        $wx_uri = Config::getConfig("wx_uri");
        $picture = $lesson->picture;

        $url = "{$wx_uri}/patientedurecord/one?gh={$wxuser->wxshop->gh}&courseid={$courseid}&lessonid={$lesson->id}";
        if ($picture instanceof Picture) {
            $img_url = $picture->getSrc(500, 500);
            $result = WxApi::trySendKefuMewsMsg($wxuser, $url, $title, $content, $img_url);
        } else {
            $result = PushMsgService::sendNoticeToWxUserBySystem($wxuser, $title, $content, $url);
        }
        if ($result) {
            $row = array();
            $row["wxuserid"] = $wxuser->id;
            $row["userid"] = $wxuser->userid;
            $row["patientid"] = $patient->id;
            $row["courseid"] = $courseid;
            $row["lessonid"] = $lesson->id;
            $row["viewcnt"] = 0;
            PatientEduRecord::createByBiz($row);
        }
    }
}

// //////////////////////////////////////////////////////

$process = new push_patient_edu(__FILE__);
$process->dowork();
