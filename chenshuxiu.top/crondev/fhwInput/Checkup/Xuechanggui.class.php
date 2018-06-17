<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Xuechanggui extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号']);
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院']);
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('xuechanggui', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        //白细胞
        $sheets['XQuestionSheet']['106315975']['106315976']['content'] = $this->getFixStr($a['白细胞计数（WBC）']);
        $sheets['XQuestionSheet']['106315975']['106315976']['qualitative'] = '';

        //白细胞
        $sheets['XQuestionSheet']['106315975']['106315977']['content'] = $this->getFixStr($a['红细胞计数（RBC）']);
        $sheets['XQuestionSheet']['106315975']['106315977']['qualitative'] = '';

        //血红蛋白
        $sheets['XQuestionSheet']['106315975']['106315978']['content'] = $this->getFixStr($a['血红蛋白（HGB）']);
        $sheets['XQuestionSheet']['106315975']['106315978']['qualitative'] = '';

        //血小板
        $sheets['XQuestionSheet']['106315975']['106315979']['content'] = $this->getFixStr($a['血小板（PLT）']);
        $sheets['XQuestionSheet']['106315975']['106315979']['qualitative'] = '';

        //备注
        $sheets['XQuestionSheet']['106315975']['106315980']['content'] = '';

        $this->sheets = $sheets;
    }
}
