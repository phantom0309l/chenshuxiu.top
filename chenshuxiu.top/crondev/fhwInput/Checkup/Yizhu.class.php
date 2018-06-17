<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Yizhu extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->createtime = $this->check_date;
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('bencizhenliaoyizhu', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        $sheets['XQuestionSheet']['106314790']['106314791']['content'] = $a['医嘱'];

        $this->sheets = $sheets;
    }
}
