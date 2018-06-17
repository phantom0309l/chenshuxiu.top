<?php
/*
 * BedTktPaperRef
 */
class BedTktPaperRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'bedtktid'    //bedtktid
        ,'paperid'    //paperid
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'bedtktid' ,'paperid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["bedtkt"] = array ("type" => "BedTkt", "key" => "bedtktid" );
        $this->_belongtos["paper"] = array ("type" => "Paper", "key" => "paperid" );
    }

    // $row = array();
    // $row["bedtktid"] = $bedtktid;
    // $row["paperid"] = $paperid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "BedTktPaperRef::createByBiz row cannot empty");

        $default = array();
        $default["bedtktid"] =  0;
        $default["paperid"] =  0;

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
