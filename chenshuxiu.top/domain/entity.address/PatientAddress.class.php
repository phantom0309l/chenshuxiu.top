<?php
/*
 * PatientAddress
 */
class PatientAddress extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'type'    //地址类型
            ,'patientid'    //patientid
            ,'xprovinceid'  // 省id
            ,'xcityid' // 市id
            ,'xcountyid'  // 区id
            ,'content'  // 详细地址
        );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'patientid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["patient"] = array ("type" => "Patient", "key" => "patientid" );
        $this->_belongtos["xprovince"] = array(
            "type" => "Xprovince",
            "key" => "xprovinceid");
        $this->_belongtos["xcity"] = array(
            "type" => "Xcity",
            "key" => "xcityid");
        $this->_belongtos["xcounty"] = array(
            "type" => "Xcounty",
            "key" => "xcountyid");
    }

    // $row = array();
    // $row["type"] = $type;
    // $row["patientid"] = $patientid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PatientAddress::createByBiz row cannot empty");

        // 四大直辖市
        $four = [110000, 120000, 310000, 500000];
        if (in_array($row['xprovinceid'], $four)) {
            if ($row['xcityid'] != ($row['xprovinceid'] + 100)) {
                $row['xcountyid'] = $row['xcityid'];
                $row['xcityid'] = $row['xprovinceid'] + 100;
            }
        }

        $default = array();
        $default["type"] = '';
        $default["patientid"] =  0;
        $default["xprovinceid"] = 0;
        $default["xcityid"] = 0;
        $default["xcountyid"] = 0;
        $default["content"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getAddressStr () {
        $four = [110000, 120000, 310000, 500000];
        if (in_array($this->xprovinceid, $four)) {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = "";
        } else {
            $xprovince_name = $this->xprovince->name;
            $xcity_name = $this->xcity->name;
        }
        $xcounty_name = $this->xcounty->name;
        $content = $this->content;

        return "{$xprovince_name}{$xcity_name}{$xcounty_name}{$content}";
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
