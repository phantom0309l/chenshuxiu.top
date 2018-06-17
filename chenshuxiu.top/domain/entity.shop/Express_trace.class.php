<?php
/*
 * Express_trace
 */
class Express_trace extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return  array(
        'express_no'    //快递号
        ,'shoporderid'    //shoporderid
        ,'sub_time'    //订阅时间
        ,'sub_status'    //订阅状态, 0:未订阅; 1:订阅成功
        ,'sub_reason'    //订阅失败原因
        ,'start_time'    //配送开始时间
        ,'end_time'    //配送结束时间
        ,'state'    //物流状态: 0-无轨迹，1-已揽收，2-在途中 201-到达派件城市，3-签收,4-问题件
        ,'traces'    //追踪数据,包括运单当前所有的的物流信息
        ,'remark'    //备注
        );
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array('shoporderid' ,);
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();
        $this->_belongtos["shoporder"] = array ("type" => "ShopOrder", "key" => "shoporderid" );
    }

    // $row = array();
    // $row["express_no"] = $express_no;
    // $row["shoporderid"] = $shoporderid;
    // $row["sub_time"] = $sub_time;
    // $row["sub_status"] = $sub_status;
    // $row["sub_reason"] = $sub_reason;
    // $row["start_time"] = $start_time;
    // $row["end_time"] = $end_time;
    // $row["state"] = $state;
    // $row["traces"] = $traces;
    // $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row,"Express_trace::createByBiz row cannot empty");

        $default = array();
        $default["express_no"] = '';
        $default["shoporderid"] =  0;
        $default["sub_time"] = '0000-00-00 00:00:00';
        $default["sub_status"] =  0;
        $default["sub_reason"] = '';
        $default["start_time"] = '0000-00-00 00:00:00';
        $default["end_time"] = '0000-00-00 00:00:00';
        $default["state"] =  0;
        $default["traces"] = '';
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    //已经订阅成功
    public function isSub(){
        return 1 == $this->sub_status;
    }

    //已完成配送
    public function isFinished(){
        return 3 == $this->state;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
