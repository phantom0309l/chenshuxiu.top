<?php

// 创建: 20170920 by txj
//方寸儿童管理服务平台当前基本配置，其他疾病太多不显示了，当个用例。
class PipeTplService
{
    //用于流页面的筛选
    public static function getArrForFilter(){
        $result = array();

        //获取患者消息
        $cond = " AND objtype in ('WxPicMsg','WxTxtMsg','WxVoiceMsg')";
        $pipeTpls = Dao::getEntityListByCond('PipeTpl', $cond);
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "患者消息";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //图片
        $pipeTpls = PipeTplDao::getListByObjtype("WxPicMsg");
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "图片";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //语音
        $pipeTpls = PipeTplDao::getListByObjtype("WxVoiceMsg");
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "语音";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //获取患者评估
        $pipeTpls = PipeTplDao::getListByObjtype("Paper");
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "患者评估";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //患者作业
        $cond = " AND objtype in ('LessonUserRef','XAnswerSheet', 'Study')";
        $pipeTpls = Dao::getEntityListByCond('PipeTpl', $cond);
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "患者作业";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //电话
        $cond = " AND objtype in ('CdrMeeting','Meeting')";
        $pipeTpls = Dao::getEntityListByCond('PipeTpl', $cond);
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "电话";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //获取检查报告
        $pipeTpl = PipeTplDao::getOneByObjtypeAndObjcode("CheckUp", "create");
        if($pipeTpl instanceof PipeTpl){
            $temp = array();
            $temp["name"] = "检查报告";
            $temp["ids"] = $pipeTpl->id;
            $result[] = $temp;
        }

        //获取化疗方案
        $pipeTpl = PipeTplDao::getOneByObjtypeAndObjcode("Chemo", "create");
        if($pipeTpl instanceof PipeTpl){
            $temp = array();
            $temp["name"] = "化疗方案";
            $temp["ids"] = $pipeTpl->id;
            $result[] = $temp;
        }

        //用药核对单
        $pipeTpls = PipeTplDao::getListByObjtype("PatientMedicineSheet");
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "用药核对";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //用药ADHD
        $cond = " AND objtype in ('DrugItem','DrugSheet')";
        $pipeTpls = Dao::getEntityListByCond('PipeTpl', $cond);
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "用药ADHD";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //获取运营消息
        $pipeTpl = PipeTplDao::getOneByObjtypeAndObjcode("PushMsg", "byAuditor");
        if($pipeTpl instanceof PipeTpl){
            $temp = array();
            $temp["name"] = "运营消息";
            $temp["ids"] = $pipeTpl->id;
            $result[] = $temp;
        }

        //获取系统消息
        $pipeTpl = PipeTplDao::getOneByObjtypeAndObjcode("PushMsg", "bySystem");
        if($pipeTpl instanceof PipeTpl){
            $temp = array();
            $temp["name"] = "系统消息";
            $temp["ids"] = $pipeTpl->id;
            $result[] = $temp;
        }

        return $result;
    }

    //用于流页面的筛选
    public static function getSuifangArrForFilter(){
        $result = array();

        //获取患者消息
        $cond = " AND objtype in ('WxPicMsg','WxTxtMsg','WxVoiceMsg')";
        $pipeTpls = Dao::getEntityListByCond('PipeTpl', $cond);
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "患者消息";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //图片
        $pipeTpls = PipeTplDao::getListByObjtype("WxPicMsg");
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "图片";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //语音
        $pipeTpls = PipeTplDao::getListByObjtype("WxVoiceMsg");
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "语音";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //电话
        $cond = " AND objtype in ('CdrMeeting','Meeting')";
        $pipeTpls = Dao::getEntityListByCond('PipeTpl', $cond);
        $ids = array();
        foreach($pipeTpls as $a){
            $ids[] = $a->id;
        }
        if(count($ids)){
            $temp = array();
            $temp["name"] = "电话";
            $temp["ids"] = implode(",", $ids);
            $result[] = $temp;
        }

        //获取运营消息
        $pipeTpl = PipeTplDao::getOneByObjtypeAndObjcode("PushMsg", "byAuditor");
        if($pipeTpl instanceof PipeTpl){
            $temp = array();
            $temp["name"] = "运营消息";
            $temp["ids"] = $pipeTpl->id;
            $result[] = $temp;
        }

        //获取系统消息
        $pipeTpl = PipeTplDao::getOneByObjtypeAndObjcode("PushMsg", "bySystem");
        if($pipeTpl instanceof PipeTpl){
            $temp = array();
            $temp["name"] = "系统消息";
            $temp["ids"] = $pipeTpl->id;
            $result[] = $temp;
        }

        return $result;
    }
}
