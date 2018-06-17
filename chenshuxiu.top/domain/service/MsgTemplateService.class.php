<?php

// 名称: MsgTemplateService
// 备注: 消息模板服务类
// 创建: 20170419 by sjp
class MsgTemplateService
{

    // 对外接口
    // 创建: 20170419 by sjp
    public static function getMsgContentByWxUserEname (WxUser $wxuser, $ename, $arr = array()) {
        $patient = $wxuser->user->patient;

        // 用传进来 doctor_entity
        if ($arr['doctor_entity'] instanceof Doctor) {
            $doctor = $arr['doctor_entity'];

            $pcard = PcardDao::getByPatientidDoctorid($patient->id, $doctor->id);
            if($pcard instanceof Pcard){
                $disease = $pcard->disease;
            }else{
                $disease = $patient->disease;
            }
        }else{
            // 这里有个问题, 如果患者有多个pcard, 不能保证取到正确的pcard, 所以需要传一下 doctor_entity
            list ($disease, $doctor) = $wxuser->getDiseaseAndDoctor();
        }

        // 清理一下
        unset($arr['doctor_entity']);

        // 获取消息模板
        $msgtemplate = self::getMsgTemplateByEnameDiseaseDoctor($ename, $disease, $doctor);
        if (false == $msgtemplate instanceof MsgTemplate) {
            return '';
        }

        $content = trim($msgtemplate->content);

        // 通用替换
        $content = self::transformCommomStr($content, $patient, $doctor, $disease);

        // 额外的业务替换
        foreach ($arr as $k => $v) {
            $content = str_replace($k, $v, $content);
        }

        return $content;
    }

    // 名称: getMsgTemplateByEnameDiseaseDoctor
    // 备注: 消息模板, 不同精确度
    // 创建: by xuzhe
    // 修改: 20170419 by sjp : 从实体上迁过来
    private static function getMsgTemplateByEnameDiseaseDoctor ($ename, $disease, $doctor) {
        $diseaseid = $disease->id;
        $doctorid = 0;
        if ($doctor instanceof Doctor) {
            $doctorid = $doctor->id;
        }

        $msgtemplate = MsgTemplateDao::getByEnameDiseaseidDoctorid($ename, $diseaseid, $doctorid);
        if ($msgtemplate instanceof MsgTemplate) {
            return $msgtemplate;
        }

        $msgtemplate = MsgTemplateDao::getByEnameDiseaseidDoctorid($ename, $diseaseid, 0);
        if ($msgtemplate instanceof MsgTemplate) {
            return $msgtemplate;
        }

        return MsgTemplateDao::getByEnameDiseaseidDoctorid($ename, 0, 0);
    }

    // 统一替换
    private static function transformCommomStr ($content, $patient, $doctor, $disease) {
        $patient_name = '';
        $doctor_name = '';
        $hospital_name = '';
        $disease_name = '';

        if ($patient instanceof Patient) {
            $patient_name = $patient->name;
        }

        if ($doctor instanceof Doctor) {
            $doctor_name = $doctor->name;

            $hospital = $doctor->hospital;
            if ($hospital instanceof Hospital) {
                $hospital_name = $hospital->getFixName($doctor->id);
            }
        }

        $disease_name = $disease->name;

        $content = str_replace('#patient_name#', $patient_name, $content);
        $content = str_replace('#doctor_name#', $doctor_name, $content);
        $content = str_replace('#hospital_name#', $hospital_name, $content);
        $content = str_replace('#disease_name#', $disease_name, $content);

        return $content;
    }
}
