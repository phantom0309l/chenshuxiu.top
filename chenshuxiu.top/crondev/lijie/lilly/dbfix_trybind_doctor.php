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
class dbfix_trybind_doctor
{

    // 新建提交
    public function dowork () {
        // $sql = "select id from doctor_hezuos where status =0 and
        // hospital_name_2='' ";
        $sql = "select id from doctor_hezuos where hospital_name_2<>'' ";
        $ids = Dao::queryValues($sql);

        $i = 0;
        $j = 0;
        $k = 0;
        $m = 0;

        foreach ($ids as $id) {

            $unitofwork = BeanFinder::get("UnitOfWork");

            $doctor_hezuo = Doctor_hezuo::getById($id);
            // $doctor_hezuo->doctorid = 0; // 解绑, 20170711 解绑过一次了

            $doctorOrCnt = $this->findOneDoctorOrCnt($doctor_hezuo);
            echo "\n$id {$doctor_hezuo->name} {$doctor_hezuo->hospital_name} {$doctor_hezuo->hospital_name_2}";
            if ($doctorOrCnt instanceof Doctor) {
                $i ++;
                $doctor = $doctorOrCnt;
                // $doctor_hezuo->doctorid = $doctor->id; // 绑定

                echo "\n ++> {$doctor->id} {$doctor->name} => {$doctor_hezuo->hospital_name} {$doctor->hospital->shortname}";
            } elseif ($doctorOrCnt == 1) {
                $j ++;
                $doctor = DoctorDao::getByName($doctor_hezuo->name);

                echo "\n ==> {$doctor->id} {$doctor->name} => {$doctor_hezuo->hospital_name} {$doctor->hospital->shortname}";
            } elseif ($doctorOrCnt > 1) {
                $k ++;
                echo "\n====={$doctorOrCnt}";
            } else {
                $m ++;
                echo "\n-----";
            }

            $unitofwork->commitAndInit();
        }

        echo "\ni = {$i}";
        echo "\nj = {$j}";
        echo "\nk = {$k}";
        echo "\nm = {$m}";
    }

    public function findOneDoctorOrCnt (Doctor_hezuo $doctor_hezuo) {
        $cond = " and name=:name ";
        $bind = [];
        $bind[':name'] = $doctor_hezuo->name;

        $doctors = Dao::getEntityListByCond("Doctor", $cond, $bind);

        foreach ($doctors as $doctor) {
            if ($this->isSameOne($doctor_hezuo, $doctor)) {
                return $doctor;
            }
        }

        return count($doctors);
    }

    public function isSameOne (Doctor_hezuo $doctor_hezuo, Doctor $doctor) {
        $arr1 = [];
        $arr1[] = str_replace('市', '', $doctor_hezuo->hospital_name);
        if ($doctor_hezuo->hospital_name_2) {
            $arr1[] = str_replace('市', '', $doctor_hezuo->hospital_name_2);
        }

        $hospital = $doctor->hospital;

        $arr2 = [];
        $arr2[] = str_replace('市', '', $hospital->name);
        $arr2[] = str_replace('市', '', $hospital->shortname);

        foreach ($arr1 as $str1) {
            if (empty($str1)) {
                continue;
            }

            foreach ($arr2 as $str2) {
                if (empty($str2)) {
                    continue;
                }

                if (false !== mb_strstr($str1, $str2)) {
                    echo " \n $str1 = $str2";
                    return true;
                } elseif (false !== mb_strstr($str2, $str1)) {
                    echo " \n $str1 = $str2";
                    return true;
                }
            }
        }

        return false;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][dbfix_trybind_doctor.php]=====");

$process = new dbfix_trybind_doctor();
$process->dowork();

Debug::trace("=====[cron][end][dbfix_trybind_doctor.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
