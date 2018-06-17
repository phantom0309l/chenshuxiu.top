<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class ExportPatient {
    public function __construct() {

    }

    public function run() {
        $sql = "
            SELECT DISTINCT (patientid) FROM checkups a
            INNER JOIN checkuptpls b ON a.checkuptplid = b.id
            INNER JOIN xanswers c ON a.xanswersheetid = c.xanswersheetid
            INNER JOIN xquestions d ON c.xquestionid=d.id

            WHERE a.checkuptplid='103284836' 
            AND d.content IN ('FVC%')
            AND c.content >= 40

            AND a.patientid IN (

                SELECT DISTINCT a.patientid FROM checkups a
                INNER JOIN checkuptpls b ON a.checkuptplid = b.id
                INNER JOIN xanswers c ON a.xanswersheetid = c.xanswersheetid
                INNER JOIN xquestions d ON c.xquestionid=d.id

                WHERE a.checkuptplid='103284836' 
                AND d.content IN ('DLco%') AND c.content > 30 AND c.content <90
                AND a.`patientid` IN (
                    SELECT patientid FROM pcards WHERE doctorid=32
                    AND 
                    (
                        LOCATE('硬皮病', complication) 
                        OR
                        LOCATE('系统性硬化症', complication)
                        OR
                        LOCATE('SSc', complication)
                        OR
                        LOCATE('dl-SSc', complication)
                        OR
                        LOCATE('lc-SSc', complication)
                    )
                )
            )
            ";
        $patientids = Dao::queryValues($sql);
        $patients = Dao::getEntityListByIds('Patient', $patientids);
        $doctorid = 32;
        $sets = [];
        foreach ($patients as $patient) {
            $pcard = PcardDao::getByPatientidDoctorid($patient->id, $doctorid);
            $set = [];
            $set['name'] = $patient->name;
            $set['patientid'] = $patient->id;
            $set['mobile'] = $patient->getMasterMobile(). "\t";
            $set['complication'] = $pcard->complication;
            $set['disease_time'] = '未知';
            $set['live_status'] = $patient->is_live == 1 ? "正常" : "死亡";
            $sql = "SELECT t.question_name, t.id, t.createtime, t.content FROM (
                SELECT d.content AS question_name, c.* FROM checkups a
                INNER JOIN checkuptpls b ON a.checkuptplid = b.id
                INNER JOIN xanswers c ON a.xanswersheetid = c.xanswersheetid
                INNER JOIN xquestions d ON c.xquestionid=d.id

                WHERE a.checkuptplid='103284836' 
                AND d.content IN ('FEV1%','FVC%', 'TLC%','DLCo%')

                AND a.`patientid` = {$patient->id}

                ORDER BY a.check_date DESC
            ) AS t 
            GROUP BY t.question_name";
            $one = Dao::queryRows($sql);
            $set['lung_data'] = $one;
            //计算当前服用药物
            $sql = "
                SELECT t.* FROM (
                SELECT LEFT(a.createtime, 10) AS first_start_date, c.name FROM patientmedicinepkgs a
                INNER JOIN patientmedicinepkgitems b ON a.id=b.patientmedicinepkgid
                INNER JOIN medicines c ON b.medicineid=c.id
                WHERE a.patientid='{$patient->id}' ORDER BY a.`createtime` DESC 
                ) AS t GROUP BY t.name
                ";
            $one = Dao::queryRows($sql);
            if (empty($one)) {
                $sql = "
                    SELECT a.first_start_date, a.status, b.name FROM `patientmedicinerefs` a
                    INNER JOIN medicines AS b ON a.medicineid = b.id
                    WHERE patientid = '{$patient->id}' AND status=1";
                $one = Dao::queryRows($sql);
            }
            $set['drug_data'] = $one;
            $sets[] = $set;
        }
        $this->export($sets);
    }

    private function export($data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        echo "姓名,手机号,诊断,发病时间,状态,末次肺功能数据,目前用药\n";
        foreach ($data as $one) {
            $str = "$one[name],$one[mobile],$one[complication],$one[disease_time],$one[live_status],";
            $str .= '"';
            foreach ($one['lung_data'] as $a) {
                $str .= "$a[createtime] $a[question_name] $a[content]\r\n";
            }
            $str .= '","';
            if ($one['drug_data']) {
                foreach ($one['drug_data'] as $a) {
                    $str .= "$a[first_start_date] $a[name]\r\n";
                }
            } else {
                $str .= "";
            }
            $str .= '"';
            echo $str, "\n";
        }
    }
}

$obj = new ExportPatient();
$obj->run();

