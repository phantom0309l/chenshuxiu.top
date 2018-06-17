<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Kanghekangti extends CheckupImp
{
    public static function getIndexArr()
    {
        $indexs = array();
        $indexs['106312202'] = '抗核抗体（IgG型）';
        $indexs['106312206'] = '抗双链DNA抗体（IgG型）';
        $indexs['106312210'] = '抗细胞浆抗体';
        $indexs['106312214'] = '抗中心粒抗体';
        $indexs['106312218'] = '抗Sm抗体（LIA）';
        $indexs['106312222'] = '抗RNP抗体（LIA）';
        $indexs['106312226'] = '抗SSA抗体（LIA）';
        $indexs['106312230'] = '抗SSB抗体（LIA）';
        $indexs['106312234'] = '抗Sc1-70抗体（LIA）';
        $indexs['106312238'] = '抗Jo-1抗体（LIA）';
        $indexs['106312242'] = '抗核糖体抗体（LIA）';
        $indexs['106312246'] = '增值性核抗原抗体（LIA）';
        $indexs['106312250'] = '抗组蛋白抗体（LIA）';
        $indexs['106312254'] = '抗Ro 52抗体（LIA）';
        $indexs['106312258'] = '抗PM-Scl抗体（LIA）';
        $indexs['106312262'] = '抗核小体抗体（LIA）';
        $indexs['106312266'] = '抗着丝点B抗体（LIA）';
        $indexs['106312270'] = '抗线粒体抗体M2亚型（LIA）';
        $indexs['106312274'] = 'DNP乳胶凝集试验（LIA）';

        return $indexs;
    }

    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院'], '');
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('kanghekangtipu', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();
        $indexs = self::getIndexArr();
        foreach ($indexs as $key => $value) {
            $sheets['XQuestionSheet']['106312201'][$key]['options'][0] = XOption::getByXQuestionidAndContent($key, $this->getFixStr($a[$value]))->id;
        }

        $sheets['XQuestionSheet']['106312201']['106312278']['content'] = '';

        $this->sheets = $sheets;
    }
}
