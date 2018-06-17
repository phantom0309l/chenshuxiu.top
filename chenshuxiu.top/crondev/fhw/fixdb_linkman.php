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

class Fixdb_linkman
{
    public function fix_ismaster () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patientids = Dao::queryValues("select patientid from linkmans group by patientid ");
        $count = count($patientids);

        $i = 0;
        $k = 0;
        foreach ($patientids as $patientid) {
            $sql = "select count(*) from linkmans where patientid = {$patientid} and is_master = 1 ";
            $cnt = Dao::queryValue($sql);

            if ($cnt > 1) {
                $i ++;

                // 有多个主联系人，只留一个
                $this->subOneMaster($patientid);
            } elseif($cnt == 0) {
                $i ++;

                // 一个都没有，修一个
                $this->AddOneMaster($patientid);
            }

            $k++;
            if ($k % 100 == 0) {
                $k += 100;
                echo $k . "/" . $count . "\n";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        echo "\nall:" . $i . "\n";

        $unitofwork->commitAndInit();
    }

    private function subOneMaster ($patientid) {
        $patient = Patient::getById($patientid);
        $mobile = $patient->mobile;

        $cond = " and patientid = :patientid and is_master = 1 ";
        $bind = [
            ':patientid' => $patientid
        ];
        $linkmans = Dao::getEntityListByCond('Linkman', $cond, $bind);

        if ($mobile) {
            $is_master_linkman = null;
            foreach ($linkmans as $linkman) {
                if ($linkman->mobile == $mobile) {
                    $is_master_linkman = $linkman;
                    break;
                }
            }

            if ($is_master_linkman instanceof Linkman) {
                foreach ($linkmans as $linkman) {
                    if ($linkman->id != $is_master_linkman->id) {
                        $linkman->is_master = 0;
                    }
                }
            } else {
                unset($linkmans[0]);
                foreach ($linkmans as $linkman) {
                    $linkman->is_master = 0;
                }
            }
        } else {
            unset($linkmans[0]);
            foreach ($linkmans as $linkman) {
                $linkman->is_master = 0;
            }
        }
    }

    private function AddOneMaster ($patientid) {
        $patient = Patient::getById($patientid);
        $mobile = $patient->mobile;

        $cond = " and patientid = :patientid and is_master = 0 ";
        $bind = [
            ':patientid' => $patientid
        ];
        $linkmans = Dao::getEntityListByCond('Linkman', $cond, $bind);

        if ($mobile) {
            $is_master_linkman = null;
            foreach ($linkmans as $linkman) {
                if ($linkman->mobile == $mobile) {
                    $is_master_linkman = $linkman;
                    break;
                }
            }

            if ($is_master_linkman instanceof Linkman) {
                foreach ($linkmans as $linkman) {
                    if ($linkman->id == $is_master_linkman->id) {
                        $linkman->is_master = 1;
                        break;
                    }
                }
            } else {
                $linkmans[0]->is_master = 1;
            }
        } else {
            $linkmans[0]->is_master = 1;
        }
    }
}

$test = new Fixdb_linkman();
$test->fix_ismaster();
