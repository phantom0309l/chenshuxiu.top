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

class Doctorserviceorders_init
{

    public function dowork () {
        $now = date("Y-m-d H:i:s", time());
        $sql = "select a.id
                    from doctors a
                    inner join doctordiseaserefs b on b.doctorid = a.id
                    where b.diseaseid=1";
        $ids = Dao::queryValues($sql);
        $i = 0;
        $dateArr = array(
            //array("2017-09-01", "2017-09-03"),
            //array("2017-09-04", "2017-09-10"),
            //array("2017-09-11", "2017-09-17"),
            //array("2017-09-18", "2017-09-24"),
            //array("2017-09-25", "2017-10-01"),
            //array("2017-10-02", "2017-10-08"),
            //array("2017-10-09", "2017-10-15"),
            //array("2017-10-16", "2017-10-22"),
            //array("2017-10-23", "2017-10-29"),
            //array("2017-10-30", "2017-11-01"),
            //array("2017-11-02", "2017-11-05"),
            //array("2017-11-06", "2017-11-12"),
            //array("2017-11-13", "2017-11-19"),
            //array("2017-11-20", "2017-11-26"),
            //array("2017-11-27", "2017-12-01"),
            //array("2017-12-02", "2017-12-03"),
            //array("2017-12-04", "2017-12-10"),
            //array("2017-12-11", "2017-12-17"),
            //array("2017-12-18", "2017-12-24"),
            //array("2017-12-25", "2018-01-02"),
            //array("2018-01-03", "2018-01-07"),
            //array("2018-01-08", "2018-01-14"),
            //array("2018-01-15", "2018-01-21"),
            //array("2018-01-22", "2018-01-28"),
            //array("2018-01-29", "2018-02-02"),
            //array("2018-02-03", "2018-02-04"),
            //array("2018-02-05", "2018-02-11"),
            //array("2018-02-12", "2018-02-18"),
            //array("2018-02-19", "2018-02-25"),
            //array("2018-02-26", "2018-03-02"),
            //array("2018-03-03", "2018-03-04"),
            //array("2018-03-05", "2018-03-11"),
            //array("2018-03-12", "2018-03-18"),
            //array("2018-03-19", "2018-03-25"),
            //array("2018-03-26", "2018-04-01"),
            //array("2018-04-02", "2018-04-08"),
            //array("2018-04-09", "2018-04-15"),
            //array("2018-04-16", "2018-04-22"),
            //array("2018-04-23", "2018-04-29"),
            //array("2018-04-30", "2018-05-01"),
            array("2018-05-02", "2018-05-06"),
            array("2018-05-07", "2018-05-13"),
            array("2018-05-14", "2018-05-20"),
            array("2018-05-21", "2018-05-27"),
            array("2018-05-28", "2018-06-01"),
        );
        foreach($dateArr as $date){
            foreach ($ids as $id) {
                $doctor = Doctor::getById($id);
                if( $doctor instanceof Doctor ){
                    $unitofwork = BeanFinder::get("UnitOfWork");
                    echo "\n====doctorid[{$id}]===\n";
                    DoctorServiceOrderService::createDoctorServiceOrders($doctor, $date[0], $date[1]);
                    $unitofwork->commitAndInit();
                }
            }
        }
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Doctorserviceorders_init.php]=====");

$process = new Doctorserviceorders_init();
$process->dowork();

Debug::trace("=====[cron][end][Doctorserviceorders_init.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
