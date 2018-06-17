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

class Complication
{
    public function getPatientByOut_case_no($out_case_no)
    {
        $cond = " and out_case_no = '{$out_case_no}' and doctorid = 33 ";

        return Dao::getEntityByCond("Patient", $cond);
    }

    public function getFixOption($selected)
    {
        $options = array();

        $options['视神经脊髓炎疾病谱（NMOSD）'] = '视神经脊髓炎谱系疾病';
        $options['复发性脊髓炎'] = '复发性脊髓炎';

        return $options["{$selected}"] ? $options["{$selected}"] : $selected;
    }

    public function arrToStr($arr){
        $str = "";
        if (!$arr) {
            return $str;
        }
        foreach ($arr as $a){
            $str .= " " . $a;
        }

        return $str;
    }

    public function getUniqueArr($arr){
        $tempArr = array();
        foreach ($arr as $k => $v){
            $tempArr[$k] = array_unique($v);
        }

        return $tempArr;
    }

    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $json_string = file_get_contents("/tmp/1012.txt");
        $zhenduans = json_decode($json_string, true);

        $arr = array();
        $time = array();
        $lastZhenduan = array();
        foreach ($zhenduans as $b) {
            $time = array();

            foreach ($b as $a) {
                $time["{$a['录入日期']}"] = $a['诊断'];
            }
            ksort($time);
            $lastZhenduan["{$b[0]["病历号"]}"] = array_pop($time);
        }

        $sql = "select out_case_no
            from patients
            where doctorid = 33";

        $out_case_nos = Dao::queryRows($sql);

        $i = 0;
        foreach ($out_case_nos as $a){
            $patient = $this->getPatientByOut_case_no($a['out_case_no']);

            $pcard = PcardDao::getByPatientidDoctorid($patient->id, 33);

            if($lastZhenduan["{$patient->out_case_no}"]){
                $pcard->complication = $lastZhenduan["{$patient->out_case_no}"];
                $i++;
                echo "[--------------------第{$i}个 {$patient->name} {$pcard->complication}--------------------]\n";
            }
        }

        echo "sum = {$i}";

        $unitofwork->commitAndInit();
    }
}

$complication = new Complication();
$complication->dowork();
