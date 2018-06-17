<?php

/*
 * DrugStore
 */

class DrugStore extends Entity
{
    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return [
              'title'    //药店名称
            , 'xprovinceid'    //xprovinceid
            , 'xcityid'    //xcityid
            , 'xcountyid'    //xcountyid
            , 'content'    //具体地址
            , 'mobile'    //联系电话
        ];
    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array();
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();

        $this->_belongtos["xprovince"] = array("type" => "Xprovince", "key" => "xprovinceid");
        $this->_belongtos["xcity"] = array("type" => "Xcity", "key" => "xcityid");
        $this->_belongtos["xcounty"] = array("type" => "Xcounty", "key" => "xcountyid");
    }

    // $row = array();
    // $row["title"] = $title;
    // $row["xprovinceid"] = $xprovinceid;
    // $row["xcityid"] = $xcityid;
    // $row["xcountyid"] = $xcountyid;
    // $row["content"] = $content;
    // $row["mobile"] = $mobile;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "DrugStore::createByBiz row cannot empty");

        $default = array();
        $default["title"] = '';
        $default["xprovinceid"] = 0;
        $default["xcityid"] = 0;
        $default["xcountyid"] = 0;
        $default["content"] = '';
        $default["mobile"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getAddressStr () {
        return $this->xprovince->name . $this->xcity->name . $this->xcounty->name . $this->content;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
    