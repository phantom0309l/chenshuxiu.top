<?php
/*
 * Herb
 */
class Herb extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'name',  // 草药名称
            'pinyin',  // 拼音
            'py'); // 拼音首字母
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

    }

    // $row = array();
    // $row["name"] = $name;
    // $row["pinyin"] = $pinyin;
    // $row["py"] = $py;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Herb::createByBiz row cannot empty");

        $default = array();
        $default["name"] = '';
        $default["pinyin"] = PinyinUtilNew::Word2PY($row['name'], '');
        $default["py"] = strtolower(PinyinUtilNew::Word2PY($row['name']));

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 各处herbjson的储存方式 药材名=用量|.....

    public static function arr2edit($arr){
        $arr_t = array();
        if(empty($arr)){
            return '';
        }

        foreach( $arr as $herb ){
            if( empty($herb) ){
                continue;
            }

            Debug::trace("-------".$herb['name'] . "=" . $herb['content']);
            $arr_t[] = $herb['name'] . "=" . $herb['content'];
        }

        if(empty($arr_t)){
            return '';
        }
        return implode("|",$arr_t);
    }

    public static function edit2arr($str){
        if('' == trim($str)){
            return array();
        }
        $arr_f = array();

        foreach( explode('|',$str) as $item ){
            $temp_arr = explode('=',$item);
            $arr_f[] = array(
                'name'=>$temp_arr[0],
                'content'=>$temp_arr[1]
            );
        }
        return $arr_f;
    }

    public static function edit2show($str){
        $arr = self::edit2arr($str);

        $str = '';
        foreach( $arr as $a_v ){
            $str .= $a_v['name'] . ":" . $a_v['content'] . "\n";
        }

        return $str;
    }

}
