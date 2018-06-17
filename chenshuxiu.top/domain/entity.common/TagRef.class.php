<?php
// TagRef
// 标签-对象-关联 多对多
// 谨慎使用,要考虑是否单独建表

// owner by xxx
// create by sjp
// review by sjp 20160628

class TagRef extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'objtype',  //
            'objid',  //
            'tagid'); //
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'objtype',
            'objid',
            'tagid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
        $this->_belongtos["tag"] = array(
            "type" => "Tag",
            "key" => "tagid");
    }

    // $row = array();
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["tagid"] = $tagid;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "TagRef::createByBiz row cannot empty");

        $tagref = TagRefDao::getByObjtypeObjidTagid($row["objtype"], $row["objid"], $row["tagid"]);
        if ($tagref instanceof TagRef) {
            return $tagref;
        }

        $default = array();
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["tagid"] = 0;

        $row += $default;
        return new self($row);
    }

    // 创建对象
    public static function createByEntity (Entity $entity, $tagid) {
        $row = array();
        $row["objtype"] = get_class($entity);
        $row["objid"] = $entity->id;
        $row["tagid"] = $tagid;
        return self::createByBiz($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // ====================================
    // ----------- static method ----------
    // ====================================
    public static function getPatientType (Patient $patient) {
        $tagrefs = TagRefDao::getListByObj($patient);

        $types = [
            'shengcunstatus' => 0,
            'zhunbeishoushu' => 0,
            'zhunbeihualiao' => 0
        ];

        $notNeeds = ['新辅助化疗中', '辅助化疗中', '晚期化疗中', '化疗'];

        $shengcunstatus = ['晚期化疗后', '放弃治疗', '中药治疗', '免疫治疗'];
        $zhunbeishoushu = ['准备手术'];
        $zhunbeihualiao = ['新辅助化疗前', '辅助化疗前', '晚期化疗前'];

        foreach ($tagrefs as $tagref) {
            $tagname = $tagref->tag->name;

            if (in_array($tagname, $notNeeds)) {
                Debug::trace("======= not {$tagname}");
                $types = [
                    'shengcunstatus' => 0,
                    'zhunbeishoushu' => 0,
                    'zhunbeihualiao' => 0
                ];

                break;
            }

            if (in_array($tagname, $shengcunstatus)) {
                Debug::trace("======= have {$tagname}");
                $types['shengcunstatus'] = 1;
            }

            if (in_array($tagname, $zhunbeishoushu)) {
                $types['zhunbeishoushu'] = 1;
            }

            if (in_array($tagname, $zhunbeihualiao)) {
                $types['zhunbeihualiao'] = 1;
            }
        }

        return $types;
    }
}
