<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");
class EDSS extends CheckupImp
{
    public function getTitle($i)
    {
        $options = array();

        $options[0]['title'] = "视觉";
        $options[0]['xquestionid'] = "106312109";
        $options[1]['title'] = "脑干";
        $options[1]['xquestionid'] = "106312117";
        $options[2]['title'] = "锥体束";
        $options[2]['xquestionid'] = "106312124";
        $options[3]['title'] = "小脑";
        $options[3]['xquestionid'] = "106312132";
        $options[4]['title'] = "感觉";
        $options[4]['xquestionid'] = "106312139";
        $options[5]['title'] = "大小便";
        $options[5]['xquestionid'] = "106312147";
        $options[6]['title'] = "大脑";
        $options[6]['xquestionid'] = "106312155";

        return $options[$i];
    }

    public function Xingdong($selected)
    {
        $options = array();

        $options['0.0'] = '0.0';
        $options['3.0'] = '3.0 无辅助或休息下行走 >500米';
        $options['4.0'] = '4.0 无辅助或休息下行走 =500米';
        $options['4.5'] = '4.5 无辅助或休息下行走 300米≤X＜500米';
        $options['5.0'] = '5.0 无辅助或休息下行走 200米≤X＜300米';
        $options['5.5'] = '5.5 无辅助或休息下行走 100米≤X＜200米';
        $options['6.0'] = '6.0 无辅助行走 50米≤X＜100米';
        $options['6.5'] = '6.5 双侧辅助行走 20米≤X＜50米';
        $options['7.0'] = '7.0 辅助行走 5米≤X＜20米';
        $options['7.5'] = '7.5 辅助行走 X＜5米 and 轮椅';
        $options['8.0'] = '8.0 行动基本限于床、椅或轮椅，但每天大部分时间在床下活动；保留很多自理能力；双臂基本有功能';
        $options['8.5'] = '8.5 每天活动基本限于床上；双臂有部分功能；保留部分自理能力';
        $options['9.0'] = '9.0 患者卧床，可交流和进食';
        $options['9.5'] = '9.5 患者卧床，基本不能有效交流或进食、吞咽';
        $options['10'] = '10 死于MS';

        return $options["{$selected}"];
    }

    public function getOption($xquestionid,$content){
        $cond = "AND xquestionid=:xquestionid AND content like :content";
        return Dao::getEntityByCond("XOption", $cond, array(
            ':xquestionid' => $xquestionid,
            ':content' => "%{$content}%"));
    }

    public function init(array $a)
    {
        $this->out_case_no = $this->getFixStr($a['病历号'], '');
        $this->check_date = $this->getFixStr($a['评估时间'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院']);
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('edss', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        $sheets['XQuestionSheet']['106312108']['106345573']['content'] = $this->getFixStr($a['评估时间'], '0000-00-00');

        for ($i = 0; $i < 7; $i++) {
            $_arr = $this->getTitle($i);
            $xoption = XOption::getByXQuestionidAndContent($_arr['xquestionid'], $this->getFixStr($a[$_arr['title']]));
            $sheets['XQuestionSheet']['106312108'][$_arr['xquestionid']]['options'][0] = $xoption->id;

        }

        //行动
        $sheets['XQuestionSheet']['106312108']['106312162']['options'][0] = $this->getOption(106312162, $this->getFixStr($this->Xingdong($a['行动'])))->id;

        $sheets['XQuestionSheet']['106312108']['106312177']['content'] = $a['行走距离'];
        $sheets['XQuestionSheet']['106312108']['106312178']['content'] = $a['得分'];

        $this->sheets = $sheets;
    }
}
