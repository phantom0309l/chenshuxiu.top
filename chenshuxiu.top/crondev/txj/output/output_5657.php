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

class Output_5657
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select
                a.the_doctorid
                from shoporders a
                inner join shoporderitems b on b.shoporderid = a.id
                where a.is_pay=1 and b.shopproductid in (282796166,282702206,282796036) group by a.the_doctorid";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "[{$id}]\n";
            $doctor = Doctor::getById($id);
            if( $doctor instanceof Doctor ){
                $temp = array();

                //医生名
                $temp[] = $doctor->name;

                //医院名
                $temp[] = $doctor->hospital->name;

                //医院所在省份
                $temp[] = $doctor->hospital->xprovince->name;

                //医院所在城市
                $temp[] = $doctor->hospital->xcity->name;

                //是否加入礼来项目
                $temp[] = $doctor->isHezuo("Lilly") ? "是" : "否";

                //加入礼来项目时间
                $join_sunflower_date = "";
                $doctor_hezuo = Doctor_hezuoDao::getOneByCompanyDoctorid("Lilly", $id, " AND status = 1 ");
                if($doctor_hezuo instanceof Doctor_hezuo){
                    $join_sunflower_date = substr($doctor_hezuo->starttime, 0, 10);
                }

                $temp[] = $join_sunflower_date;

                //医生加入礼来项目前的择思达销量
                $temp[] = $this->getSaleCntLtDoctorJoinDate($id, $join_sunflower_date);

                //医生加入礼来项目后的择思达销量
                $temp[] = $this->getSaleCntGtDoctorJoinDate($id, $join_sunflower_date);

                $baodao_date = "2017-07-08";
                //2017.7.8日前报到的患者的择思达销量
                $temp[] = $this->getSaleCntLtBaodaoDate($id, $baodao_date);

                //2017.7.8日后报到的患者的择思达销量
                $temp[] = $this->getSaleCntGtBaodaoDate($id, $baodao_date);

                $data[] = $temp;
            }
        }
        $headarr = array(
            "医生名",
            "医院名",
            "医院所在省份",
            "医院所在城市",
            "是否加入礼来项目",
            "加入礼来项目时间",
            "医生加入礼来项目前的择思达销量",
            "医生加入礼来项目后的择思达销量",
            "2017.7.8日前报到的患者的择思达销量",
            "2017.7.8日后报到的患者的择思达销量",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_5657.xlsx");
        $unitofwork->commitAndInit();
    }

    private function getSaleCntLtDoctorJoinDate($doctorid, $date){
        if(empty($date)){
            return 0;
        }
        $sql = "select
                    sum(b.cnt)
                from shoporders a
                inner join shoporderitems b on b.shoporderid = a.id
                where a.is_pay=1 and b.shopproductid in (282796166,282702206,282796036) and a.the_doctorid = :doctorid and a.time_pay < :date ";
        $bind = [];
        $bind[":doctorid"] = $doctorid;
        $bind[":date"] = $date;
        $cnt = Dao::queryValue($sql, $bind) + 0;
        return $cnt;
    }

    private function getSaleCntGtDoctorJoinDate($doctorid, $date){
        if(empty($date)){
            return 0;
        }
        $sql = "select
                    sum(b.cnt)
                from shoporders a
                inner join shoporderitems b on b.shoporderid = a.id
                where a.is_pay=1 and b.shopproductid in (282796166,282702206,282796036) and a.the_doctorid = :doctorid and a.time_pay >= :date ";
        $bind = [];
        $bind[":doctorid"] = $doctorid;
        $bind[":date"] = $date;
        $cnt = Dao::queryValue($sql, $bind) + 0;
        return $cnt;
    }

    private function getSaleCntLtBaodaoDate($doctorid, $date){
        $sql = "select
                    sum(b.cnt)
                from shoporders a
                inner join shoporderitems b on b.shoporderid = a.id
                inner join patients c on c.id = a.patientid
                where a.is_pay=1 and b.shopproductid in (282796166,282702206,282796036) and a.the_doctorid = :doctorid and c.createtime < :date ";
        $bind = [];
        $bind[":doctorid"] = $doctorid;
        $bind[":date"] = $date;
        $cnt = Dao::queryValue($sql, $bind) + 0;
        return $cnt;
    }

    private function getSaleCntGtBaodaoDate($doctorid, $date){
        $sql = "select
                    sum(b.cnt)
                from shoporders a
                inner join shoporderitems b on b.shoporderid = a.id
                inner join patients c on c.id = a.patientid
                where a.is_pay=1 and b.shopproductid in (282796166,282702206,282796036) and a.the_doctorid = :doctorid and c.createtime >= :date ";
        $bind = [];
        $bind[":doctorid"] = $doctorid;
        $bind[":date"] = $date;
        $cnt = Dao::queryValue($sql, $bind) + 0;
        return $cnt;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5657.php]=====");

$process = new Output_5657();
$process->dowork();

Debug::trace("=====[cron][end][Output_5657.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
