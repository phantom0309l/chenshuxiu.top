<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Gangongneng extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号']);
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院']);
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('gangongneng', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        //丙氨酸氨基转移酶
        $sheets['XQuestionSheet']['106312180']['106312181']['content'] = $a['丙氨酸氨基转移酶（ALT）'];
        $sheets['XQuestionSheet']['106312180']['106312181']['qualitative'] = '';

        //天冬氨酸氨基转移酶
        $sheets['XQuestionSheet']['106312180']['106312182']['content'] = $a['天冬氨酸氨基转移酶（AST）'];
        $sheets['XQuestionSheet']['106312180']['106312182']['qualitative'] = '';

        //γ-谷氨酰转肽酶
        $sheets['XQuestionSheet']['106312180']['106312183']['content'] = $a['γ-谷氨酰转肽酶（GGT）'];
        $sheets['XQuestionSheet']['106312180']['106312183']['qualitative'] = '';

        //检查说明
        $sheets['XQuestionSheet']['106312180']['106312184']['content'] = '';

        $this->sheets = $sheets;
    }
}
