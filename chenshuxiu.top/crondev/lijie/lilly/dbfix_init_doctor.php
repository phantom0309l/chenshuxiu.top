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
class dbfix_init_doctor
{

    // 新建提交
    public function dowork () {
        $sql = "select id from doctor_hezuos where status =0 and hospital_name_2<>'';";
        $ids = Dao::queryValues($sql);

        $i = 0;
        $j = 0;

        $names = [
            '陈静',
            '李荣',
            '林敏',
            '林鄞',
            '刘芳',
            '刘艳'];

        foreach ($ids as $id) {

            $unitofwork = BeanFinder::get("UnitOfWork");

            $doctor_hezuo = Doctor_hezuo::getById($id);
            $doctor = $this->findOneDoctor($doctor_hezuo);
            echo "\n$id {$doctor_hezuo->name} {$doctor_hezuo->hospital_name} {$doctor_hezuo->hospital_name_2}";
            if ($doctor instanceof Doctor) {
                echo "\n ==> {$doctor->id} {$doctor->name} => {$doctor->hospital->name} {$doctor->hospital->shortname}";
                $doctor_hezuo->doctorid = $doctor->id;
                $doctor_hezuo->status = 1;
            } elseif ($doctor > 0) {
                $j ++;
                $doctor = DoctorDao::getByName($doctor_hezuo->name);
                if (in_array($doctor_hezuo->name, $names)) {
                    echo "\n ---> {$doctor->id} {$doctor->name} => {$doctor->hospital->name} {$doctor->hospital->shortname}";
                } else {
                    $doctor_hezuo->doctorid = $doctor->id;
                    $doctor_hezuo->status = 1;
                    echo "\n ===> {$doctor->id} {$doctor->name} => {$doctor->hospital->name} {$doctor->hospital->shortname}";
                }
            } else {
                echo "\n-----";
                $hospital = $this->getHospital($doctor_hezuo->hospital_name, $doctor_hezuo->hospital_name_2);
            }

            $unitofwork->commitAndInit();
        }

        echo "\ni = {$i}";
        echo "\nj = {$j}";
    }

    public function findOneDoctor (Doctor_hezuo $doctor_hezuo) {
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
        $arr1[] = str_replace('市', '', $doctor_hezuo->hospital_name_2);

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

    private function getHospital ($hospital_name, $hospital_name_2) {
        $arr_change_hospital = array(
            '湖州市第三人民医院' => '湖州市妇幼保健院',
            '中国人民解放军陆军总医院' => '中国人民解放军总医院',
            '首都儿科研究所' => '儿研所',
            '首都医科大学附属北京安定医院' => '北京安定医院',
            '首都医科大学附属北京宣武医院' => '首都医科大学宣武医院',
            '北大六院' => '北医六院',
            '四川大学华西第二医院' => '华西妇产儿童医院',
            '深圳儿童医院' => '深圳市儿童医院',
            '上海儿童医学中心' => '上海交通大学医学院附属上海儿童医学中心',
            '上海交通大学医学院附属新华医院' => '上海新华医院');

        if (isset($arr_change_hospital[$hospital_name])) {
            $hospital_name = $arr_change_hospital[$hospital_name];
        }

        $cond = " and (name like '%{$hospital_name}%' or shortname like '%{$hospital_name}%' or name like '%{$hospital_name_2}%' or shortname like '%{$hospital_name_2}%') ";

        $hospital = Dao::getEntityByCond('Hospital', $cond);

        if (false == $hospital instanceof Hospital) {
            echo "\n[{$hospital_name}, {$hospital_name_2}]";
        }else {

            echo "\n[{$hospital_name}, {$hospital_name_2}] => [{$hospital->name}, {$hospital->shortname}]";
        }

        return $hospital;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][dbfix_init_doctor.php]=====");

$process = new dbfix_init_doctor();
$process->dowork();

Debug::trace("=====[cron][end][dbfix_init_doctor.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
