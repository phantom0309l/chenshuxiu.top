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

class del_mobiles
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
                $users = $patient->getUsers();
                $user_cnt = count($users);

                if($user_cnt == 0){
                    continue;
                }

                $other_contactsArr = JsonLinkman::jsonArray($this);
                foreach($other_contactsArr as $other_contact){
                    $userid = $other_contact["userid"];
                    if(empty($userid)){
                        continue;
                    }
                    $the_user = User::getById($userid);
                    if($the_user instanceof User){
                        $the_patientid = $the_user->patientid;
                        if($the_patientid != $id){
                            $content = "{$id}_{$userid}";
                            $this->patientidArr[] = $content;
                        }
                    }
                }
            }
        }
        $content = implode(",", $this->patientidArr);
        file_put_contents("c.txt", $content);

        $arr_cnt = count($this->patientidArr);
        echo "\narr_cnt[{$arr_cnt}]\n";

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][del_mobiles.php]=====");

$process = new del_mobiles();
$process->dowork();

Debug::trace("=====[cron][end][del_mobiles.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
