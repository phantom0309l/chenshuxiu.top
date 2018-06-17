<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Zhiliao extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->createtime = $this->check_date;
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('yongyao', 33);
    }

    public function yongyao($option){
        if($option == ''){
            return "无";
        }

        return $option;
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

//         $sheets['XQuestionSheet']['107372400']['107372401'] = $this->selectAndOther(107372401, $this->yongyao($a['药物']));

//         $sheets['XQuestionSheet']['107372400']['107372413']['content'] = $this->getFixStr($a['开始用药'],'0000-00-00');

//         $sheets['XQuestionSheet']['107372400']['107372414']['content'] = $this->getFixStr($a['停药时间'],'0000-00-00');

//         $sheets['XQuestionSheet']['107372400']['107372415'] = $this->selectAndOther(107372415, $this->getFixStr($this->yongyao($a['停药原因'])));

//         $sheets['XQuestionSheet']['107372400']['107372421']['content'] = $this->getFixStr($a['备注']);

        $sheets['XQuestionSheet']['107482794']['107482795'] = $this->selectAndOther(107482795, $this->yongyao($a['药物']));

        $sheets['XQuestionSheet']['107482794']['107482808']['content'] = $this->getFixStr($a['开始用药'],'0000-00-00');

        $sheets['XQuestionSheet']['107482794']['107482809']['content'] = $this->getFixStr($a['停药时间'],'0000-00-00');

        $sheets['XQuestionSheet']['107482794']['107482810'] = $this->selectAndOther(107482810, $this->getFixStr($this->yongyao($a['停药原因'])));

        $sheets['XQuestionSheet']['107482794']['107482817']['content'] = $this->getFixStr($a['备注']);

        $this->sheets = $sheets;
    }
}
