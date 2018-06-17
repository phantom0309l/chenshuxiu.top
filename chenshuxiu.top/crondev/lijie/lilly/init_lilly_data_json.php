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
class Init_lilly_data_json
{

    public function dowork () {
        // 由于同一个医生不同医院多余出来的veevaid
        $ignore_arr = ['CN-300045840HCP','CN-300127212HCP','CN-6023416HCP','CN-3003687HCP','CN-6015678HCP','CN-30031013HCP','CN-300068082HCP','CN-6012572HCP','CN-300076515HCP','CN-300127348HCP','CN-6151214HCP','CN-6116303HCP'];
        $doctor_codes_history = [];
        $lines = file(ROOT_TOP_PATH . "/crondev/lijie/lilly/new_veevaid.csv");
        // print_r($lines);
        foreach ($lines as $i => $line) {
            $line = trim($line);

            $arr = explode(",", $line);
            foreach ($arr as $key => $value) {
                $arr[$key] = trim($value);
            }

            list ($doctor_code, $xing, $ming, $sex, $title1, $title2, $hospital_name, $department, $territory_name, $group_key, $is_target_customer, $is_clinician, $sol_id, $tier, $veeva_tier, $frequency, $type) = $arr;
            $doctor_name = $xing.$ming;
            if (empty($str)) {
                $str = "";
            }

            if ($i < 1) {
                continue;
            }

            if(in_array($doctor_code, $ignore_arr)){
                continue;
            }

            if(in_array($doctor_code, $doctor_codes_history)){
                echo "\nveevaid重复 : {$doctor_code} ";
                continue;
            }

            array_push($doctor_codes_history, $doctor_code);

            $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorCode("Lilly", $doctor_code);

            $marketer_name = $this->getMarketer_name($territory_name);
            if (false == $doctor_hezuo instanceof Doctor_hezuo) {
                $result = array();

                $result["company"] = "Lilly";
                $result["doctor_code"] = $doctor_code;

                $result["name"] = $doctor_name;
                $result["sex"] = "男" == $sex ? 1 : 2;
                $result["title1"] = $title1;
                $result["title2"] = $title2;
                $result["hospital_name"] = $hospital_name;
                $result["department"] = $department;
                $result["marketer_name"] = $marketer_name;

                $json = array();
                $json["territory_name"] = $territory_name;
                $json["group_key"] = $group_key;
                $json["is_target_customer"] = $is_target_customer;
                $json["is_clinician"] = $is_clinician;
                $json["sol_id"] = $sol_id;
                $json["tier"] = $tier;
                $json["veeva_tier"] = $veeva_tier;
                $json["frequency"] = $frequency;
                $json["type"] = $type;

                $result["json"] = json_encode($json, JSON_UNESCAPED_UNICODE);

                // 提交入库
                $doctor_hezuo = Doctor_hezuo::createByBiz($result);
                echo "\n新增合作医生[{$doctor_code}] : {$doctor_name}";
            }

            if ($doctor_hezuo instanceof Doctor_hezuo) {
                if ($doctor_hezuo->name == $doctor_name) {
                    $marketer_name_mysql = $this->getMarketer_nameFromMysql($doctor_hezuo);

                    if($marketer_name_mysql == $marketer_name){
                        $doctor_hezuo->marketer_name = $marketer_name;
                    }else {
                        echo "\n市场人员不一致[{$doctor_code}][$doctor_hezuo->name] : {$marketer_name_mysql}(第一次给出) <>  {$marketer_name}(第二次给出)";
                    }
                }

                if ($doctor_hezuo->name != $doctor_name) {
                    echo "\n医生名字不一致{$doctor_code} : {$doctor_hezuo->name} <>  {$doctor_name}";
                }

                if ($doctor_hezuo->hospital_name != $hospital_name) {
                    echo "\n医院名字不一致{$doctor_code} : {$doctor_hezuo->hospital_name} <>  {$hospital_name}";
                }
            }

        }
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofwork->commitAndInit();

    }

    private function getMarketer_nameFromMysql ($doctor_hezuo) {
        $json = $doctor_hezuo->json;

        if($json == ""){
            return "";
        }

        $json_obj = json_decode($json);
        $territory_name = $json_obj->territory_name;
        return $this->getMarketer_name($territory_name);
    }

    private function getMarketer_name ($territory_name) {
        $marketer_name = $territory_name;
        if(strstr($territory_name, "STR")){
            preg_match_all("/(?:\()(.*)(?:\))/i",$territory_name, $result);
            $marketer_name = $result[1][0];
        }
        return $marketer_name;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_lilly_data_json.php]=====");

$process = new Init_lilly_data_json();
$process->dowork();

Debug::trace("=====[cron][end][Init_lilly_data_json.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
