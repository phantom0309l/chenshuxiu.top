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

class Output_3313_02
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patientpgrouprefs where startdate > '2017-04-05' and startdate < '2017-04-13' group by patientid";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            echo "[{$id}]\n";
            $patientpgroupref = PatientPgroupRef::getById($id);
            $patient = $patientpgroupref->patient;
            if( $patient instanceof Patient ){
                $temp = array();
                $temp[] = $patient->id;
                $temp[] = $patientpgroupref->startdate;
                $temp[] = $patientpgroupref->pgroup->name;
                $temp[] = $patient->doctor->name;

                $has = $this->hasHuchuConnectedGtSecond($patient, 75, " and auditorid = 10045");
                $temp[] = $has;

                $cnt = $this->getHomeWorkCnt($patientpgroupref);
                $temp[] = $cnt > 0 ? '是' : '否';

                $do_lesson_cnt = $this->getDoLessonCnt($patientpgroupref);
                $temp[] = $do_lesson_cnt;

                $cnt0 = $this->getHomeWorkCntOfOneStudy($patientpgroupref, 0);
                $temp[] = $cnt0;

                $cnt1 = $this->getHomeWorkCntOfOneStudy($patientpgroupref, 1);
                $temp[] = $cnt1;

                $temp[] = $cnt;

                $data[] = $temp;
            }
        }
        $headarr = array(
            "patientid",
            "入组日期",
            "所入组的名称",
            "当前所属医生",
            "是否有首次电话（李洁，大于75秒）",
            "入组日期+7天，是否提交过作业（是/否）",
            "入组日期+7天，提交过几节课的作业",
            "入组日期+7天，第一节课提交的作业次数",
            "入组日期+7天，第二节课提交的作业次数",
            "入组日期+7天，共提交过几次作业",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_331302.xlsx");
        $unitofwork->commitAndInit();
    }

    private function patientHasTags($patient){
        $cond = " AND objtype=:objtype AND objid=:objid AND tagid in (136,137,138,139,140)";
        $bind = [];

        $bind[':objtype'] = "Patient";
        $bind[':objid'] = $patient->id;
        $tagrefs = Dao::getEntityListByCond('TagRef', $cond, $bind);
        return count($tagrefs) > 0;
    }

    private function getDoLessonCnt($patientpgroupref){
        $all = 0;
        $startdate = $patientpgroupref->startdate;
        $thedatetime = strtotime($startdate) + 8*86400;
        $thedate = date("Y-m-d", $thedatetime);
        $studyplans = StudyPlanDao::getListByPatientpgrouprefid($patientpgroupref->id, " and objcode='hwk'");
        foreach($studyplans as $a){
            $studys = StudyDao::getListByStudyplanid($a->id, " and createtime < '{$thedate}'");
            $cnt = count($studys);
            if($cnt >0){
                $all++;
            }
        }
        return $all;
    }

    private function getHomeWorkCnt($patientpgroupref){
        $all = 0;
        $startdate = $patientpgroupref->startdate;
        $thedatetime = strtotime($startdate) + 8*86400;
        $thedate = date("Y-m-d", $thedatetime);
        $studyplans = StudyPlanDao::getListByPatientpgrouprefid($patientpgroupref->id, " and objcode='hwk'");
        foreach($studyplans as $a){
            $studys = StudyDao::getListByStudyplanid($a->id, " and createtime < '{$thedate}'");
            $cnt = count($studys);
            $all += $cnt;
        }
        return $all;
    }

    private function getHomeWorkCntOfOneStudy($patientpgroupref, $studypos){
        $cnt = 0;
        $startdate = $patientpgroupref->startdate;
        $thedatetime = strtotime($startdate) + 8*86400;
        $thedate = date("Y-m-d", $thedatetime);
        $studyplans = StudyPlanDao::getListByPatientpgrouprefid($patientpgroupref->id, " and objcode='hwk'");

        $studyplan = $studyplans[$studypos] ?? null;
        if($studyplan instanceof StudyPlan){
            $studys = StudyDao::getListByStudyplanid($studyplan->id, " and createtime < '{$thedate}'");
            $cnt = count($studys);
        }
        return $cnt;
    }

    //有大于多少秒的呼出接通
    private function hasHuchuConnectedGtSecond($patient, $second, $condEx=""){
        $cdrmeetings = CdrMeetingDao::getListByPatientid($patient->id, $condEx);
        foreach ($cdrmeetings as $a) {
            if($a->cdr_bridge_time > 0){
                $cdr_call_type = $a->cdr_call_type;
                if($cdr_call_type==3 || $cdr_call_type==4){
                    $s = $a->cdr_end_time - $a->cdr_bridge_time;
                    if($s>=$second){
                        return "是";
                    }
                }
            }
        }
        return "否";
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_3313_02.php]=====");

$process = new Output_3313_02();
$process->dowork();

Debug::trace("=====[cron][end][Output_3313_02.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
