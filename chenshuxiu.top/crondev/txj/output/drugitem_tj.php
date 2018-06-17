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

// Debug::$debug = 'Dev';

class DrugItem_tj
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select count(*) as cnt,patientid from drugitems where datediff(now(), createtime) <=30 group by patientid having cnt > 1";
        $arr = Dao::queryRows($sql);
        $i = 0;

        $x = 0;//新增用药
        $y = 0;//停药
        $z = 0;//持续服药
        $x1 = 0;
        $x2 = 0;
        $x3 = 0;
        $x4 = 0;
        $x5 = 0;

        $y1 = 0;
        $y2 = 0;
        $y3 = 0;
        $y4 = 0;
        $y5 = 0;

        $z1 = 0;
        $z2 = 0;
        $z3 = 0;
        $z4 = 0;
        $z5 = 0;

        foreach ($arr as $a) {
            $patientid = $a["patientid"];
            $patient = Patient::getById($patientid);
            $createtime = $patient->createtime;
            $diff = XDateTime::getDateDiff(date("Y-m-d",time()), $createtime);
            $cond = "AND patientid = {$patientid} ORDER BY id DESC limit 2";
            $drugitems = Dao::getEntityListByCond("DrugItem", $cond);
            $d0 = $drugitems[1];
            $d1 = $drugitems[0];
            $flag0 = true;
            $flag1 = true;
            if( $d0->value == 0 ){
                $flag0 = false;
            }

            if( $d1->value == 0 ){
                $flag1 = false;
            }

            if($flag0==false && $flag1==true){
                $x++;
                if( $diff <=30 ){
                    $x1++;
                }
                if( $diff<=60 && $diff > 30 ){
                    $x2++;
                }
                if( $diff <=90 && $diff > 60 ){
                    $x3++;
                }
                if( $diff <=120 && $diff > 90 ){
                    $x4++;
                }
                if( 120<$diff ){
                    $x5++;
                }

            }

            if($flag0==true && $flag1==false){
                $y++;
                if( $diff <=30 ){
                    $y1++;
                }
                if( $diff <=60 && $diff > 30 ){
                    $y2++;
                }
                if( $diff <=90 && $diff > 60){
                    $y3++;
                }
                if( $diff <=120 && $diff > 90){
                    $y4++;
                }
                if( 120<$diff ){
                    $y5++;
                }

            }

            if($flag0==true && $flag1==true){
                $z++;
                if( $diff <=30 ){
                    $z1++;
                }
                if( $diff <=60 && $diff > 30 ){
                    $z2++;
                }
                if( $diff <=90 && $diff > 60){
                    $z3++;
                }
                if( $diff <=120&& $diff > 90){
                    $z4++;
                }
                if( 120<$diff ){
                    $z5++;
                }

            }

        }

        echo "\n====x[{$x}]\n";
        echo "====x1[{$x1}]\n";
        echo "====x2[{$x2}]\n";
        echo "====x3[{$x3}]\n";
        echo "====x4[{$x4}]\n";
        echo "====x5[{$x5}]\n";

        echo "\n====y[{$y}]\n";
        echo "====y1[{$y1}]\n";
        echo "====y2[{$y2}]\n";
        echo "====y3[{$y3}]\n";
        echo "====y4[{$y4}]\n";
        echo "====y5[{$y5}]\n";

        echo "\n====z[{$z}]\n";
        echo "====z1[{$z1}]\n";
        echo "====z2[{$z2}]\n";
        echo "====z3[{$z3}]\n";
        echo "====z4[{$z4}]\n";
        echo "====z5[{$z5}]\n";

        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][DrugItem_tj.php]=====");

$process = new DrugItem_tj();
$process->dowork();

Debug::trace("=====[cron][end][DrugItem_tj.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
