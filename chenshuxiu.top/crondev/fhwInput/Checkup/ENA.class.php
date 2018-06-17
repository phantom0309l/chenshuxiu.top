<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class ENA extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院'], '');
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('ena', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        $sheets['XQuestionSheet']['106315981']['106315982'] = $this->selectAndOther(106315982, trim($a['（双扩散法）抗Sm抗体（Sm）']));
        $sheets['XQuestionSheet']['106315981']['106315987'] = $this->selectAndOther(106315987, trim($a['（双扩散法）抗RNP抗体（RNP）']));
        $sheets['XQuestionSheet']['106315981']['106315992'] = $this->selectAndOther(106315992, trim($a['( 双扩散法 ) 抗SSA抗体（SSA）']));
        $sheets['XQuestionSheet']['106315981']['106315997'] = $this->selectAndOther(106315997, trim($a['( 双扩散法 ) 抗SSB抗体（SSB）']));
        $sheets['XQuestionSheet']['106315981']['106316002'] = $this->selectAndOther(106316002, trim($a['(印记法)Sm （Sm_）']));
        $sheets['XQuestionSheet']['106315981']['106316007'] = $this->selectAndOther(106316007, trim($a['(印记法)抗RNP抗体（RNP_）']));
        $sheets['XQuestionSheet']['106315981']['106316012'] = $this->selectAndOther(106316012, trim($a['(印记法)抗SSA （SAA_）']));
        $sheets['XQuestionSheet']['106315981']['106316017'] = $this->selectAndOther(106316017, trim($a['(印记法)抗SSB （SSB_）']));
        $sheets['XQuestionSheet']['106315981']['106316022'] = $this->selectAndOther(106316022, trim($a['(印记法)抗Scl_70抗体 （ Scl_70_）']));
        $sheets['XQuestionSheet']['106315981']['106316027'] = $this->selectAndOther(106316027, trim($a['(印记法)抗Jo-l抗体（ Jo-l )']));
        $sheets['XQuestionSheet']['106315981']['106316032'] = $this->selectAndOther(106316032, trim($a['(印记法)抗rRNP抗体 ( rRNP )']));
        $sheets['XQuestionSheet']['106315981']['106316037']['content'] = '';

        $this->sheets = $sheets;
    }
}
