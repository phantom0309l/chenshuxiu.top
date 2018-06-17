<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//Debug::$debug = 'Dev';
class Data_doctor_name
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.name as 'doctor_name',b.name as 'hospital_name',a.department,a.title,a.mobile
                from doctors a
                inner join hospitals b on b.id = a.hospitalid
                where a.old_doctorid > 0 ";
        $rows = Dao::queryRows($sql);

        $data["爱可泰隆医生列表"] = [
            'heads' => [
                '医生姓名',
                '医院姓名',
                '科室',
                '职称',
                '电话'
            ],
            'data' => $rows
        ];

        $fileurl = "/tmp/fhw/data/actelion_doctors.xls";

        ExcelUtil::createExcelImp($data, $fileurl);

        $unitofwork->commitAndInit();
    }


}

$test = new Data_doctor_name();
$test->work();
