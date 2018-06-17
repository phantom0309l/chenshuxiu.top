<?php
    /*
     * DoctorServiceOrderTpl
     */
    class DoctorServiceOrderTpl extends Entity
    {
        protected function init_keys()
        {
            $this->_keys = self::getKeysDefine();
        }

        public static function getKeysDefine()
        {
            return  array(
            'ename'    //英文名称
            ,'title'    //标题
            ,'content'    //内容
            ,'price'    //单价，单位分
            );
        }

        protected function init_keys_lock()
        {
            $this->_keys_lock = array();
        }

        protected function init_belongtos()
        {
            $this->_belongtos = array();
        }

        // $row = array();
        // $row["ename"] = $ename;
        // $row["title"] = $title;
        // $row["content"] = $content;
        // $row["price"] = $price;
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"DoctorServiceOrderTpl::createByBiz row cannot empty");

            $default = array();
            $default["ename"] = '';
            $default["title"] = '';
            $default["content"] = '';
            $default["price"] =  0;

            $row += $default;
            return new self($row);
        }

        // ====================================
        // ------------ obj method ------------
        // ====================================

        public function getPrice_yuan () {
            return sprintf("%.2f", $this->price / 100);
        }

        // ====================================
        // ----------- static method ----------
        // ====================================

    }
