<?php

/*
 * Dc_patientPlanItem
 */
class Dc_patientPlanItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'dc_patientplanid'    //dc_patientplanid
        ,'patientid'    //patientid
        ,'doctorid'    //doctorid
        ,'plan_date'    //计划日期
        ,'submit_time'    //提交日期
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'dc_patientplanid' ,'patientid' ,'doctorid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["dc_patientplan"] = array ("type" => "Dc_patientPlan", "key" => "dc_patientplanid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
    }

    // $row = array(); 
    // $row["dc_patientplanid"] = $dc_patientplanid;
    // $row["patientid"] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row["plan_date"] = $plan_date;
    // $row["submit_time"] = $submit_time;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Dc_patientPlanItem::createByBiz row cannot empty");

        $default = array();
        $default["dc_patientplanid"] =  0;
        $default["patientid"] =  0;
        $default["doctorid"] =  0;
        $default["plan_date"] = '';
        $default["submit_time"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    // 获取item状态
    public function getStatus () {
        // 0：未填写，1：进行中，2：已完成
        $papertplcnt = $this->getPaperTplCnt();
        $papercnt = $this->getPaperCnt();

        if ($papertplcnt == $papercnt) {
            $status = 2;
        } elseif ($papercnt == 0) {
            $status = 0;
        } else {
            $status = 1;
        }

        return $status;
    }

    // 得到问卷数
    public function getPaperTplCnt () {
        $papertplids = explode(',', $this->dc_patientplan->papertplids);
        return count($papertplids);
    }

    // 得到答卷数
    public function getPaperCnt () {
        $papertplids = explode(',', $this->dc_patientplan->papertplids);

        $done = 0;
        foreach ($papertplids as $papertplid) {
            $papertpl = PaperTpl::getById($papertplid);
            $paper = PaperDao::getByPaperTplObjtypeObjid($papertpl, 'Dc_patientPlanItem', $this->id);
            if ($paper instanceof Paper) {
                $done ++;
            }
        }

        return $done;
    }

    // 得到答卷数
    public function getPaperTplToPaperStr () {
        $papertplids = explode(',', $this->dc_patientplan->papertplids);

        $str = "";
        foreach ($papertplids as $i => $papertplid) {
            $papertpl = PaperTpl::getById($papertplid);
            $paper = PaperDao::getByPaperTplObjtypeObjid($papertpl, 'Dc_patientPlanItem', $this->id);
            if ($paper instanceof Paper) {
                $str .= ++$i . ":<a style='color: green' target='_blank' href='/xanswersheetmgr/modify?xanswersheetid=$paper->xanswersheetid'>{$papertpl->xquestionsheet->title}(已完成)</a><br>";
            } else {
                $str .= ++$i . ":<a style='color: red' target='_blank' href='/xquestionsheetmgr/one?xquestionsheetid=$papertpl->xquestionsheetid'>{$papertpl->xquestionsheet->title}(未完成)</a><br>";
            }
        }

        return $str;
    }
}
