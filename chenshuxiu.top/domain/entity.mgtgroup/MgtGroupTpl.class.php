<?php
    /*
     * MgtGroupTpl
     */
    class MgtGroupTpl extends Entity
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
        ,'brief'    //摘要
        ,'content'    //content
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
    // $row["brief"] = $brief;
    // $row["content"] = $content;
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"MgtGroupTpl::createByBiz row cannot empty");

            $default = array();
             $default["ename"] = '';
             $default["title"] = '';
             $default["brief"] = '';
             $default["content"] = '';

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
    