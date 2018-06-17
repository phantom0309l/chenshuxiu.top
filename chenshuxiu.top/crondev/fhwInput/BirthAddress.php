<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

include_once("Base.class.php");

TheSystem::init(__FILE__);

class BirthAddress
{

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " select id
            from patients
            where doctorid = 33 ";

        $patientids = Dao::queryValues($sql);

        foreach ($patientids as $patientid){
            $patient = Patient::getById($patientid);

            if((strpos($patient->native_place,"内蒙古") >= 0) || (strpos($patient->native_place,"新疆") >= 0)){
                if(strpos($patient->native_place,"内蒙古") >= 0){
                    $province = "内蒙古";
                }
                if(strpos($patient->native_place,"新疆") >= 0){
                    $province = "新疆";
                }

                //2.内蒙古xxx 或  新疆xxx
                $provinces = explode("{$province}", $patient->native_place);
                if(count($provinces) > 1){
                    $patient->provincestr = "{$province}";
                    $patient->citystr = $provinces[1];

                    echo "[籍贯：{$patient->native_place}  出生地：{$patient->provincestr} {$patient->citystr}]\n";
                }

            }elseif(strpos($patient->native_place,"省") >= 0){
                //1.xxx省xxx市（区）
                $provinces = explode("省", $patient->native_place);
                if(count($provinces) > 1){
                    $patient->provincestr = $provinces[0];
                    $patient->citystr = $provinces[1];

                    echo "[籍贯：{$patient->native_place}  出生地：{$patient->provincestr} {$patient->citystr}]\n";
                }
            }else{
                //3.省级市 北京市海淀区 或 重庆市xxx县
                $provinces = explode("市", $patient->native_place);
                if(count($provinces) > 1){
                    $patient->provincestr = $provinces[0];
                    $patient->citystr = $provinces[1];

                    echo "[籍贯：{$patient->native_place}  出生地：{$patient->provincestr} {$patient->citystr}]\n";
                }
            }
        }

//         print_r($patientids);

        $unitofwork->commitAndInit();
    }

}

$birthAddress = new BirthAddress();
$birthAddress->dowork();
