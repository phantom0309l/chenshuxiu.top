<?php

class CdrMeetingService
{
    // 判断 cdr_call_type 是否是呼入
    public static function isCallIn ($cdr_call_type) {
        $callInType = [1,2];
        if (in_array($cdr_call_type, $callInType)) {
            return true;
        }else {
            return false;
        }
    }

    // 根据时间段 获取通话cnt
    public static function getCntByAuditorAndTimeSort ($auditor_id, $startTime, $endTime) {
        $less2 = CdrMeetingDao::getCntByAnswerTimeAndCreateTime($auditor_id, 0, 2*60, $startTime, $endTime);
        $between2_5 = CdrMeetingDao::getCntByAnswerTimeAndCreateTime($auditor_id, 2*60, 5*60, $startTime, $endTime);
        $between5_10 = CdrMeetingDao::getCntByAnswerTimeAndCreateTime($auditor_id, 5*60, 10*60, $startTime, $endTime);
        $between10_15 = CdrMeetingDao::getCntByAnswerTimeAndCreateTime($auditor_id, 10*60, 15*60, $startTime, $endTime);
        $greater15 = CdrMeetingDao::getCntByAnswerTimeAndCreateTime($auditor_id, 15*60, time(), $startTime, $endTime);
        $cdrMeeting_out_list = [];
        $less2==0 ? null : $cdrMeeting_out_list[] = ['name'=>"小于等于2分钟 : {$less2}个",'value'=>$less2];
        $between2_5==0 ? null : $cdrMeeting_out_list[] = ['name'=>"2分钟至5分钟 : {$between2_5}个",'value'=>$between2_5];
        $between5_10==0 ? null : $cdrMeeting_out_list[] = ['name'=>"5分钟至10分钟 : {$between5_10}个",'value'=>$between5_10];
        $between10_15==0 ? null : $cdrMeeting_out_list[] = ['name'=>"10分钟至15分钟 : {$between10_15}个",'value'=>$between10_15];
        $greater15==0 ? null : $cdrMeeting_out_list[] = ['name'=>"大于15分钟 : {$greater15}个",'value'=>$greater15];

        return $cdrMeeting_out_list;
    }

    /**
     * 此方法提供给业务层调用发送短信
     * 参数说明：
     *
     * account   	企业Id--enterpriseId必选，企业后台右上角的企业编号
     * userName   	    用户名--登录后台用户名，如admin
     * pwd   	        密码--对应username的密码，md5(md5(登录密码)+seed)，例如：md5(md5(123456)seed)
     * seed   		    随机字符串
     * type   	        短信类型--12表示座席发送，8表示后台发送
     * mobile   	    手机号--多个之间用英文逗号隔开
     * customerName   	客户姓名--多个之间用英文逗号隔开，此参数需要URLEncode
     * msg   	        短信内容--此参数需要URLEncode（msg最多180个字。每条短信最多60个字，当msg超过60个字时会被自动拆分）
     * cno   	        座席号--type为座席发送时使用
     **/
    public static function sendSms($mobile, $msg = "", $product = "") {
        DBC::requireTrue(11 == strlen($mobile), "要发送短信的手机号不是11位！");
        DBC::requireTrue(false == empty($msg), "要发送的短信信息不能为空！");

        $account = Config::getConfig('cdr_vlink_account');
        $pswd = Config::getConfig('cdr_vlink_pswd');
        $product = Config::getConfig('cdr_vlink_product');

        $params = array (
            'account' => $account,
            'pswd' => $pswd,
            'mobile' => $mobile,
            'msg' => $msg ,
            'needstatus' => true,
            'product' => $product,
        );

        Debug::trace($params);
        $url = "http://sms.vlink.cn/msg/HttpBatchSendSM";

        $str = FUtil::curlPost($url, $params, 5);
        Debug::trace($str);
    }
}