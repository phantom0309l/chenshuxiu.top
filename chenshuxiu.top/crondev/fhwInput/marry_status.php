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

TheSystem::init(__FILE__);

class Patient_xuyan
{
    public function getFixStr($str, $default = '')
    {
        if (empty($str)) {
            return $default;
        }

        return trim(str_replace(array(
           "\n",
           "\t"), "", $str));
    }

    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}' and doctorid = 33 ";

        return Dao::getEntityByCond("Patient", $cond);
    }

    public function dowork()
    {

        // 从文件中导入徐雁患者
        $json_string = file_get_contents("/tmp/1000.txt");
        $patientinfos = json_decode($json_string, true);

        $excludePatients = array("李傅凤","石秋生","幕秀花");

        $i = 0;
        foreach ($patientinfos as $arr) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            if(true == in_array($this->getFixStr($arr['姓名']), $excludePatients)){
                continue;
            }

            $out_case_no = $this->getFixStr($arr['病历号']);

            $patient = $this->getPatientByOut_case_no($out_case_no);
            if($patient instanceof Patient){
                if(!$patient->marry_status){
                    $i++;
                    $patient->marry_status = $arr['婚姻'];

                    echo "[---------------------第{$i}个患者  婚姻：{$patient->marry_status}----------------------]\n";
                }
            }

            $unitofwork->commitAndInit();
        }

        echo "sum" . $i;
    }
}

$patient_xuyan = new Patient_xuyan();
$patient_xuyan->dowork();
