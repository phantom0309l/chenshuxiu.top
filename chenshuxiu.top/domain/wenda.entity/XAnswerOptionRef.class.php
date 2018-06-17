<?php

/*
 * XAnswerOptionRef
 */
class XAnswerOptionRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'xanswerid',  // 答案id
            'xoptionid',  // 选项id
            'content',  // 内容冗余,主要用于数据库维护方便,也有快照的意义
            'score'); // 得分
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'xanswerid',
            'xoptionid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["xanswer"] = array(
            "type" => "XAnswer",
            "key" => "xanswerid");
        $this->_belongtos["xoption"] = array(
            "type" => "XOption",
            "key" => "xoptionid");
    }

    public function copyOne ($xanswerNew) {
        $row = array();
        $row['xanswerid'] = $xanswerNew->id;
        $row['xoptionid'] = $this->xoptionid;
        $row['content'] = $this->content;
        $row['score'] = $this->score;

        return self::createByBiz($row);
    }

    // $row = array();
    // $row["xanswerid"] = $xanswerid;
    // $row["xoptionid"] = $xoptionid;
    // $row["content"] = $content;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "XAnswerOptionRef::createByBiz row cannot empty");

        $default = array();
        $default["content"] = '';
        $default["score"] = 0;

        $row += $default;

        return new self($row);
    }

    // createBy2Id
    public static function createBy2Id ($xanswerid, $xoptionid, $content = '', $score = 0) {
        $row = array();
        $row["xanswerid"] = $xanswerid;
        $row["xoptionid"] = $xoptionid;
        $row["content"] = $content;
        $row["score"] = $score;

        return self::createByBiz($row);
    }

    // createBy2Entity
    public static function createBy2Entity (XAnswer $xanswer, XOption $xoption) {
        $row = array();
        $row["xanswerid"] = $xanswer->id;
        $row["xoptionid"] = $xoption->id;
        $row["content"] = $xoption->content;
        $row["score"] = $xoption->score;

        return self::createByBiz($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // getBy2Id
    // public static function getBy2Id($xanswerid, $xoptionid) {
    // $cond = "AND xanswerid=:xanswerid AND xoptionid=:xoptionid ";
    // $bind = array ();
    // $bind [':xanswerid'] = $xanswerid;
    // $bind [':xoptionid'] = $xoptionid;
    // return Dao::getEntityByCond('XAnswerOptionRef', $cond, $bind );
    // }

    // 选中项列表
    public static function getArrayOfXAnswer (XAnswer $xanswer) {
        $cond = " AND xanswerid=:xanswerid ";
        $bind = [];
        $bind[':xanswerid'] = $xanswer->id;
        return Dao::getEntityListByCond('XAnswerOptionRef', $cond, $bind);
    }

    // 单个选中项列表
    public static function getOneByXAnswer (XAnswer $xanswer) {
        $cond = " AND xanswerid=:xanswerid ";
        $bind = [];
        $bind[':xanswerid'] = $xanswer->id;
        return Dao::getEntityByCond('XAnswerOptionRef', $cond, $bind);
    }

}
