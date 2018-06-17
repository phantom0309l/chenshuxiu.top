<?php

/*
 * XObjLog
 */
class XObjLog extends Entity
{

    protected function init_database () {
        $this->database = 'xworkdb';
    }

    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'randno',  // 日期散列
            'xunitofworkid',  // xunitofworkid
            'type',  // 0 新增, 1 修改, 2 删除, 3 初始化, 4 恢复, 5 修订
            'objtype',  // objtype
            'objid',  // objid
            'objver',  // objver
            'content',  // content
            'randno_fix'); // MD5暂列
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
        // $this->_keys_lock = self::getKeysDefine();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");

        $this->_belongtos["xunitofwork"] = array(
            "type" => "XUnitOfWork",
            "tableno" => $this->randno,
            "key" => "xunitofworkid");
    }

    // $row = array();
    // $row["randno"] = $randno;
    // $row["xunitofworkid"] = $xunitofworkid;
    // $row["type"] = $type;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["objver"] = $objver;
    // $row["content"] = $content;
    // $row["randno_fix"] = $randno_fix;
    public static function createByBiz ($row, $dbconf = []) {
        DBC::requireNotEmpty($row, "XObjLog::createByBiz row cannot empty");

        $default = array();
        $default["randno"] = 0;
        $default["xunitofworkid"] = 0;
        $default["type"] = 0;
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["objver"] = 0;
        $default["content"] = '';
        $default["randno_fix"] = 0;

        $row += $default;
        return new self($row, $dbconf);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getTypeStr () {
        $typearr = array();
        $typearr['0'] = '新增';
        $typearr['1'] = '修改';
        $typearr['2'] = '删除';
        $typearr['3'] = '初始化';
        $typearr['4'] = '恢复';
        $typearr['5'] = '修订';

        return $typearr["{$this->type}"] ? $typearr["{$this->type}"] : "";
    }

    // 获取快照中的值
    public function getValueByKey ($key) {
        $arr = json_decode($this->content, true);
        return $arr[$key];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    // 生成 tableno
    public static function getTablenoByObjtypeObjid ($objtype, $objid) {
        $str = "{$objtype}:{$objid}";
        $md5str = md5($str);
        $tableno = substr($md5str, 0, 2);
        $tableno = hexdec($tableno);
        $tableno = 1000 + $tableno;

        return $tableno;
    }

    public static function getObjtypeArr () {
        $sql = " show tables ";

        $tables = Dao::queryValues($sql, []);

        $arr = array();
        foreach ($tables as $table) {
            $entityType = XObjLog::table2entityType($table);
            $arr[] = $entityType;
        }

        return $arr;
    }

    public static function getObjtypeStr () {
        $arr = self::getObjtypeArr();

        $arrstr = array();
        $arrstr['all'] = 'all';
        foreach ($arr as $a) {
            if ($a) {
                $arrstr["{$a}"] = $a;
            }
        }

        return $arrstr;
    }

    // 表名 => 实体类名
    public static function table2entityType ($table) {
        global $lowerclasspath;

        $tabl = substr($table, 0, strlen($table) - 1);
        $entityType = $lowerclasspath[$tabl];

        return $entityType;
    }

    public static function getSnapByObj ($objtype, $objid, $objver) {
        $xobjlogs = XObjLogDao::getListByObjtypeObjid($objtype, $objid);

        $arr = array();
        foreach ($xobjlogs as $xobjlog) {
            if ($xobjlog->objver > $objver) {
                break;
            }

            $c_arr = json_decode($xobjlog->content, true);

            foreach ($c_arr as $k => $v) {
                $arr[$k] = $v;
            }
        }

        return $arr;
    }
}
