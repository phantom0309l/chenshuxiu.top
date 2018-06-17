<?php
/*
 * 导出任务
 */
class Export_Job extends Entity
{
    const STATUS_NEW = 0;//初始状态
    const STATUS_RUNNING = 1;//正在进行
    const STATUS_FAILED = 2;//失败
    const STATUS_COMPLETE = 3;//完成
    const STATUS_REMOVED = 4;//删除

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return [
            'type',  //
            'data',  // data
            'progress',  // patientid
            'doctorid',  // doctorid
            'diseaseid',  // diseaseid
            'auditorid',
            'patienttagtplid',
            'status',
        ];
    }

    protected function init_keys_lock () {
        $this->_keys_lock = [
            'wxuserid',
            'userid',
            'patientid',
            'doctorid',
            'auditorid'
        ];
    }

    protected function init_belongtos () {
        $this->_belongtos = [];
        $this->_belongtos["doctor"] = [
            "type" => "Doctor",
            "key" => "doctorid"
        ];
        $this->_belongtos["auditor"] = [
            "type" => "Auditor",
            "key" => "auditorid"
        ];
        $this->_belongtos["disease"] = [
            "type" => "Disease",
            "key" => "diseaseid"
        ];
        $this->_belongtos["patienttagtpl"] = [
            "type" => "PatientTagTpl",
            "key" => "patienttagtplid"
        ];
    }

    // $row = array();
    // $row['type'] = $type;
    // $row['data'] = $data;
    // $row['patientid'] = $patientid;
    // $row["doctorid"] = $doctorid;
    // $row['auditorid'] = $auditorid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, __METHOD__ . ' row cannot empty');

        $default = array();
        $default["type"] = '';
        $default["data"] = '';
        $default["progress"] = 0;
        $default["doctorid"] = 0;
        $default["diseaseid"] = 0;
        $default['auditorid'] = 0;
        $default['patienttagtplid'] = 0;
        $default['status'] = self::STATUS_NEW;

        $row += $default;
        return new self($row);
    }

    public static function getTypeDescArr(){
        $arr = [];
        $arr["all"] = "全部";
        $arr["doctor_checkup"] = "量表导出";
        $arr["shoporder_service"] = "服务1";
        $arr["shoporder_service2"] = "服务2";
        $arr["shoporder_market"] = "市场";
        $arr["shoporder_detail"] = "订单明细";
        $arr["sunflowerforpatient"] = "礼来(患者纬度)";
        $arr["ADHD_KPI"] = "持续服药率统计";
        return $arr;
    }

    public static function getStatusDescArr(){
        $arr = [];
        $arr[self::STATUS_NEW] = "初始状态";
        $arr[self::STATUS_RUNNING] = "正在进行";
        $arr[self::STATUS_FAILED] = "失败";
        $arr[self::STATUS_COMPLETE] = "完成";
        $arr[self::STATUS_REMOVED] = "删除";
        return $arr;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeDesc(){
        $type = $this->type;
        $arr = self::getTypeDescArr();
        return $arr[$type] ?? "";
    }

    public function getStatusDesc(){
        $status = $this->status;
        $arr = self::getStatusDescArr();
        return $arr[$status] ?? "";
    }

    public function isNew() {
        return $this->status == self::STATUS_NEW;
    }

    public function isComplete() {
        return $this->status == self::STATUS_COMPLETE;
    }

    public function isRunning() {
        return $this->status == self::STATUS_RUNNING;
    }

    public function getDownloadUrl(){
        $url = "";
        $dluri = Config::getConfig("dl_uri");
        $type = $this->type;

        //医生后台checkup导出
        $export_jobid = $this->id;
        if($type == "doctor_checkup"){
            $url = "{$dluri}/doctordb/" . md5($export_jobid) . ".xls";
        }

        //shoporder导出相关地址
        $type_part = substr($type, 0, 10);
        if($type_part == "shoporder_"){
            $url = "{$dluri}/shoporder/" . md5($export_jobid) . ".xlsx";
        }

        $arr_audit = ["sunflowerforpatient", "ADHD_KPI"];
        if( in_array($type, $arr_audit) ){
            $url = "{$dluri}/audit/" . md5($export_jobid) . ".xlsx";
        }

        return $url;
    }

    //获取完成下载时间
    public function getCompleteTime(){
        return $this->isComplete() ? $this->updatetime : "";
    }

}
