<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Shuitongdao extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院'], '');
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('shuitongdaodanbai4kangti', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        $sheets['XQuestionSheet']['106312280']['106312281']['options'][0] = XOption::getByXQuestionidAndContent(106312281, $this->getFixStr($a['AQP4IgG']))->id;
        $sheets['XQuestionSheet']['106312280']['106312281']['content'] = '';

        $sheets['XQuestionSheet']['106312280']['106312286']['options'][0] = XOption::getByXQuestionidAndContent(106312286, $this->getFixStr($a['NMO-IgG']))->id;
        $sheets['XQuestionSheet']['106312280']['106312286']['content'] = '';

        $sheets['XQuestionSheet']['106312280']['106312291']['content'] = '';

        $this->sheets = $sheets;
    }
}
