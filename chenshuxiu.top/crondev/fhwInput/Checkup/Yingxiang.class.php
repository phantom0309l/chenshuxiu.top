<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Yingxiang extends CheckupImp
{
    public function Items($xquestionid, $strarr)
    {
        foreach ($strarr as $a) {
            $selectedarr[] = XOption::getByXQuestionidAndContent($xquestionid, $a)->id;
        }

        return $selectedarr;
    }

    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('yingxiangjiancha', 33);
    }

    // 规则（C:颈髓 T:胸髓 L:腰髓） >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        /*
                        外院。C3-T1病灶。
                        外院。胸髓MRI：T1-10病灶，>3。
        */
        $arr = array();
        if ($this->getFixStr($a['子项目']) == '脊髓') {
            $tempArr = explode("：", $a['备注']);
            if (count($tempArr) > 1) {
                if (strpos($tempArr[0], '颈') > 0) {
                    $arr[] = "颈髓";
                }
                if (strpos($tempArr[0], '胸') > 0) {
                    $arr[] = "胸髓";
                }
                if (strpos($tempArr[0], '腰') > 0) {
                    $arr[] = "腰髓";
                }
            } else {
                if (strpos($a['备注'], 'C') > 0) {
                    $arr[] = "颈髓";
                }
                if (strpos($a['备注'], 'T') > 0) {
                    $arr[] = "胸髓";
                }
                if (strpos($a['备注'], 'L') > 0) {
                    $arr[] = "腰髓";
                }
            }
        } else {
            $arr[] = "头部";
        }

        if (count($arr) <= 0) {
            $arr[] = '其他';
        }

        $sheets['XQuestionSheet']['106312377']['106312378']['options'][0] = XOption::getByXQuestionidAndContent(106312378, $this->getFixStr($a['项目']))->id;

        $sheets['XQuestionSheet']['106312377']['106312381']['options'] = $this->Items(106312381, $arr);
        $sheets['XQuestionSheet']['106312377']['106312381']['content'] = '';

        $sheets['XQuestionSheet']['106312377']['106312382']['content'] = $this->getFixStr($a['备注']);

        $this->sheets = $sheets;
    }
}
