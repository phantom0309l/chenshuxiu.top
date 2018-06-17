<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/15
 * Time: 10:24
 */

ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Doctor_Week_Rpt_Main extends CronBase
{

    private static $stCates = [
        'patient',
        'msg',
        'paper',
        'checkup',
        'bedtkt',
        'revisit',
        'Patient_Active'
    ];

    private static $stCateIns = [];

    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'week';
        $row["title"] = '每周一, 01:00 统计医生周报数据';
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

    protected function getDoctorIds() {
        $doctorIds = [];
        // 除开小儿多动症和肿瘤的不跑
        $sql = "SELECT id FROM doctors 
                WHERE id NOT IN (
                    SELECT doctorid
                    FROM doctordiseaserefs
                    WHERE diseaseid in (1, 8, 14, 15, 19, 21)
                )";
        //test cancer doctor
        //$sql = "SELECT DISTINCT(a.id) FROM doctors a INNER JOIN doctordiseaserefs b ON a.id=b.doctorid WHERE b.doctorid=33 LIMIT 5";
        $doctorIds = Dao::queryValues($sql);
        return $doctorIds;
    }

    protected function getDiseaseIdsByDoctorId($doctorId) {
        if (empty($doctorId)) {
            return [];
        }
        $sql = 'SELECT diseaseid from doctordiseaserefs WHERE doctorid=:doctorid AND diseaseid > 1';
        $rows = Dao::queryValues($sql, [':doctorid' => $doctorId]);
        return $rows;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        $stData = [];
        $doctorIds = $this->getDoctorIds();
        $ret = [];
        $i = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($doctorIds as $doctorId) {
            echo ++$i, " doctorid:$doctorId\n";
            $diseaseIds = $this->getDiseaseIdsByDoctorId($doctorId);
            foreach ($diseaseIds as $diseaseId) {
                $ret = [];
                foreach (self::$stCates as $stCate) {
                    $ins = $this->getStCateIns($stCate);
                    $ret = array_merge($ret, $ins->stData($doctorId, $diseaseId));
                }
                if (empty($ins) || empty($ret)) {
                    echo "ins is empty or stdata empty \n";
                }
                //一个医生的一个分类结束, 写入数据库
                $row = [];
                $row['doctorid'] = $doctorId;
                $row['diseaseid'] = $diseaseId;
                $row['weekend_date'] = $ins->getLastWeekendDate();
                $row['data'] = json_encode($ret, JSON_UNESCAPED_UNICODE);
                Rpt_week_doctor_data::createByBiz($row);
            }
            if ($i % 50 == 0) {
                $unitofwork->commitAndInit();
                echo "commit unitofwork \n";
            }
        }
        $unitofwork->commitAndRelease();
    }

    private function mergeData($data, $stData) {
        if (empty($data)) {
            return $stData;
        }
        $doctorIds = array_keys($stData);
        foreach ($doctorIds as $doctorId) {
            if (!$data[$doctorId]) {
                $data[$doctorId] = [];
            }
            $diseaseIds = array_keys($stData[$doctorId]);
            foreach ($diseaseIds as $diseaseId) {
                if (!isset($data[$doctorId][$diseaseId])) {
                    $data[$doctorId][$diseaseId] = [];
                }
                $data[$doctorId][$diseaseId] = array_merge($data[$doctorId][$diseaseId], $stData[$doctorId][$diseaseId]);
            }
        }

        return $data;
    }


    private function getStCateIns($stCate) {
        $className = $this->getClassName($stCate);
        if (!isset(self::$stCateIns[$stCate])) {
            self::$stCateIns[$stCate] = new $className();
        }
        return self::$stCateIns[$stCate];
    }


    private function getClassName($stCate) {
        return "Doctor_Week_Rpt_" . ucfirst($stCate);
    }
}


// //////////////////////////////////////////////////////
//curl -i 'http://fangcun001:9090/doctor/statement4doctor?token=1|d6afe0190cb85ef442b2bc470302494d&doctorId=477&diseaseId=15&date=2017-08-06'

$process = new Doctor_Week_Rpt_Main(__FILE__);
$process->dowork();
