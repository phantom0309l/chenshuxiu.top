<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/15
 * Time: 10:47
 */

abstract class Doctor_Week_Rpt_Base
{
    protected $last_weekend = "";
    protected $this_monday = "";
    protected $last_monday = "";
    protected $last_last_monday = "";
    protected $doctorId = 0;
    protected $diseaseId = 0;

    public function __construct() {
        $this->last_weekend = date('Y-m-d', strtotime('-1 sunday', time()));
        $this->this_monday = date('Y-m-d', strtotime('-1 monday', time()));
        $this->last_monday = date('Y-m-d', strtotime('-2 monday', time()));
        $this->last_last_monday = date('Y-m-d', strtotime('-3 monday', time()));

        //$this->last_weekend = date('Y-m-d', strtotime('-4 sunday', time()));
        //$this->this_monday = date('Y-m-d', strtotime('-4 monday', time()));
        //$this->last_monday = date('Y-m-d', strtotime('-5 monday', time()));
        //$this->last_last_monday = date('Y-m-d', strtotime('-6 monday', time()));
    }

    public function setDoctorAndDiseaseId($doctorId, $diseaseId) {
        $this->doctorId = $doctorId;
        $this->diseaseId = $diseaseId;
    }

    public function getLastWeekendDate() {
        return $this->last_weekend;
    }

    //格式化数据，保持和旧接口数据兼容
    protected function formatCnt($cnt) {
        if ($cnt == 0) {
            return ['全部' => 0];
        }
        return ['cnt' => $cnt];
    }

    abstract public function stData($doctorId, $diseaseId);
}
