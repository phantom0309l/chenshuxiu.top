<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Fazuoshi extends CheckupImp
{
    public function fazuozhengzhuang($fazuostr, $xquestionid)
    {
        $selectedarr = array();
        $strarr = explode(",", $fazuostr);
        foreach ($strarr as $a) {
            $selectedarr[] = XOption::getByXQuestionidAndContent($xquestionid, $a)->id;
        }

        return $selectedarr;
    }

    public function zhiliaofangfa($zhiliaostr, $xquestionid)
    {
        $selectedarr = array();
        $strarr = explode(",", $zhiliaostr);
        foreach ($strarr as $a) {
            $selectedarr[] = XOption::getByXQuestionidAndContent($xquestionid, $a)->id;
        }

        return $selectedarr;
    }

    public function muqianzhenduan($option){
        if($option == '0' || $option == ''){
            return "无";
        }

        return $option;
    }

    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['发作时间'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['记录时间'], '0000-00-00');
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('fazuoshi', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        //发作时间
        $sheets['XQuestionSheet']['106312064']['106344551']['content'] = $a['发作时间'];

        //发作症状
        $sheets['XQuestionSheet']['106312064']['106344747']['options'] = $this->fazuozhengzhuang($this->getFixStr($a['发作症状']), 106344747);

        //治疗方法
        $sheets['XQuestionSheet']['106312064']['106312073']['options'] = $this->zhiliaofangfa($this->getFixStr($a['治疗方法']), 106312073);

        //转归
        $sheets['XQuestionSheet']['106312064']['106312078']['options'][0] = XOption::getByXQuestionidAndContent(106312078, $this->getFixStr($a['转归']))->id;

        //发作诱因
        $sheets['XQuestionSheet']['106312064']['106312083'] = $this->selectAndOther(106312083, $this->getFixStr($a['发作诱因']));

        //目前诊断
        $sheets['XQuestionSheet']['106312064']['106312091'] = $this->selectAndOther(106312091, $this->muqianzhenduan($this->getFixStr($a['目前诊断'])));

        //备注
        $sheets['XQuestionSheet']['106312064']['106312106']['content'] = $this->getFixStr($a['备注']);

        $this->sheets = $sheets;
    }
}
