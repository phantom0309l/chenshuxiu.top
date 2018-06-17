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

class Doctorsettleorder_process
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $doctors = DoctorDao::getListByDiseaseid(1);

        $price = 15;

        $date = date("Y-m", time());

        $themonth = date("Y-m-d", strtotime("last month", strtotime($date)));
        echo "\n\n---------================================================----- " . $themonth;

        // 截取的年月
        $themonthshort = substr($themonth, 0, 7);

        foreach ($doctors as $doctor) {

            $create_month_yisheng = substr($doctor->createtime, 0, 7);

            if (strtotime($create_month_yisheng) > strtotime($themonthshort)) {
                continue;
            }

            // 根据医生id与年月获取活跃患者数
            $activecnt = Rpt_patient_month_settleDao::getAvtivecntByDoctorid($doctor->id, $themonthshort);
            echo "\n\n-------------- " . $doctor->id;

            $row["doctorid"] = $doctor->id;
            $row["themonth"] = $themonth;
            $row["activecnt"] = $activecnt;
            $row["price"] = $price;
            $row["amount"] = $activecnt * $price;

            // 用来去重
            $doctorsettleorder = DoctorSettleOrderDao::getByDoctoridAndDateYm($doctor->id, $themonth);

            if (false == ($doctorsettleorder instanceof DoctorSettleOrder)) {
                $entity = DoctorSettleOrder::createByBiz($row);
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Doctorsettleorder_process.php]=====");

$process = new Doctorsettleorder_process();
$process->dowork();

Debug::trace("=====[cron][end][Doctorsettleorder_process.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
