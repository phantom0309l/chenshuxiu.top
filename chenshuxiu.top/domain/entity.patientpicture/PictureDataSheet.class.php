<?php
/*
 * PictureDataSheet
 */
class PictureDataSheet extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'picturedatasheettplid'    //picturedatasheettplid
            ,'patientpictureid'    //patientpictureid
            ,'thedate'    //日期
            ,'answercontents'    //答案数组json
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'picturedatasheettplid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["picturedatasheettpl"] = array ("type" => "PictureDataSheetTpl", "key" => "picturedatasheettplid" );
        $this->_belongtos["patientpicture"] = array ("type" => "PatientPicture", "key" => "patientpictureid" );
    }

    // $row = array();
    // $row["picturedatasheettplid"] = $picturedatasheettplid;
    // $row["patientpictureid"] = $patientpictureid;
    // $row["thedate"] = $thedate;
    // $row["answercontents"] = $answercontents;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PictureDataSheet::createByBiz row cannot empty");

        $default = array();
        $default["picturedatasheettplid"] =  0;
        $default["patientpictureid"] =  0;
        $default["thedate"] = '';
        $default["answercontents"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getQaArr(){
        $pairs = explode("&&&",$this->answercontents);
        $qas = array();

        foreach( $pairs as $pair ){
            $temparr = explode("###",$pair);
            $qas[] = array(
                'q' => $temparr[0],
                'a' => $temparr[1]
            );
        }

        return $qas;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
