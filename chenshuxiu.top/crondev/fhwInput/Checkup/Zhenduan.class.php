<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Zhenduan extends CheckupImp
{
    public function getFixOption($selected)
    {
        $options = array();

        $options['临床孤立综合征(CIS)'] = '临床孤立综合征(CIS)';
        $options['复发-缓解型多发性硬化(RRMS)'] = '复发-缓解型多发性硬化(RRMS)';
        $options['继发进展型多发性硬化(SPMS)'] = '继发进展型多发性硬化(SPMS)';
        $options['原发进展型多发性硬化(PPMS)'] = '原发进展型多发性硬化(PPMS)';
        $options['视神经脊髓炎(NMO)'] = '视神经脊髓炎(NMO)';
        $options['视神经脊髓炎疾病谱(NMOSD)'] = '视神经脊髓疾病谱(NMOSD)';
        $options['视神经炎(ON)'] = '视神经炎(ON)';
        $options['急性脊髓炎'] = '急性脊髓炎';
        $options['脑干脑炎'] = '脑干脑炎';
        $options['复发性脊髓炎'] = '复发性视神经炎(RON)';
        $options['ADEM'] = 'ADEM';
        $options['炎性脱髓鞘病'] = '炎性脱髓鞘病';

        return $options["{$selected}"] ? $options["{$selected}"] : $selected;
    }

    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->createtime = $this->check_date;
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('zhenduan', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        $sheets['XQuestionSheet']['106314772']['106314773'] = $this->selectAndOther(106314773, $this->getFixOption($this->getFixStr($a['诊断'])));

        $sheets['XQuestionSheet']['106314772']['106314788']['content'] = $a['备注'];

        $this->sheets = $sheets;
    }
}
