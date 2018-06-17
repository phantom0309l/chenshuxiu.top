<?php
    /*
     * OpTaskCron
     */
    class OpTaskCron extends Entity
    {
        protected function init_keys()
        {
            $this->_keys = self::getKeysDefine();
        }

        public static function getKeysDefine()
        {
            return  array(
            'optaskid'    //
        ,'optasktplcronid'    //
        ,'plan_exe_time'    //执行时间
        ,'content'      //发送内容
        ,'status'    //状态 0：未发送　1：已发送 2：被中断
        ,'remark'   // 备注
            );
        }

        protected function init_keys_lock()
        {
            $this->_keys_lock = array('optaskid' ,'optasktplcronid' ,);
        }

        protected function init_belongtos()
        {
            $this->_belongtos = [];
            $this->_belongtos["optask"] = array ("type" => "OpTask", "key" => "optaskid" );
            $this->_belongtos["optasktplcron"] = array ("type" => "OpTaskTplCron", "key" => "optasktplcronid" );
        }

        // $row = array(); 
        // $row["optaskid"] = $optaskid;
        // $row["optasktplcronid"] = $optasktplcronid;
        // $row["plan_exe_time"] = $plan_exe_time;
        // $row["content"] = '';
        // $row["status"] = $status;
        // $row["remark"] = '';
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"OpTaskCron::createByBiz row cannot empty");

            $default = array();
            $default["optaskid"] =  0;
            $default["optasktplcronid"] =  0;
            $default["plan_exe_time"] = '';
            $default["content"] = '';
            $default["status"] =  0;
            $default["remark"] = '';

            $row += $default;
            return new self($row);
        }

        // ====================================
        // ------------ obj method ------------
        // ====================================
        public function getStatusStr() {
            $arr = self::getStatuArr();

            return $arr["{$this->status}"];
        }

        // 获取发送内容
        public function getSendContent () {
            return $this->content ? $this->content : $this->optasktplcron->send_content;
        }

        // ====================================
        // ----------- static method ----------
        // ====================================
        public static function getStatuArr () {
            $arr = [
                '0' => '未执行',
                '1' => '已执行',
                '2' => '已中断'
            ];

            return $arr;
        }

    }
    