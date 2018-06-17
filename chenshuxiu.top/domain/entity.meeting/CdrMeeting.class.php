<?php
/*
 * CdrMeeting
 */
class CdrMeeting extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
         'wxuserid'
        ,'userid'    //
        ,'patientid'    //
        ,'auditorid'
        ,'cdr_main_unique_id'    //一通呼叫的唯一标识；如：ccic2_202-1367040082.6
        ,'cdr_enterprise_id'    //    企业号，7位数字；如：3000290
        ,'cdr_customer_number'    //客户的号码；如：01087128906
        ,'cdr_customer_area_code'    //呼入或外呼座席接听后的座席区号 3位或4位电话区号；如：010
        ,'cdr_customer_number_type'    //客户号码类型，其值为1或2；1--固话，2--手机
        ,'cdr_start_time'    //呼叫座席时间 时间戳
        ,'cdr_answer_time'    //座席接听时间
        ,'cdr_bridge_time'    //客户接听时间
        ,'cdr_end_time'    //挂机时间
        ,'cdr_call_type'    //呼叫类型 1:呼入 2:web400呼入 3:点击外呼 4:预览外呼
        ,'cdr_status'    //通话状态 1:座席接听 2:已呼叫座席，座席未接听 3:系统接听 4:系统未接-IVR配置错误 5:系统未接-停机 6:系统未接-欠费 7:系统未接-黑名单 8:系统未接-未注册 9:系统未接-彩铃 10:网上400未接受 11:系统未接-呼叫超出营帐中设置的最大限制 12:其他错误 21:（点击外呼、预览外呼时）座席接听，客户未接听(超时) 22:（点击外呼、预览外呼时）座席接听，客户未接听(空号拥塞) 24:（点击外呼、预览外呼时）座席未接听 28:双方接听
        ,'cdr_number_trunk'    //外显号码
        ,'cdr_bridged_cno'    //呼出接听电话的座席号码 2000
        ,'cdr_record_file'    //录音文件名在文件名前补充http://api.clink.cn/yyyymmdd(年月日)/，并在后面补充
        ,'cdr_hotline'    //热线电话
        ,'cdr_json'       //cdr识别出的json ， 可能被修改
        ,'cdr_json_back'  //cdr识别出的原始json
        ,'filename'     //录音文件名
        ,'downloadstatus'       //下载录音状态： 1:已下载    2:下载失败
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'wxuserid','userid' ,'patientid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
        $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["auditor"] = array ("type" => "Auditor", "key" => "auditorid" );
    }

    // $row = array();
    // $row["userid"] = $userid;
    // $row["patientid"] = $patientid;
    // $row["cdr_main_unique_id"] = $cdr_main_unique_id;
    // $row["cdr_enterprise_id"] = $cdr_enterprise_id;
    // $row["cdr_customer_number"] = $cdr_customer_number;
    // $row["cdr_customer_area_code"] = $cdr_customer_area_code;
    // $row["cdr_customer_number_type"] = $cdr_customer_number_type;
    // $row["cdr_start_time"] = $cdr_start_time;
    // $row["cdr_answer_time"] = $cdr_answer_time;
    // $row["cdr_bridge_time"] = $cdr_bridge_time;
    // $row["cdr_end_time"] = $cdr_end_time;
    // $row["cdr_call_type"] = $cdr_call_type;
    // $row["cdr_status"] = $cdr_status;
    // $row["cdr_number_trunk"] = $cdr_number_trunk;
    // $row["cdr_bridged_cno"] = $cdr_bridged_cno;
    // $row["cdr_record_file"] = $cdr_record_file;
    // $row["cdr_hotline"] = $cdr_hotline;
    // $row['cdr_json'] = '';
    // $row['cdr_json_back'] = '';
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CdrMeeting::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] =  0;
        $default["userid"] =  0;
        $default["patientid"] =  0;
        $default["cdr_main_unique_id"] = '';
        $default["cdr_enterprise_id"] = '';
        $default["cdr_customer_number"] = '';
        $default["cdr_customer_area_code"] = '';
        $default["cdr_customer_number_type"] =  0;
        $default["cdr_start_time"] =  0;
        $default["cdr_answer_time"] =  0;
        $default["cdr_bridge_time"] =  0;
        $default["cdr_end_time"] =  0;
        $default["cdr_call_type"] =  0;
        $default["cdr_status"] =  0;
        $default["cdr_number_trunk"] = '';
        $default["cdr_bridged_cno"] =  0;
        $default["cdr_record_file"] = '';
        $default["cdr_hotline"] = '';
        $default['cdr_json'] = '';
        $default['cdr_json_back'] = '';
        $default["filename"] = '';
        $default["downloadstatus"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 呼叫时间
    public function formatStartTime () {
        return date('Y-m-d H:i:s', $this->cdr_start_time);
    }

    // 挂机时间
    public function formatEndTime () {
        return date('Y-m-d H:i:s', $this->cdr_end_time);
    }

    // 通话时长
    public function formatDuration () {
        if ($this->downloadstatus == 2 || $this->cdr_record_file == '') {
            return "呼叫失败";
        }

        return ($this->cdr_end_time - $this->cdr_bridge_time) . '秒';
    }

    public function getVoiceUrl () {
        $voiceUri = Config::getConfig('voice_uri');
        return $voiceUri . '/' . $this->filename;
    }

    public function getInputStatusArr () {
        return array(1,2);
    }

    public function getOutputStatusArr () {
        return array(3,4);
    }

    //是否患者呼入
    public function isPatientCallIn(){
        $inputs = $this->getInputStatusArr();
        if (in_array($this->cdr_call_type, $inputs)) {
            return true;
        }else{
            return false;
        }
    }

    //是否通话成功
    //成功 -> 1:坐席接听 28:双方接听
    //失败-> 其他类型
    public function isCallOk(){
        $cdr_status = $this->cdr_status;
        if ($cdr_status == 1 || $cdr_status == 28) {
            return true;
        }else{
            return false;
        }
    }

    //获取呼叫结果
    public function getCallResultDesc(){
        $str = "";
        if($this->isCallOk()){
            $str = "成功";
        }else{
            $cdr_status = $this->cdr_status;
            if ($cdr_status == 2 || $cdr_status == 24) {
                $str = "座席未接听";
            } else if ($cdr_status == 21 | $cdr_status == 22) {
                $str = "患者未接听";
            } else {
                $str = "<span class='text-danger'>呼叫失败</span>";
            }
        }
        return $str;
    }

    //是否需要下载文件
    public function needDownloadVoiceFile(){
        if ($this->cdr_record_file != '' && $this->downloadstatus != 1) {
            return true;
        }else{
            return false;
        }
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getCdrStatusArr () {
        static $cdrstatuscode = array(
            '01' => '座席已接听',
            '00' => '正在呼叫',
            '1' => '呼叫座席失败',
            '2' => '参数不正确',
            '3' => '用户验证没有通过',
            '4' => '账号被停用',
            '5' => '资费不足',
            '6' => '指定的业务尚未开通',
            '7' => '电话号码不正确',
            '8' => '座席工号（cno）不存在',
            '9' => '座席状态不为空闲，可能未登录或',
            '10' => '其他错误',
            '11' => '电话号码为黑名单',
            '12' => '座席不在线',
            '13' => '座席正在通话/呼叫中',
            '14' => '外显号码不正确',
            '33' => '请勿重复点击外呼',
            '40' => '同一被叫号码，1分钟内限呼1次 \n同一被叫号码，10分钟内限呼6次 \n同一被叫号码，1小时内限呼10次 \n同一被叫号码，24小时内限呼30次 '
        );

        return $cdrstatuscode;
    }

}
