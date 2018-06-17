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

class Init_studyplan
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select a.id
                from patientpgrouprefs a
                inner join pgroups b on b.id = a.pgroupid
                where a.diseaseid=1 and b.typestr = 'manage'";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $patientpgroupref = PatientPgroupRef::getById($id);
            $course = $patientpgroupref->pgroup->course;
            $courselessonrefs = CourseLessonRefDao::getListByCourse($course);
            $lesson_cnt = count($courselessonrefs);

            $patientpgrouprefid = $patientpgroupref->id;
            $status = $patientpgroupref->status;

            $patientpgroupactitems = PatientPgroupActItemDao::getListByPatientpgrouprefid($patientpgrouprefid, " and objtype= 'LessonUserRef' order by id asc");
            $patientpgroupactitem_cnt = count($patientpgroupactitems);

            //创建
            if($patientpgroupactitem_cnt>0){
                $this->createStudyplanAndStudys($patientpgroupactitems);
            }

            //顺利完成出组的处理
            if($status==2){
                $patientpgroupactitem_paper = PatientPgroupActItemDao::getOneByPatientpgrouprefid($patientpgrouprefid, " and objtype = 'Paper'");
                if($patientpgroupactitem_paper instanceof PatientPgroupActItem){
                    $paper = $patientpgroupactitem_paper->obj;
                    $patientpgroupref->paperid = $paper->id;
                    $paper->objtype = get_class($patientpgroupref);
                    $paper->objid = $patientpgroupref->id;
                }
            }

            $diff = $lesson_cnt - $patientpgroupactitem_cnt;
            if($diff>0){
                $courselessonrefs_notlearn = $this->getNotLearnCourseLessonRefs($course, $patientpgroupactitems);
                //强制出组的处理
                if($status==0){
                    $startdate = $this->getCreateStudyPlanStartdate($patientpgroupref, $patientpgroupactitems);
                    $this->createStudyplans($startdate, $courselessonrefs_notlearn, $patientpgroupref);
                }

                //正在组中的处理
                if($status==1){
                    $this->createStudyplansByOpen_duration($courselessonrefs_notlearn, $patientpgroupref);
                }
            }

            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }

    private function getNotLearnCourseLessonRefs($course, $patientpgroupactitems){
        $result = array();
        $cnt = count($patientpgroupactitems);
        if($cnt == 0){
            $result = CourseLessonRefDao::getListByCourse($course);
        }else{
            $patientpgroupactitem = end($patientpgroupactitems);
            $obj = $patientpgroupactitem->obj;
            $lesson = $obj->lesson;
            $courselessonref = CourseLessonRefDao::getByCourseAndLesson($course->id, $lesson->id);
            $pos = $courselessonref->pos;
            $result = CourseLessonRefDao::getListByCourseid($course->id, " and pos > {$pos} order by id asc");

        }
        return $result;
    }

    private function getCreateStudyPlanStartdate($patientpgroupref, $patientpgroupactitems){
        $startdate = "";
        $cnt = count($patientpgroupactitems);
        if($cnt == 0){
            $startdate = $patientpgroupref->createtime;
        }else{
            $patientpgroupactitem = end($patientpgroupactitems);
            $obj = $patientpgroupactitem->obj;
            $hwkanswersheet = $obj->getHwkAnswerSheet();
            if($hwkanswersheet instanceof XAnswerSheet){
                $createtime = $hwkanswersheet->createtime;
                $startdate = $this->getDateStr($createtime, 1);
            }else{
                $startdate = $patientpgroupref->createtime;
            }
        }
        return $startdate;
    }

    //已有patientpgroupactitem场景
    private function createStudyplanAndStudys($patientpgroupactitems){
        foreach ($patientpgroupactitems as $a) {
            $this->createStudyplanAndStudyImp($a);
        }
    }

    private function createStudyplanAndStudyImp( $patientpgroupactitem ){
        $lessonuserref = $patientpgroupactitem->obj;
        $patientpgroupref = $patientpgroupactitem->patientpgroupref;
        $patientpgrouprefid = $patientpgroupref->id;

        //如果已经存在studyplan则返回
        $studyplans = StudyPlanDao::getListByPatientpgrouprefid($patientpgrouprefid);
        if( count($studyplans) > 0 ){
            return;
        }
        $fiveIds = $this->get5idByPatientPgroupActItem($patientpgroupactitem);

        $testanswersheet = $lessonuserref->getTestAnswerSheet();
        $hwkanswersheet = $lessonuserref->getHwkAnswerSheet();

        if($lessonuserref instanceof LessonUserRef){
            $row = array();
            $row["createtime"] = $patientpgroupref->createtime;
            $row["updatetime"] = $patientpgroupref->updatetime;
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $lessonuserref->lessonid;
            $row["objcode"] = "read";
            $createtime = $lessonuserref->createtime;
            $row["startdate"] = $this->getDateStr($createtime);
            $row["enddate"] = $this->getDateStr($createtime, 1);
            $row["done_cnt"] = 1;
            $studyplan = StudyPlan::createByBiz( $row );
        }

        if($testanswersheet instanceof XAnswerSheet){
            $row = array();
            $row["createtime"] = $patientpgroupref->createtime;
            $row["updatetime"] = $patientpgroupref->updatetime;
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $lessonuserref->lessonid;
            $row["objcode"] = "test";
            $createtime = $testanswersheet->createtime;
            $row["startdate"] = $this->getDateStr($createtime);
            $row["enddate"] = $this->getDateStr($createtime, 1);
            $row["done_cnt"] = 1;
            $studyplan = StudyPlan::createByBiz( $row );

            $row = array();
            $row["createtime"] = $testanswersheet->createtime;
            $row["updatetime"] = $testanswersheet->updatetime;
            $row += $fiveIds;
            $row["studyplanid"] = $studyplan->id;
            $row["xanswersheetid"] = $testanswersheet->id;
            $study = Study::createByBiz( $row );
        }

        if($hwkanswersheet instanceof XAnswerSheet){
            $row = array();
            $row["createtime"] = $patientpgroupref->createtime;
            $row["updatetime"] = $patientpgroupref->updatetime;
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $lessonuserref->lessonid;
            $row["objcode"] = "hwk";
            $createtime = $hwkanswersheet->createtime;
            $row["startdate"] = $this->getDateStr($createtime);
            $row["enddate"] = $this->getDateStr($createtime, 1);
            $row["done_cnt"] = 1;
            $studyplan = StudyPlan::createByBiz( $row );

            $row = array();
            $row["createtime"] = $hwkanswersheet->createtime;
            $row["updatetime"] = $hwkanswersheet->updatetime;
            $row += $fiveIds;
            $row["studyplanid"] = $studyplan->id;
            $row["xanswersheetid"] = $hwkanswersheet->id;
            $study = Study::createByBiz( $row );
        }

    }

    //强制出组场景
    private function createStudyplans($startdate, $courselessonrefs, $patientpgroupref){
        $patientpgrouprefid = $patientpgroupref->id;
        //如果已经存在studyplan则返回
        $studyplans = StudyPlanDao::getListByPatientpgrouprefid($patientpgrouprefid);
        if( count($studyplans) > 0 ){
            return;
        }
        $fiveIds = $this->get5idByPatientPgroupRef($patientpgroupref);
        foreach ($courselessonrefs as $i => $a) {
            $row = array();
            $row["createtime"] = $patientpgroupref->createtime;
            $row["updatetime"] = $patientpgroupref->updatetime;
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $a->lessonid;
            $row["objcode"] = "read";
            $row["startdate"] = $this->getDateStr($startdate, $i);
            $row["enddate"] = $this->getDateStr($startdate, $i+1);
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz( $row );

            $row = array();
            $row["createtime"] = $patientpgroupref->createtime;
            $row["updatetime"] = $patientpgroupref->updatetime;
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $a->lessonid;
            $row["objcode"] = "test";
            $row["startdate"] = $this->getDateStr($startdate, $i);
            $row["enddate"] = $this->getDateStr($startdate, $i+1);
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz( $row );

            $row = array();
            $row["createtime"] = $patientpgroupref->createtime;
            $row["updatetime"] = $patientpgroupref->updatetime;
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $a->lessonid;
            $row["objcode"] = "hwk";
            $row["startdate"] = $this->getDateStr($startdate, $i);
            $row["enddate"] = $this->getDateStr($startdate, $i+1);
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz( $row );
        }
    }

    //正在组中场景
    private function createStudyplansByOpen_duration($courselessonrefs, $patientpgroupref){
        $patientpgrouprefid = $patientpgroupref->id;
        //如果已经存在studyplan则返回
        $studyplans = StudyPlanDao::getListByPatientpgrouprefid($patientpgrouprefid);
        if( count($studyplans) > 0 ){
            return;
        }
        $fiveIds = $this->get5idByPatientPgroupRef($patientpgroupref);
        $startdate = date("Y-m-d");
        foreach ($courselessonrefs as $a) {
            $open_duration = $a->lesson->open_duration;
            $enddate = $this->getDateStr($startdate, $open_duration);

            $row = array();
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $a->lessonid;
            $row["objcode"] = "read";
            $row["startdate"] = $startdate;
            $row["enddate"] = $enddate;
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz( $row );

            $row = array();
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $a->lessonid;
            $row["objcode"] = "test";
            $row["startdate"] = $startdate;
            $row["enddate"] = $enddate;
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz( $row );

            $row = array();
            $row += $fiveIds;
            $row['patientpgrouprefid'] = $patientpgrouprefid;
            $row["objtype"] = "Lesson";
            $row["objid"] = $a->lessonid;
            $row["objcode"] = "hwk";
            $row["startdate"] = $startdate;
            $row["enddate"] = $enddate;
            $row["done_cnt"] = 0;
            $studyplan = StudyPlan::createByBiz( $row );
            $startdate = $enddate;
        }

    }

    private function get5idByPatientPgroupRef($patientpgroupref){
        $fiveIds = array();
        $wxuser = $patientpgroupref->wxuser;
        $patient = $patientpgroupref->patient;
        if($wxuser instanceof WxUser){
            $fiveIds = $wxuser->get5id();
        }else if($patient instanceof Patient){
            $fiveIds = $patient->get5id();
        }
        return $fiveIds;
    }

    private function get5idByPatientPgroupActItem($patientpgroupactitem){
        $fiveIds = array();
        $wxuser = $patientpgroupactitem->wxuser;
        $patient = $patientpgroupactitem->patient;
        if($wxuser instanceof WxUser){
            $fiveIds = $wxuser->get5id();
        }else if($patient instanceof Patient){
            $fiveIds = $patient->get5id();
        }
        return $fiveIds;
    }

    private function getDateStr($createtime, $d=0){
        $time = strtotime($createtime) + $d*86400;
        return date("Y-m-d", $time);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_studyplan.php]=====");

$process = new Init_studyplan();
$process->dowork();

Debug::trace("=====[cron][end][Init_studyplan.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
