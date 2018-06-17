<?php

/*
 * PatientTagTpl
 */
class PatientTagTpl extends Entity
{

    protected function init_keys()
    {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine()
    {
        return array(
            'doctorid', // 所属医生
            'pos', // 排序
            'name', // 标签名称
            'content', // 标签描述
            'create_userid' // 创建userid
        ); // 备注

    }

    protected function init_keys_lock()
    {
        $this->_keys_lock = array(
            'doctorid'
        );
    }

    protected function init_belongtos()
    {
        $this->_belongtos = array();

        $this->_belongtos["doctor"] = array(
            "type" => "Doctor",
            "key" => "doctorid"
        );
        $this->_belongtos["createuser"] = array(
            "type" => "User",
            "key" => "create_userid"
        );
    }

    // $row = array();
    // $row["doctorid"] = $doctorid;
    // $row["pos"] = $pos;
    // $row["name"] = $name;
    // $row["content"] = $content;
    // $row["remark"] = $remark;
    public static function createByBiz($row)
    {
        DBC::requireNotEmpty($row, "PatientTagTpl::createByBiz row cannot empty");

        $default = array();
        $default["doctorid"] = 0;
        $default["pos"] = 0;
        $default["name"] = '';
        $default["content"] = '';
        $default["create_userid"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getPatientTagTplStrArr (Doctor $mydoctor, $needall = false) {
        $patienttagtpls = PatientTagTplDao::getListByDoctor($mydoctor);

        $list = array();
        foreach ($patienttagtpls as $a) {
            $tmp = array();

            $tmp['id'] = $a->id;

            $str = mb_substr($a->name, 0, 7);
            if (mb_strlen($a->name) > 7) {
                $str .= '...';
            }

            $tmp['name'] = $str;

            $list[] = $tmp;
        }

        if ($needall) {
            $tmp = array();

            $tmp['id'] = 0;
            $tmp['name'] = '全部';

            $list[] = $tmp;
        }

        return $list;
    }
}
