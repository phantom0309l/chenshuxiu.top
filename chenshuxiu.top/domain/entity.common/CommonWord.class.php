<?php

/*
 * CommonWord
 */
class CommonWord extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'ownertype',  // 所属对象type
            'ownerid',  // 所属对象id
            'typestr',  // 类型
            'groupstr',  // 分组
            'content',  // 内容
            'weight'); // 权重
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'ownerid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();

        $this->_belongtos["owner"] = array(
            "type" => $this->ownertype,
            "key" => "ownerid");
    }

    // $row = array();
    // $row["ownertype"] = $ownertype;
    // $row["ownerid"] = $ownerid;
    // $row["typestr"] = $typestr;
    // $row["groupstr"] = $groupstr;
    // $row["content"] = $content;
    // $row["weight"] = $weight;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "CommonWord::createByBiz row cannot empty");

        $default = array();
        $default["ownertype"] = '';
        $default["ownerid"] = 0;
        $default["typestr"] = '';
        $default["groupstr"] = '';
        $default["content"] = '';
        $default["weight"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getDoctorDesc () {
        if ($this->ownertype == 'Doctor') {
            return $this->owner->name . '(' . $this->owner->id . ')';
        } else {
            return $this->owner->doctor->name . '(' . $this->owner->doctor->id . ')';
        }
    }

    public function getTypestrDesc () {
        $arr = array(
            'symptom' => '症状体征(symptom)',
            'adverseevent' => '不良反应(adverseevent)',
            'diagnosis' => '诊断(diagnosis)');

        return $arr[$this->typestr];
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
}
