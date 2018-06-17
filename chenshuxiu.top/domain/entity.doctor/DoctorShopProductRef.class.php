<?php
    /*
     * DoctorShopProductRef
     */
    class DoctorShopProductRef extends Entity
    {
        protected function init_keys()
        {
            $this->_keys = self::getKeysDefine();
        }

        public static function getKeysDefine()
        {
            return  array(
            'doctorid'    //doctorid
            ,'shopproductid'    //shopproductid
            );
        }

        protected function init_keys_lock()
        {
            $this->_keys_lock = array('doctorid' ,'shopproductid' ,);
        }

        protected function init_belongtos()
        {
            $this->_belongtos = array();
            $this->_belongtos["doctor"] = array ("type" => "Doctor", "key" => "doctorid" );
            $this->_belongtos["shopproduct"] = array ("type" => "ShopProduct", "key" => "shopproductid" );
        }

        // $row = array();
        // $row["doctorid"] = $doctorid;
        // $row["shopproductid"] = $shopproductid;
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"DoctorShopProductRef::createByBiz row cannot empty");

            $default = array();
             $default["doctorid"] =  0;
             $default["shopproductid"] =  0;

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
