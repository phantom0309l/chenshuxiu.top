<?php

/*
 * Plan_txtMsg
 */

class Plan_txtMsgService
{
    //
    public static function createILD ($wxuserid, $patientid) {
        // 先中断原来所有未发送定时
        $cond = " and patientid = :patientid and code = 'auto_send_paper' and pushmsgid = 0 ";
        $plan_txtmsgs = Dao::getEntityListByCond('Plan_txtMsg', $cond, [':patientid' => $patientid]);
        if (count($plan_txtmsgs) > 0) {
            foreach ($plan_txtmsgs as $plan_txtmsg) {
                $plan_txtmsg->remove();
            }
        }

        $userid = 0;
        if ($wxuserid) {
            $wxuser = WxUser::getById($wxuserid);

            $userid = $wxuser->userid;
        }

        //
        /*
         发送量表【肺动脉高压患者随访表】， 发送对象：【中日医院肺血管病】医生下的患者 患者入组后第0/1/3/6/12个月填写一次；
         */
        // 创建0月，即立刻发送（报到3分钟后发送）
        $row = [];
        $row["wxuserid"] = $wxuserid;
        $row["userid"] = $userid;
        $row["patientid"] = $patientid;
        $row["auditorid"] = 1;
        $row["objtype"] = 'Patient';
        $row["objid"] = $patientid;
        $row["type"] = 1;
        $row["code"] = "auto_send_paper";
        $row["url"] = "/paper/wenzhen/?papertplid=651007386";
        $row["plan_send_time"] = date('Y-m-d H:i:s', time());
        $row["content"] = "肺动脉高压患者随访表";
        Plan_txtMsg::createByBiz($row);

        $months = [1, 3, 6, 12];
        foreach ($months as $month) {
            $row = [];
            $row["wxuserid"] = $wxuserid;
            $row["userid"] = $userid;
            $row["patientid"] = $patientid;
            $row["patientid"] = $patientid;
            $row["auditorid"] = 1;
            $row["objtype"] = 'Patient';
            $row["objid"] = $patientid;
            $row["type"] = 1;
            $row["code"] = "auto_send_paper";
            $row["url"] = "/paper/wenzhen/?papertplid=651007386";
            $row["plan_send_time"] = date('Y-m-d', strtotime("+{$month} months", time())) . " 10:00:00";
            $row["content"] = "肺动脉高压患者随访表";

            Plan_txtMsg::createByBiz($row);
        }
    }

}
