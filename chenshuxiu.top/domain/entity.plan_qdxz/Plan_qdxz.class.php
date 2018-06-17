<?php
    /*
     * Plan_qdxz
     */
    class Plan_qdxz extends Entity
    {
        protected function init_keys()
        {
            $this->_keys = self::getKeysDefine();
        }

        public static function getKeysDefine()
        {
            return  array(
            'wxuserid'    //wxuserid
        ,'userid'    //userid
        ,'patientid'    //patientid
        ,'plan_date'    //发送日期
        ,'status'    //0：未发送  1：已发送
            );
        }

        protected function init_keys_lock()
        {
            $this->_keys_lock = array('wxuserid' ,'userid' ,'patientid' ,);
        }

        protected function init_belongtos()
        {
            $this->_belongtos = array();
            $this->_belongtos["wxuser"] = array ("type" => "WxUser", "key" => "wxuserid" );
            $this->_belongtos["user"] = array ("type" => "User", "key" => "userid" );
            $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        }

        // $row = array(); 
        // $row["wxuserid"] = $wxuserid;
        // $row["userid"] = $userid;
        // $row["patientid"] = $patientid;
        // $row["plan_date"] = $plan_date;
        // $row["status"] = $status;
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"Plan_qdxz::createByBiz row cannot empty");

            $default = array();
            $default["wxuserid"] =  0;
            $default["userid"] =  0;
            $default["patientid"] =  0;
            $default["plan_date"] = '';
            $default["status"] =  0;

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
    