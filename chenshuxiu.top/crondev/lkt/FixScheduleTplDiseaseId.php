<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/5/22
 * Time: 13:31
 */
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

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";

$sql = "
        SELECT *
        FROM doctors
        ";
$doctors = Dao::loadEntityList("Doctor", $sql);

$scheduleTpl_count = 0;
$schedule_count = 0;

$unitofwork = BeanFinder::get("UnitOfWork");
foreach ($doctors as $doctor) {
    echo "\n";
    echo $doctor->name;
    echo " - ";
    $scheduleTpls = $doctor->getScheduleTpls();

    if (count($scheduleTpls) == 0) { // 多疾病，暂不处理
        echo "jump";
        continue;
    }

    $diseases = $doctor->getDiseases();

    // 乔大夫
    if ($doctor->id == 10) {
        $diseases = [];
        $diseases[] = Disease::getById(1);
    } elseif ($doctor->id == 11 || $doctor->id == 32 || $doctor->id == 619) {
        // 史老师 , 王迁 ,许医生测试
        $diseases = [];
        $diseases[] = Disease::getById(2);
    } elseif ($doctor->id == 15) {
        // 王医生
        $diseases = [];
        $diseases[] = Disease::getById(3);
    } elseif ($doctor->id == 477) {
        // 王颖轶
        $diseases = [];
        $diseases[] = Disease::getById(8);
    }

    if (count($diseases) != 1) { // 多疾病，暂不处理
        echo "多疾病，暂不处理";
        continue;
    }
    $diseaseid = $diseases[0]->id;
    echo "疾病id：{$diseaseid}";
    echo " - ";

    foreach ($scheduleTpls as $scheduleTpl) {
        $scheduleTpl->diseaseid = $diseaseid;
        $scheduleTpl_count ++;
        // echo $scheduleTpl->toMoreStr();
        // echo "\n";

        $schedules = ScheduleDao::getListByScheduleTpl($scheduleTpl);
        foreach ($schedules as $schedule) {
            $schedule->diseaseid = $diseaseid;
            $schedule_count ++;
        }
    }
}
$unitofwork->commitAndInit();
echo "\n\n";
echo "ScheduleTpl  {$scheduleTpl_count}";
echo "\n";
echo "Schedule     {$schedule_count}";
echo "\n\n";