<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 汇总rpt_patients表中的数据到rpt_patient_months中
/*  背景：方便展示月度患者留存及服药留存（留存：留存基数是报到当月患者数，三月的人数可能大于二月的人数）
    是什么：患者每个月的扫码状态，报到时间，用药状态以及各种“动作”的总数量等等。
    汇总规则：
        1.patient_status_first，drug_status_first：当月患者最早一天的patient_status，drug_status
        2.isscan，baodaodate，patient_daycnt_lifecycle，patient_status_last，drug_status_last：
        当月患者最后一天的数据得出
        3.patient_pipe_cnt，drugitem_cnt：当月各种“动作”的总数量及用药填写总数量
    */
class Rpt_patient_month_process
{

    public function dowork()
    {

        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;
        $thetime = strtotime('2018-01-01');
        // 往前翻一个月
        //$endtime = strtotime("last month", time());
        $endtime = strtotime('2018-01-01');

        while ($thetime <= $endtime) {
            $theDateYm = date("Y-m", $thetime);
            echo "\n======================\n";
            echo $theDateYm;
            echo "\n======================\n";
            $rpt = Rpt_patient_monthDao::getByThedateYm($theDateYm);

            if ($rpt instanceof Rpt_patient_month) {
                echo "\n repeate";
            } else {
                $patientids = $this->getPatientids($theDateYm);

                foreach ($patientids as $patientid) {
                    echo "\n======================" . $patientid;
                    $this->doRpt_patient_month($patientid, $theDateYm);

                    $i++;
                    if($i == 100){
                        $unitofwork->commitAndInit();
                        $unitofwork = BeanFinder::get("UnitOfWork");
                        $i = 0;
                    }
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");

            $thetime = strtotime("next month", $thetime);
        }
        $unitofwork->commitAndInit();
    }

    // 拿到某个月的基础数据中需要汇总的所有patientid
    private function getPatientids($theDateYm)
    {
        $fromdate = date("Y-m-d", strtotime($theDateYm));
        $todate = date("Y-m-d", strtotime("next month", strtotime($theDateYm)));
        $sql = " SELECT DISTINCT patientid FROM rpt_patients
          WHERE thedate >= '$fromdate' and thedate < '$todate' and patientid > 0";

        return Dao::queryValues($sql, array(), 'statdb');
    }

    // 从rpt_patients表中汇总出当月患者的状态，将数据插入到rpt_patient_months中
    private function doRpt_patient_month($patientid, $theDateYm)
    {
        $themonth = date("Y-m-d", strtotime($theDateYm));
        $rpt_patient_month = Rpt_patient_monthDao::getByPatientidAndDateYmd($patientid, $themonth);
        if ($rpt_patient_month instanceof Rpt_patient_month) {
            return;
        }

        $rpt_patient_First = Rpt_patientDao::getFirstByPatientidThedateYm($patientid, $theDateYm);
        $rpt_patient_Last = Rpt_patientDao::getLastByPatientidThedateYm($patientid, $theDateYm);
        $rpt_patientSumArr = $this->getRpt_patientSumByDataYm($patientid, $theDateYm);

        $baodaodate = $rpt_patient_Last->baodaodate;
        $month_offsetcnt = XDateTime::getDateDiffOfMonth($baodaodate, $themonth) + 1;

        $row = array();
        $row["patientid"] = $patientid;
        $row["doctorid"] = $rpt_patient_Last->doctorid;
        $row["isscan"] = $rpt_patient_Last->isscan;
        $row["baodaodate"] = $rpt_patient_Last->baodaodate;
        $row["patient_daycnt_lifecycle"] = $rpt_patient_Last->patient_daycnt_lifecycle;
        $row["themonth"] = $rpt_patient_Last->thedate;
        $row["month_offsetcnt"] = $month_offsetcnt;
        $row["patient_status_first"] = $rpt_patient_First->patient_status;
        $row["patient_status_last"] = $rpt_patient_Last->patient_status;
        $row["patient_pipe_cnt"] = $rpt_patientSumArr["patient_pipe_cnt"];
        $row["drugitem_cnt"] = $rpt_patientSumArr["drugitem_cnt"];
        // 用药状态,0:无填写记录，1：用药，2：不服药，3：停药
        $row["drug_status_first"] = $rpt_patient_First->drug_status;
        $row["drug_status_last"] = $rpt_patient_Last->drug_status;

        Rpt_patient_month::createByBiz($row);
    }

    // 拿到该患者传入日期所在月的汇总数据
    // patient_pipe_cnt>0字段标志活跃
    private function getRpt_patientSumByDataYm($patientid, $theDateYm)
    {
        $sql = " SELECT
            sum(drugitem_cnt+paper_cnt+wxpicmsg_cnt+wxtxtmsg_cnt
            +wxvoicemsg_cnt+patientnote_cnt+lessonuserref_hwk_cnt
            +lessonuserref_test_cnt+comment_share_cnt) as patient_pipe_cnt,
            sum(drugitem_cnt) as drugitem_cnt
            FROM rpt_patients
            WHERE patientid={$patientid} AND left(thedate, 7)='{$theDateYm}'
            GROUP BY left(thedate, 7)";

        return Dao::queryRow($sql, array(), 'statdb');
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Rpt_patient_month_process.php]=====");

$process = new Rpt_patient_month_process();
$process->dowork();

Debug::trace("=====[cron][end][Rpt_patient_month_process.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
