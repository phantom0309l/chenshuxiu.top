<?php
/*
 * PictureDataSheetTpl
 */
class PictureDataSheetTpl extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'diseaseid'    //diseaseid
            ,'ename'    //编码
            ,'title'    //问题标题
            ,'questiontitles'    //问题标题列表, 每行一个
            );
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'diseaseid' ,);
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["disease"] = array ("type" => "Disease", "key" => "diseaseid" );
    }

    // $row = array();
    // $row["diseaseid"] = $diseaseid;
    // $row["ename"] = $ename;
    // $row["title"] = $title;
    // $row["questiontitles"] = $questiontitles;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "PictureDataSheetTpl::createByBiz row cannot empty");

        $default = array();
        $default["diseaseid"] =  0;
        $default["ename"] = '';
        $default["title"] = '';
        $default["questiontitles"] = '';

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getQaArr(){
        $titlecontent1 = str_replace("\r\n","\n",$this->questiontitles);
        $titlecontent = str_replace("\r","\n",$titlecontent1);
        $qs = explode("\n",$titlecontent);
        $qas = array();

        foreach( $qs as $q ){
            $qas[] = array(
                'q' => $q,
                'a' => ''
            );
        }

        return $qas;
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

}
