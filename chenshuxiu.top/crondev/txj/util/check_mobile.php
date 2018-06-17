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

class Check_mobile
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select id from patients where mobile !=''";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $arr = [];
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                $i ++;
                echo "\n====[$i][{$id}]===\n";
                $mobileArr = $this->getMobiles($patient);
                foreach ($mobileArr as $mobile) {
                    if( !in_array($mobile, $arr) ){
                        $arr[$mobile] = [];
                    }
                    $arr[$mobile][] = $id;
                }

            }
            if($i>100){
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();

        foreach($arr as $mobile => $a){
            $cnt = count($a);
            if($cnt > 1){
                $patientids = implode(",", $a);
                $txt = "mobile[{$mobile}]patientid[{$patientids}]cnt[{$cnt}]\n";
                file_put_contents(dirname(__FILE__) . '/check_mobile.txt', $txt, FILE_APPEND);
            }
        }
    }

    private function getMobiles($patient){
        $temp = [];
        $m1 = $patient->mobile;
        $temp[] = $m1;
        $other_contacts = json_decode($patient->other_contacts, true);
        foreach ($other_contacts as $a) {
            $mobile = $a['mobile'];
            if( $mobile && !in_array($mobile, $temp) ){
                $temp[] = $mobile;
            }
        }
        return $temp;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Check_mobile.php]=====");

$process = new Check_mobile();
$process->dowork();

Debug::trace("=====[cron][end][Check_mobile.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
