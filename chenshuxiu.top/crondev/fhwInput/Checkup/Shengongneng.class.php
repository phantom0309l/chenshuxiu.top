<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Shengongneng extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号']);
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院']);
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('shengongneng', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        //丙氨酸氨基转移酶
        $sheets['XQuestionSheet']['106312186']['106312187']['content'] = $this->getFixStr($a['尿素（Urea）']);
        $sheets['XQuestionSheet']['106312186']['106312187']['qualitative'] = '';

        //天冬氨酸氨基转移酶
        $sheets['XQuestionSheet']['106312186']['106312188']['content'] = $this->getFixStr($a['肌酐（CR）']);
        $sheets['XQuestionSheet']['106312186']['106312188']['qualitative'] = '';

        //检查说明
        $sheets['XQuestionSheet']['106312186']['106312189']['content'] = '';

        $this->sheets = $sheets;
    }
}
