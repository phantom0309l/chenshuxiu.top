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

class Fixdb_checkup_merge
{

    public function dowork () {
        echo "[fixdb_checkup_merge begin]\n";
        $checkups = Dao::getEntityListByCond('Checkup', ' and checkuptplid > 0 and status = 0 order by updatetime desc ');

        foreach ($checkups as $a) {
            $unitofwork = BeanFinder::get("UnitOfWork");
            $checkupmerges = Dao::getEntityListByCond('Checkup',
                    " and status = 0 and patientid = {$a->patientid} and checkuptplid = {$a->checkuptplid} and check_date = '{$a->check_date}' order by id ");

            if (count($checkupmerges) > 1) {
                echo "\n-----------------------------begin----------------------------\n";
                echo "---------------------same---------------------\n";
                foreach ($checkupmerges as $x) {
                    echo 'id=' . $x->id . "\n";
                }
                echo "---------------------same---------------------\n";
            }

            if (count($checkupmerges) > 1) {
                $arr['xanswersheet_pictrue'] = array();
                $arr['xanswersheet'] = array();
                $arr['pictrue'] = array();
                $arr['not_xanswersheet_pictrue'] = array();

                foreach ($checkupmerges as $b) {
                    if ($b->xanswersheetid > 0 && count(CheckupPictureDao::getListByCheckupid($b->id, $b->patientid)) > 0) {
                        $arr['xanswersheet_pictrue'][] = $b;
                    }

                    if ($b->xanswersheetid > 0 && count(CheckupPictureDao::getListByCheckupid($b->id, $b->patientid)) == 0) {
                        $arr['xanswersheet'][] = $b;
                    }

                    if ($b->xanswersheetid == 0 && count(CheckupPictureDao::getListByCheckupid($b->id, $b->patientid)) > 0) {
                        $arr['pictrue'][] = $b;
                    }

                    if ($b->xanswersheetid == 0 && count(CheckupPictureDao::getListByCheckupid($b->id, $b->patientid)) == 0) {
                        $arr['not_xanswersheet_pictrue'][] = $b;
                    }
                }

                if (false == empty($arr['xanswersheet_pictrue'])) {
                    echo "\n-------------------xanswersheet_pictrue------------------\n";
                    $this->deleteAll($arr['xanswersheet'], 'xanswersheet');
                    $this->deleteAll($arr['pictrue'], 'pictrue');
                } else {
                    echo "\n--------------xanswersheet or pictrue or not-------------\n";

                    $this->remainMaxUpdatetime($arr['xanswersheet'], 'xanswersheet');
                    $this->remainMaxUpdatetime($arr['pictrue'], 'pictrue');

                    if (count($arr['xanswersheet']) > 0 && count($arr['pictrue']) > 0) {
                        $this->mergeCheckup($arr['xanswersheet'][0], $arr['pictrue'][0]);
                    }
                }

                $this->deleteAll($arr['not_xanswersheet_pictrue'], 'not_xanswersheet_pictrue');

                unset($arr['xanswersheet_pictrue']);
                unset($arr['xanswersheet']);
                unset($arr['pictrue']);
                unset($arr['not_xanswersheet_pictrue']);
                echo "-----------------------------end------------------------------\n";
            }

            $unitofwork->commitAndInit();
        }

        echo "[fixdb_checkup_merge end]";
    }

    public function deleteAll (Array $arr, $typestr) {
        if (false == empty($arr)) {
            foreach ($arr as $a) {
                echo "checkup {$typestr} delete id={$a->id} \n";
                $a->status = 1;
                // $a->remove();
            }
        }
    }

    public function remainMaxUpdatetime (Array $arr, $typestr) {
        if (false == empty($arr)) {
            for ($i = 1; $i < count($arr); $i ++) {
                echo "checkup updatetime_old {$typestr} delete id={$arr[$i]->id} \n";
                $arr[$i]->status = 1;
                // $arr[$i]->remove();
            }
        }
    }

    public function mergeCheckup ($checkup_xanswersheet, $checkup_pictrue) {
        if ($checkup_xanswersheet != null && $checkup_pictrue != null) {
            echo "checkup pictrue modify xansersheetid id={$checkup_pictrue->id} \n";
            $checkup_pictrue->xanswersheetid = $checkup_xanswersheet->xanswersheetid;

            echo "checkup merge xanswersheet delete id={$checkup_xanswersheet->id} \n";
            $checkup_xanswersheet->status = 1;
            // $checkup_xanswersheet->remove();
        }
    }
}

$fixdb_checkup_merge = new Fixdb_checkup_merge();
$fixdb_checkup_merge->dowork();