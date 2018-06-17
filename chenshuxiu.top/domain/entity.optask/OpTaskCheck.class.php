<?php
    /*
     * OpTaskCheck
     */
    class OpTaskCheck extends Entity
    {
        protected function init_keys()
        {
            $this->_keys = self::getKeysDefine();
        }

        public static function getKeysDefine()
        {
            return  array(
                'thedate' // thedate
                ,'optaskchecktplid'    //optaskchecktplid
                ,'xanswersheetid'    //xanswersheetid
                ,'auditor_id'    //autidor_id
                ,'optask_id'    //optask_id
                ,'checked_auditor_id'    //
                ,'checked_time'    //checked_time
                ,'status'    //状态
                ,'woy'    //week of year
                ,'remark'    //主管评价
            );
        }

        protected function init_keys_lock()
        {
            $this->_keys_lock = array();
        }

        protected function init_belongtos()
        {
            $this->_belongtos = array();
            $this->_belongtos["optaskchecktpl"] = array(
                "type" => "OpTaskCheckTpl",
                "key" => "optaskchecktplid");
            $this->_belongtos["xanswersheet"] = array(
                "type" => "XAnswerSheet",
                "key" => "xanswersheetid");
            $this->_belongtos["auditor"] = array(
                "type" => "Auditor",
                "key" => "auditor_id");
            $this->_belongtos["optask"] = array(
                "type" => "OpTask",
                "key" => "optask_id");
            $this->_belongtos["checkedAuditor"] = array(
                "type" => "Auditor",
                "key" => "checked_auditor_id");
        }

        // $row = array(); 
    // $row["optaskchecktplid"] = $optaskchecktplid;
    // $row["xanswersheetid"] = $xanswersheetid;
    // $row["auditor_id"] = $auditor_id;
    // $row["optask_id"] = $optask_id;
    // $row["checked_auditor_id"] = $checked_auditor_id;
    // $row["checked_time"] = $checked_time;
    // $row["status"] = $status;
    // $row["woy"] = $woy;
    // $row["remark"] = $remark;
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"OpTaskCheck::createByBiz row cannot empty");

            $default = array();
             $default["thedate"] = '0000-00-00';
             $default["optaskchecktplid"] =  0;
             $default["xanswersheetid"] =  0;
             $default["auditor_id"] =  0;
             $default["optask_id"] =  0;
             $default["checked_auditor_id"] =  0;
             $default["checked_time"] = '0000-00-00 00:00:00';
             $default["status"] =  0;
             $default["woy"] =  0;
             $default["remark"] = '';

            $row += $default;
            return new self($row);
        }

        // ====================================
        // ------------ obj method ------------
        // ====================================

        // ====================================
        // ----------- static method ----------
        // ====================================

    }
    