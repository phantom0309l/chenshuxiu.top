<?php

class Rpt_Patient_MonthMgrAction extends AuditBaseAction
{

    // 患者月统计列表
    /* 患者数据留存数据展示规则（rule）：
        1.扫码患者（isscan=1）
        2.患者报到审核通过（patient_status_last>0）
        3.患者有活跃（patient_pipe_cnt>0）
    */
    public function doList () {
        $items = $this->getDataByCondFix(" and (patient_pipe_cnt>0 or left(baodaodate,7)=left(themonth,7))");
        $drug_items = $this->getDataByCondFix(" AND drugitem_cnt>0 ");

        XContext::setValue("items", $items);
        XContext::setValue("drug_items", $drug_items);
        return self::SUCCESS;
    }

    private function getDataByCondFix ($cond = "") {
        $sql = " SELECT
        left(baodaodate, 7) as baodaodate,
        sum(if(month_offsetcnt=1, 1, 0)) as column_1,
        sum(if(month_offsetcnt=2, 1, 0)) as column_2,
        sum(if(month_offsetcnt=3, 1, 0)) as column_3,
        sum(if(month_offsetcnt=4, 1, 0)) as column_4,
        sum(if(month_offsetcnt=5, 1, 0)) as column_5,
        sum(if(month_offsetcnt=6, 1, 0)) as column_6,
        sum(if(month_offsetcnt=7, 1, 0)) as column_7,
        sum(if(month_offsetcnt=8, 1, 0)) as column_8,
        sum(if(month_offsetcnt=9, 1, 0)) as column_9,
        sum(if(month_offsetcnt=10, 1, 0)) as column_10,
        sum(if(month_offsetcnt=11, 1, 0)) as column_11,
        sum(if(month_offsetcnt=12, 1, 0)) as column_12,
        sum(if(month_offsetcnt=13, 1, 0)) as column_13
        FROM rpt_patient_months
        WHERE isscan=1 and patient_status_last>0 {$cond}
        GROUP BY left(baodaodate, 7)
        ORDER BY left(baodaodate, 7) ";

        return Dao::queryRows($sql, [], 'statdb');
    }
}
