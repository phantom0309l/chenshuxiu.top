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

class Init_lilly_data
{

    private static $arr_title = array();
    public function dowork () {
        require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
        $fileurl = "/home/taoxiaojin/scale/lilly_data.xlsx";
        $objPHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $PHPReader->load($fileurl);

        $currentSheet = $objPHPExcel->getActiveSheet();
        $row_num = $currentSheet->getHighestDataRow();
        //读出的是最大列号
        $highestColumm = $currentSheet->getHighestColumn();
        $column_num = PHPExcel_Cell::columnIndexFromString($highestColumm);

        echo "row_num[{$row_num}]\n\n";
        echo "column_num[{$column_num}]\n\n";

        for ($column = 0; $column < $column_num; $column++) {
            $val = $currentSheet->getCellByColumnAndRow($column, 1)->getValue();
            $val = trim($val);
            self::$arr_title[] = $val;
            //echo "{$val}\n\n";
        }

        for ($row = 2; $row <= $row_num; $row++) {
            echo "{$row}\n\n";
            $temp = array();
            for ($column = 0; $column < $column_num; $column++) {
                $val = $currentSheet->getCellByColumnAndRow($column, $row)->getValue();
                $val = trim($val);
                $title = $this->getTitle($column);
                $temp[$title] = $val;
                //echo "{$title}----{$val}\n\n";
            }
            $temp = $this->getTempFix($temp);
            //提交入库
            $unitofwork = BeanFinder::get("UnitOfWork");
            Doctor_hezuo::createByBiz($temp);
            $unitofwork->commitAndInit();
        }
    }

    private function getTempFix($temp){
        $result = array();

        $result["company"] = "Lilly";
        $result["doctor_code"] = $temp["Physician ID"];

        $name = $temp["姓"] . $temp["名"];
        $doctorid = $this->getDoctoridByName($name);
        $result["doctorid"] = $doctorid;
        $result["name"] = $name;
        $result["sex"] = $this->getSex($temp["性别"]);
        $result["title1"] = $temp["技术职称"];
        $result["title2"] = $temp["行政职称"];
        $result["hospital_name"] = $temp["医院"];
        $result["department"] = $temp["科室"];
        $result["json"] = $this->getJson($temp);

        return $result;
    }

    private function getJson($temp){
        $result = array();
        $result["territory_name"] = $temp["Territory Name"];
        $result["group_key"] = $temp["Group Key"];
        $result["is_target_customer"] = $temp["是否为目标客户"];
        $result["is_clinician"] = $temp["是否临床医生"];
        $result["sol_id"] = $temp["SOL_ID"];
        $result["tier"] = $temp["Tier"];
        $result["veeva_tier"] = $temp["Veeva Tier"];
        $result["frequency"] = $temp["Frequency"];
        $result["type"] = $temp["type"];

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    private function getSex($sexstr){
        if($sexstr=="男"){
            return 1;
        }
        if($sexstr=="女"){
            return 2;
        }
        return 0;
    }

    private function getDoctoridByName($name){
        $doctorid = 0;
        $doctor = DoctorDao::getByName($name);
        if($doctor instanceof Doctor){
            $doctorid = $doctor->id;
        }
        return $doctorid;
    }

    private function getTitle($column){
        return self::$arr_title[$column] ?? null;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_lilly_data.php]=====");

$process = new Init_lilly_data();
$process->dowork();

Debug::trace("=====[cron][end][Init_lilly_data.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
