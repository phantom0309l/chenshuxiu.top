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

// doctordiseaseref 初始化
class doctordiseaseref_init extends DbFixBase
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $doctors = Dao::getEntityListByCond("Doctor", ' and id < 10000 ');

        foreach ($doctors as $a) {

            echo " " . $a->id;

            $diseaseid = 1;
            if ($a->id == 32) {
                $diseaseid = 2;
            } elseif ($a->id == 33) {
                $diseaseid = 3;
            }

            $ref = DoctorDiseaseRefDao::getByDoctoridDiseaseid($a->id, $diseaseid);
            if ($ref instanceof DoctorDiseaseRef) {
                echo '-';
                continue;
            }

            $wxshop = WxShopDao::getByDiseaseid($diseaseid);

            if ($diseaseid > 0) {
                $row = array();
                $row['id'] = $a->id;
                $row["doctorid"] = $a->id;
                $row["diseaseid"] = $diseaseid;
//                 $row["visit_daycnt"] = $a->visit_daycnt;
//                 $row["qr_ticket"] = $a->qr_ticket;

                $ref = DoctorDiseaseRef::createByBiz($row);
                $ref->check_qr_ticket();
                echo "+";
            } else {
                echo "=";
            }
        }

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][doctordiseaseref_init.php]=====");

$process = new doctordiseaseref_init();
$process->dowork();

Debug::trace("=====[cron][end][doctordiseaseref_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
