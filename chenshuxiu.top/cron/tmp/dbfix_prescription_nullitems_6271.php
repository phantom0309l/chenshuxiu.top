<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// #6271
// by sjp
class Dbfix_prescription_nullitems_6271
{

    public function dowork () {

        // 一批旧数据
        $sql = "select a.* 
from prescriptions a
left join prescriptionitems b on b.prescriptionid=a.id
where a.chufang_cfbh = '' and b.id is null
order by a.id ;";

        // 内部测试
        $sql = "select * 
from prescriptions
where chufang_cfbh = '' and userid > 10000 and userid < 20000
order by id ;";

        $prescriptions = Dao::loadEntityList('Prescription', $sql);

        foreach($prescriptions as $i => $a){
            echo "\n";
            $unitofwork = BeanFinder::get("UnitOfWork");
            echo "{$i} {$a->id} {$a->createtime} {$a->patient_name} \n";
            $a->remove();

            foreach($a->getPrescriptionItems() as $b){
                echo "\t[{$b->id} {$b->medicine_title}]";
                $b->remove();
            }
            $unitofwork->commitAndInit();
        }

    }
}

echo "\n==== begin ====\n";
$process = new Dbfix_prescription_nullitems_6271();
$process->dowork();
echo "\n==== end ====\n";
