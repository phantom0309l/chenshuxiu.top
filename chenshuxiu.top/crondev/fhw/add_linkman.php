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

class Add_linkman
{
    public function dowork()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        //$sql = "select id,name,doctorid,diseaseid,mobile,other_contacts from patients where other_contacts <> '' ";
        //$rows = Dao::queryRows($sql);
        $sql = "select id,name,doctorid,diseaseid,mobile,other_contacts 
        from patients 
        where id in (562177666,562177826,562177916,562180116,562187026,562201866,562223136) ";
        $rows = Dao::queryRows($sql);

        $i = 0;
        $k = 0;
        $cnt = count($rows);
        $needfixs = [];
        foreach ($rows as $row) {
            $patientid = $row['id'];
            $doctorid = $row['doctorid'];
            $doctorname = Doctor::getById($doctorid)->name;
            $diseaseid = $row['diseaseid'];
            $diseasename = Disease::getById($diseaseid)->name;
            $patientname = $row['name'];
            $master_mobile = $row['mobile'];

            $contacts = json_decode($row['other_contacts'], true);

            $other_contacts_have_mastermobile = 0;
            foreach ($contacts as $contact) {
                $userid = $contact['userid'];
                $name = $contact['name'];
                $shipstr = $contact['shipstr'];
                $mobile = $contact['mobile'];

                if (empty($mobile)) {
                    continue;
                }

                if ($mobile == $master_mobile) {
                    $other_contacts_have_mastermobile = 1;
                }

                if (false == $this->check_mobile($mobile)) {
                    // 号码格式不正确，需要运营手动修复
                    $needfixs[] = [
                        $patientname,
                        $doctorname,
                        $diseasename,
                        $userid,
                        $patientid,
                        $name,
                        $shipstr,
                        $mobile
                    ];
                    continue;
                }

                // 创建linkman
                $row = [];
                $row["userid"] = $userid ? $userid : $this->getUserId($patientid);
                $row["patientid"] = $patientid;
                $row["name"] = $name ?? '';
                $row["shipstr"] = $shipstr ?? '';
                $row["mobile"] = $mobile;
                $row["is_master"] = $mobile == $master_mobile ? 1 : 0;
                Linkman::createByBiz($row);

                $i++;
                if ($i % 200 == 0) {
                    $k += 200;
                    echo $k . "/" . $cnt . "\n";
                    $unitofwork->commitAndInit();
                } else {
                    if ($i % 2 == 0) {
                        echo ".";
                    }
                }
            }

            // 创建master linkman
            if ($other_contacts_have_mastermobile == 0 && $master_mobile) {
                $row = [];
                $row["userid"] = LinkmanService::getOneUseridByPatientidMobile($patientid, $master_mobile);
                $row["patientid"] = $patientid;
                $row["name"] = $patientname;
                $row["shipstr"] = '';
                $row["mobile"] = $master_mobile;
                $row["is_master"] = 1;
                Linkman::createByBiz($row);
            }
        }

        $this->output_needfix_mobile($needfixs);

        echo $cnt . "/" . $cnt . "\n";

        $unitofwork->commitAndInit();
    }

    // 去除重复的linkman
    public function fix_repeat () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select patientid,mobile,count(*) as cnt
                from linkmans
                group by patientid,mobile
                having cnt > 1 ";
        $rows = Dao::queryRows($sql);

        foreach ($rows as $row) {
            $patientid = $row['patientid'];
            $mobile = $row['mobile'];

            $cond = " and patientid = :patientid and mobile = :mobile ";
            $bind = [
                ':patientid' => $patientid,
                ':mobile' => $mobile
            ];

            $linkmans = Dao::getEntityListByCond('Linkman', $cond, $bind);

            $have_userids = [];
            $not_userids = [];
            foreach ($linkmans as $linkman) {
                if ($linkman->userid) {
                    $have_userids[] = $linkman;
                } else {
                    $not_userids[] = $linkman;
                }
            }

            if (count($have_userids) == 1) {
                foreach ($not_userids as $a) {
                    $a->remove();
                }
            } elseif (count($have_userids) == 0) {
                unset($not_userids[0]);
                foreach ($not_userids as $a) {
                    $a->remove();
                }
            } else {
                unset($have_userids[0]);
                foreach ($have_userids as $a) {
                    $a->remove();
                }
            }
        }

        $unitofwork->commitAndInit();
    }

    // 修复linkman上的userid
    public function fix_userid () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id,patientid,mobile from linkmans where userid = 0 ";
        $rows = Dao::queryRows($sql);

        $from_mobile_cnt = 0;
        $from_patient_cnt = 0;

        $k = 0;
        $cnt = count($rows);
        foreach ($rows as $i => $row) {
            $linkmanid = $row['id'];
            $patientid = $row['patientid'];
            $mobile = $row['mobile'];

            // 通过patientid和mobile找userid
            $userid = $this->getMobileUserid($patientid, $mobile);
            if ($userid) {
                $from_mobile_cnt++;
            } else {
                // 直接通过patientid找一个有效userid
                $userid = $this->getUserId($patientid);
                $from_patient_cnt++;
            }

            if ($userid) {
                $sql = "update linkmans set userid = {$userid} where id = {$linkmanid} ";
                Dao::executeNoQuery($sql);
            }

            if ($i % 100 == 0) {
                $k += 100;
                echo $k . "/" . $cnt . "\n";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        echo $cnt . "/" . $cnt . "\n";

        echo "from_mobile_cnt:{$from_mobile_cnt} \n";
        echo "from_patient_cnt:{$from_patient_cnt} \n";

        $unitofwork->commitAndInit();
    }

    private function getUserId ($patientid) {
        $sql = "select id from users where id = (
                    select createuserid
                    from patients
                    where id = {$patientid}
                ) ";
        $userid = Dao::queryValue($sql);

        if ($userid) {
            return $userid;
        } else {
            $sql = "select id from users where patientid = {$patientid} order by id asc limit 1";
            $userid = Dao::queryValue($sql);
            if ($userid) {
                return $userid;
            } else {
                return 0;
            }
        }
    }

    private function getMobileUserid ($patientid, $mobile) {
        $sql = "select id from users where patientid = {$patientid} and mobile = {$mobile} limit 1";
        return Dao::queryValue($sql);
    }

    private function check_mobile ($mobile) {
        if(strlen($mobile) == 11) {
            if (preg_match_all("/^1[0-9]{10}$/",$mobile,$array)) {
                return true;
            } else {
                return false;
            }
        }elseif (strlen($mobile) == 12 || strlen($mobile) == 13 ) {
            if (preg_match_all("/^0[0-9]{2,3}-[0-9]{8}$/",$mobile,$array) || preg_match_all("/^0[0-9]{2,3}[0-9]{8}$/",$mobile,$array)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function output_needfix_mobile ($list) {
        $data = [
            '联系人号码修复' => [
                'heads' => [
                    '患者姓名',
                    '医生',
                    '疾病',
                    'userid',
                    'patientid',
                    'name',
                    'shipstr',
                    'mobile'
                ],
                'data' => $list
            ]
        ];

        $fileurl = "data/needfix_all.xls";
        ExcelUtil::createExcelImp($data, $fileurl);
    }
}

$test = new Add_linkman();
$test->dowork();
$test->fix_repeat();
$test->fix_userid();
