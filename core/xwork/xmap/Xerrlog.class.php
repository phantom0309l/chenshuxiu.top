<?php

/*
 * Xerrlog
 */
class Xerrlog extends Entity
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
            'level',  // XWORK < TRC < SQL < INF < WAR < ERR
            'content',  // 内容
            'auditorid',  // 责任人
            'status',  // 0 待处理, 1 已处理, 2 已归档
            'remark'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
        $this->_keys_lock[] = 'randno';
        $this->_keys_lock[] = 'xunitofworkid';
        $this->_keys_lock[] = 'level';
        $this->_keys_lock[] = 'content';
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["xunitofwork"] = array(
            "type" => "XUnitOfWork",
            "tableno" => $this->randno,
            "key" => "xunitofworkid");

        $this->_belongtos["auditor"] = array(
            "type" => "Auditor",
            "key" => "auditorid");
    }

    // $row = array();
    // $row["randno"] = $randno;
    // $row["xunitofworkid"] = $xunitofworkid;
    // $row["level"] = $level;
    // $row["content"] = $content;
    public static function createByBiz ($row, $dbconf = []) {
        DBC::requireNotEmpty($row, "Xerrlog::createByBiz row cannot empty");

        $default = array();
        $default["auditorid"] = 0;
        $default["status"] = 0;
        $default["remark"] = '';

        $row += $default;

        $entity = new self($row, $dbconf);

        $entity->fixContent();

        return $entity;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getStatusDesc () {
        return self::status($this->status);
    }

    public function isNew () {
        return $this->status == 0;
    }

    public function fixContent () {
        $content = self::contentFix($this->content);
        $this->set4lock('content', $content);
    }

    public function getDate () {
        return date('Ymd', substr($this->xunitofworkid, 0, 10));
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getLevels () {
        $arr = array(
            'all' => "全部",
            'WAR' => "警告",
            'ERR' => "错误");
        return $arr;
    }

    public static function getStatuss () {
        $arr = array(
            - 1 => "<span class='blue'>全部</span>",
            0 => "<span class='red'>待处理</span>",
            1 => "<span class='gray'>忽略</span>",
            2 => "<span class='green'>已处理</span>");
        return $arr;
    }

    public static function status ($value) {
        $arr = self::getStatuss();
        return $arr[$value];
    }

    public static function contentFix ($content) {
        $content = preg_replace("/[\s]{2,}/", "", $content);
        $content = str_replace("#", "\n#", $content);
        $content = str_replace("in /home", "\nin /home", $content);
        $content = str_replace("[ERR]", "\n[ERR]", $content);
        $content = str_replace("[WAR]", "\n[WAR]", $content);
        $content = str_replace("\n  \n", "\n", $content);
        $content = str_replace("\n \n", "\n", $content);
        $content = str_replace("\n\n", "\n", $content);
        return $content;
    }
}
