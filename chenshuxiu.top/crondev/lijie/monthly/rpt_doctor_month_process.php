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

// 根据rpt_patient_months表，将某月医生名下的对应的“各种”患者数量汇总成一条数据，插入rpt_doctor_months表中
/*  背景：方便活跃医生统计展示
    是什么：记录医生每个月的报到患者数，扫码报到患者数，报到患者总数，扫码报到患者总数等等。
    汇总规则：
        1.patient_cnt_all_* 截止到当月底累计患者数
        2.patient_cnt_* 当月患者数
        3.wxuser_cnt_all_* 截止到当月底全部wxuser数
        4.wxuser_cnt_* 当月wxuser数
    */
class Rpt_doctor_month_process
{
    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");
        $thetime = strtotime('2015-03-01');
        $endtime = strtotime("last month", time());

        while ($thetime <= $endtime) {
            $theDateYm = date("Y-m", $thetime);
            echo "\n======================\n";
            echo $theDateYm;
            echo "\n======================\n";
            $rpt = Rpt_doctor_monthDao::getByThedateYm($theDateYm);

            if ($rpt instanceof Rpt_doctor_month) {
                echo "\n repeate";
            } else {
                $doctorids = $this->getDoctorids($theDateYm);
                foreach($doctorids as $doctorid){
                    echo "\n======================\n".$doctorid;
                    $this->doRpt_doctor_month($doctorid, $theDateYm);
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");

            $thetime = strtotime("next month", $thetime);
        }

        $unitofwork->commitAndInit();
    }

    // 得到需要汇总数据的docotrid
    private function getDoctorids ($theDateYm) {
        $sql = " SELECT DISTINCT a.doctorid
          FROM statdb.rpt_patient_months a
          INNER JOIN fcqxdb.doctors b on b.id=a.doctorid
          WHERE left(a.themonth, 7)='{$theDateYm}' and b.hospitalid!=5 and a.patient_status_last>0 ";

          return Dao::queryValues($sql);
    }

    private function doRpt_doctor_month($doctorid,$theDateYm){
        $baodaodate_doctor = $this->getBaodaoDate_doctor($doctorid);

        $rpt_dateArr = $this->getRpt_doctor_monthData($doctorid, $theDateYm);

        $themonth = date("Y-m-d", strtotime($theDateYm));
        $month_offsetcnt = XDateTime::getDateDiffOfMonth($baodaodate_doctor, $themonth)+1;

        if(count($rpt_dateArr) > 0){
            $row = array();
            $row["doctorid"] = $doctorid;
            $row["themonth"] = $themonth;
            $row["month_offsetcnt"] = $month_offsetcnt;
            $row["patient_cnt_baodao"] = $rpt_dateArr["patient_cnt_baodao"];
            $row["patient_cnt_baodao_scan"] = $rpt_dateArr["patient_cnt_baodao_scan"];
            $row["patient_cnt_baodao_drug"] = $rpt_dateArr["patient_cnt_baodao_drug"];
            $row["patient_cnt_baodao_drug_scan"] = $rpt_dateArr["patient_cnt_baodao_drug_scan"];

            $row["patient_cnt_all"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm);
            $row["patient_cnt_all_scan"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and isscan=1 ");
            $row["patient_cnt_all_active"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and patient_pipe_cnt>0 ");
            $row["patient_cnt_all_active_scan"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and isscan=1 and patient_pipe_cnt>0 ");
            $row["patient_cnt_all_drug"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and drug_status_last=1 ");
            $row["patient_cnt_all_drug_scan"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and isscan=1 and drug_status_last=1 ");
            $row["patient_cnt_all_drugitem"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and drugitem_cnt>0 ");
            $row["patient_cnt_all_drugitem_scan"] = $this->getPatientCntAllByDoctorid($doctorid, $theDateYm, " and isscan=1 and drugitem_cnt>0 ");

            $rpt_doctor_month = Rpt_doctor_monthDao::getByDoctoridAndDateYm($doctorid, substr($themonth, 0, 7));
            if (false == ($rpt_doctor_month instanceof Rpt_doctor_month)) {
                $entity = Rpt_doctor_month::createByBiz($row);
                echo "\n==========ok============\n";
            }
        }
    }

    // 得到某位医生的总量数据
    private function getPatientCntAllByDoctorid($doctorid, $theDateYm, $cond=""){
        $sql = "select sum(cnt) from(
         select count(DISTINCT patientid) as cnt
         from rpt_patient_months
         where left(themonth, 7)<='{$theDateYm}'
         and doctorid='{$doctorid}' AND patient_daycnt_lifecycle!=0 ".$cond."
         and left(themonth, 7)=left(baodaodate, 7) and patient_status_last>0
         group by left(themonth, 7)
         ) t";

         $cnt = Dao::queryValue($sql, array(), 'statdb');
         if(is_null($cnt)){
             $cnt = 0;
         }
         return $cnt;
    }

    // 得到某位医生的第一位报到患者的第一次统计时间
    private function getBaodaoDate_doctor($doctorid){
        $rpt_patient_month = Dao::getEntityByCond("Rpt_patient_month", " and doctorid= {$doctorid} and patientid!=0 and patient_status_last>0 order by themonth ", "", "statdb");
        //医生的第一个报到患者的第一次统计时间视为医生的开通时间
        return $rpt_patient_month->themonth;
    }

    // 得到某位医生的增量数据
    private function getRpt_doctor_monthData($doctorid, $theDateYm){
        $sql = " SELECT
            count(DISTINCT patientid) as patient_cnt_baodao,
            ifnull(sum(if(isscan=1, 1, 0)), 0) as patient_cnt_baodao_scan,
            ifnull(sum(if(drug_status_last=1, 1, 0)), 0) as patient_cnt_baodao_drug,
            ifnull(sum(if(isscan=1 && drug_status_last=1, 1, 0)), 0) as patient_cnt_baodao_drug_scan
            FROM rpt_patient_months
        WHERE 1=1 AND left(themonth, 7)='{$theDateYm}' AND patient_daycnt_lifecycle!=0
        AND left(baodaodate, 7)=left(themonth, 7) and patient_status_last>0
        AND doctorid={$doctorid}";

        return Dao::queryRow($sql, array(), 'statdb');
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Rpt_doctor_month_process.php]=====");

$process = new Rpt_doctor_month_process();
$process->dowork();

Debug::trace("=====[cron][end][Rpt_doctor_month_process.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
