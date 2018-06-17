<?php
// Tag
// 标签

// owner by xxx
// create by sjp
// review by sjp 20160628

class Tag extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'typestr',  // 类型
            'name'); // 名称
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["typestr"] = $typestr;
    // $row["name"] = $name;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Tag::createByBiz row cannot empty");
        $entity = TagDao::getByTypestrAndName($row['typestr'], $row['name']);
        if ($entity instanceof Tag) {
            return $entity;
        }

        $default = array();
        $default["typestr"] = '';
        $default["name"] = '';

        $row += $default;
        return new self($row);
    }

    // 创建对象
    public static function createByName ($name, $typestr = "") {

        $row = array();
        $row["typestr"] = $typestr;
        $row["name"] = $name;
        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 标签类型
    public function getTypeStrDesc () {
        return self::getTypeStrDefine($this->typestr);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 标签类型定义
    public static function getTypeStrDefine ($typeStr = 'Patient') {
        $arr = self::getTypeStrDefines();
        return $arr[$typeStr];
    }

    // 标签类型定义数组
    public static function getTypeStrDefines ($needAll = false) {
        $arr = array();
        if ($needAll) {
            $arr['all'] = "全部";
        }
        $arr['Disease'] = "合并症";
        $arr['Patient'] = "患者分类";
        $arr['patientDiagnosis'] = '患者诊断标签';
        $arr['OpTask'] = "任务";
        $arr['WxPicMsg'] = "病历标签";
        $arr['sideeffect'] = "副反应";
        $arr['GradeLabel'] = "问题分级";
        $arr['medicineForWww'] = "前台网站用药分类";
        $arr['treatmentPhase'] = "治疗阶段";

        return $arr;
    }

    public static function getNamesStrByIds ($ids) {
        $str = '';
        if (! empty($ids)) {
            $tagids = explode(",", $ids);
            foreach ($tagids as $tagid) {
                if ($tagid == - 1) {
                    continue;
                }
                $tag = Tag::getById($tagid);
                $str .= "{$tag->name} ";
            }
        }
        return $str;
    }
}
