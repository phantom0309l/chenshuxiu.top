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
class Init_marketer_city_area
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $lines = file(ROOT_TOP_PATH . "/crondev/lijie/lilly/marketer_city_area.csv");
        // print_r($lines);
        foreach ($lines as $i => $line) {
            $line = trim($line);

            $arr = explode(",", $line);
            foreach ($arr as $key => $value) {
                $arr[$key] = trim($value);
            }

            list ($doctor_code, $doctor_name, $marketer_name, $city, $area) = $arr;
            if ($i < 1) {
                continue;
            }

            $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorCode("Lilly", $doctor_code);
            if (false == $doctor_hezuo instanceof Doctor_hezuo) {
                echo "\n需要新增合作医生[doctor_code] : {$doctor_code}";
            }

            if ($doctor_hezuo instanceof Doctor_hezuo) {
                if ($doctor_name != $doctor_hezuo->name) {
                    echo "\n医生名字不一致[{$doctor_code}] : {$doctor_hezuo->name}(库中) <>  {$doctor_name}(表中)";
                }

                if ($marketer_name != $doctor_hezuo->marketer_name) {
                    echo "\n市场名字不一致{$doctor_code}[{$doctor_hezuo->name}] : {$doctor_hezuo->marketer_name}(库中) <>  {$marketer_name}(表中)";
                }

                if ($doctor_name == $doctor_hezuo->name && $marketer_name == $doctor_hezuo->marketer_name) {
                    $doctor_hezuo->city_name_bymarketer = $city;
                    $doctor_hezuo->area_bymarketer = $area;
                }
            }

        }
        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_marketer_city_area.php]=====");

$process = new Init_marketer_city_area();
$process->dowork();

Debug::trace("=====[cron][end][Init_marketer_city_area.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
