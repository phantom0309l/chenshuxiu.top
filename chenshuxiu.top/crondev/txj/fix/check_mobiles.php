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

class check_mobiles
{

    private $patientidArr = array();

    //A = patients表字段other_contacts
    //B = users表mobile字段集合
    public function dowork () {
        $sql = "select id from patients where other_contacts!=''";
        $ids = Dao::queryValues($sql);
        $all_cnt = count($ids);
        $i = 0;
        $data = array();

        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($ids as $index => $id) {
            $i ++;
            if ($i > 100) {
                $i = 0;
                $pre = round($index/$all_cnt, 2);
                echo "[{$pre}]\n";
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);
            if($patient instanceof Patient){
                $compare_status = $this->getCompareStatus($patient);

                if($compare_status == 1){
                    //$this->handleWhenAeqB($patient);
                    continue;
                }

                if($compare_status == 2){
                    //$this->handleWhenAgtB_base($patient);
                    continue;
                }

                if($compare_status == 3){
                    //$this->handleWhenAltB($patient);
                }
            }
        }
        $content = implode(",", $this->patientidArr);
        file_put_contents("b.txt", $content);

        $arr_cnt = count($this->patientidArr);
        echo "\narr_cnt[{$arr_cnt}]\n";

        $unitofwork->commitAndInit();
    }

    //A>B
    private function handleWhenAgtB($patient){
        $users = $patient->getUsers();
        foreach($users as $user){
            $mobile = $user->mobile;
            if(!empty($mobile)){
                if (false === strpos($patient->other_contacts, $mobile)) {
                    $this->patientidArr[] = $patient->id;
                    return;
                }
            }
        }
    }

    //A>B 且A中userid > 0;
    private function handleWhenAgtB2($patient){
        $users = $patient->getUsers();
        if(count($users) == 0){
            return;
        }

        $other_contactsArr = JsonLinkman::jsonArray($this);

        foreach($other_contactsArr as $a){
            $userid = $a["userid"];
            if($userid == 0){
                return;
            }
        }
        $this->patientidArr[] = $patient->id;
    }

    //A>B base
    private function handleWhenAgtB_base($patient){
        $users = $patient->getUsers();
        if(count($users) == 0){
            return;
        }

        $this->patientidArr[] = $patient->id;
    }

    //A<B
    private function handleWhenAltB($patient){
        $arr = [];
        $users = $patient->getUsers();
        foreach($users as $user){
            $mobile = $user->mobile;
            if(!empty($mobile)){
                if(!in_array($mobile, $arr)){
                    $arr[] = $mobile;
                }
            }
        }
        if( count($arr) > 1 ){
            $this->patientidArr[] = $patient->id;
        }
    }

    //A==B
    private function handleWhenAeqB($patient){
        $users = $patient->getUsers();
        foreach($users as $user){
            $mobile = $user->mobile;
            if(!empty($mobile)){
                if (false === strpos($patient->other_contacts, $mobile)) {
                    $this->patientidArr[] = $patient->id;
                    return;
                }
            }
        }
    }

    //user上存储的电话与other_contacts上存储的电话数量是否一致

    //获取比较情况 1:A==B  2:A > B  3:A < B
    private function getCompareStatus($patient){
        $users = $patient->getUsers();
        $user_mobile_cnt = 0;
        foreach($users as $user){
            $mobile = $user->mobile;
            if(!empty($mobile)){
                $user_mobile_cnt++;
            }
        }

        $other_contactsArr = JsonLinkman::jsonArray($this);
        $other_contactsArr_cnt = count($other_contactsArr);

        if($other_contactsArr_cnt == $user_mobile_cnt){
            return 1;
        }elseif($other_contactsArr_cnt > $user_mobile_cnt){
            return 2;
        }elseif($other_contactsArr_cnt < $user_mobile_cnt){
            return 3;
        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][check_mobiles.php]=====");

$process = new check_mobiles();
$process->dowork();

Debug::trace("=====[cron][end][check_mobiles.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
