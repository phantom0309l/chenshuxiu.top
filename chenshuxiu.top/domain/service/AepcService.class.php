<?php

// 创建: 20170626 by txj
class AepcService
{
    //通过xquestionid获取答案
    public static function getContent($xanswersheetid, $xquestionid){
        $a = self::getXanswer($xanswersheetid, $xquestionid);
        return $a->content;
    }

    //判断某个单选题是否被选中
    public static function isChecked($xanswersheetid, $xquestionid, $xoptionid){
        $a = self::getXanswer($xanswersheetid, $xquestionid);
        $thexoption = $a->getTheXOption();
        return $xoptionid == $thexoption->id;
    }

    //通过xquestionid获取答案
    public static function getSex($xanswersheetid, $xquestionid){
        $a = self::getXanswer($xanswersheetid, $xquestionid);
        $xoption = $a->getTheXOption();
        return $xoption->content ?? '';
    }

    //获取怀疑产品
    public static function getHuaiYiProducts($xanswersheetid){
        $xquestionid = 275200646;
        $a = self::getXanswer($xanswersheetid, $xquestionid);
        $content = urldecode($a->content);
        return json_decode($content, true);
    }

    //获取合并用药
    public static function getHeBingMedicines($xanswersheetid){
        $xquestionid = 275201196;
        $a = self::getXanswer($xanswersheetid, $xquestionid);
        $content = urldecode($a->content);
        return json_decode($content, true);
    }

    //获取事件编号
    public static function getEventNo($xanswersheetid){
        $event_no = $xanswersheetid;
        $content = AepcService::getContent($xanswersheetid, 275148346);
        if($content){
            $event_no = $content;
        }
        return $event_no;
    }

    //生成导出PDF的title
    public static function genPDFTitle(Paper $paper){
        $xanswersheetid = $paper->xanswersheetid;
        $event_no = AepcService::getEventNo($xanswersheetid);
        $patientid = $paper->patientid;
        $ename = $paper->ename;

        return "sunflower{$ename}事件，事件ID{$event_no}患者ID{$patientid}";
    }

    private static function getXanswer($xanswersheetid, $xquestionid){
        $xanswersheet = XAnswerSheet::getById($xanswersheetid);
        return XAnswer::getByXQuestionIdOfXAnswerSheet($xanswersheet, $xquestionid);
    }
}
