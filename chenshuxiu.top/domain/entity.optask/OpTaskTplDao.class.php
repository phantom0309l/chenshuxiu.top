<?php

/*
 * OpTaskTplDao
 */
class OpTaskTplDao extends Dao
{

    // 名称: getList
    public static function getList ($condEx = " and status = 1 ") {
        $cond = " {$condEx} ";
        return Dao::getEntityListByCond("OpTaskTpl", $cond);
    }

    // 名称: getListByDiseaseid
    public static function getListByDiseaseid ($diseaseid = 0, $condEx = "") {
        $optasktpls = OpTaskTplDao::getList($condEx);

        $diseaseid = $diseaseid ? $diseaseid : 0;

        $result = array();
        if ($diseaseid > 0) {
            foreach ($optasktpls as $a) {
                $diseaseids = $a->diseaseids;
                $diseaseids_arr = explode(",", $diseaseids);
                if (in_array($diseaseid, $diseaseids_arr) || $diseaseids == 0) {
                    $result[] = $a;
                }
            }
        } else {
            $result = $optasktpls;
        }

        return $result;
    }

    // 名称: getListByAuditorid
    public static function getListByAuditorid ($auditorid, $condEx = "") {
        $sql = "select a.*
            from optasktpls a
            inner join optasktplauditorrefs b on b.optasktplid = a.id
            where b.auditorid = :auditorid {$condEx}";
        $bind = [];
        $bind[":auditorid"] = $auditorid;
        return Dao::loadEntityList("OpTaskTpl", $sql, $bind);
    }

    // 名称: getList_ADHD
    public static function getList_ADHD ($condEx = " and status = 1 ") {
        $optasktpls = OpTaskTplDao::getList($condEx);

        $result = array();
        foreach ($optasktpls as $a) {
            $diseaseids = $a->diseaseids;
            if ($diseaseids == 1 || $diseaseids == 0) {
                $result[] = $a;
            }
        }
        return $result;
    }

    // 名称: getList_NotADHD
    public static function getList_NotADHD ($condEx = " and status = 1 ") {
        $optasktpls = OpTaskTplDao::getList($condEx);

        $result = array();
        foreach ($optasktpls as $a) {
            $diseaseids = $a->diseaseids;
            if ($diseaseids != 1) {
                $result[] = $a;
            }
        }
        return $result;
    }

    // 对外接口: 获取任务模板 getOneByUnicode
    public static function getOneByUnicode ($unicode) {
        $unicode = trim($unicode);

        $code = '';
        $subcode = '';

        $arr = explode(':', $unicode);

        if (count($arr) == 2) {
            $code = trim(array_shift($arr));
            $subcode = trim(array_shift($arr));
        } else {
            Debug::warn("OpTaskTplDao::getOneByUnicode('{$unicode}') : unicode error ");
        }

        // Code + Subcode
        $optasktpl = OpTaskTplDao::getOneByCodeSubcode($code, $subcode);

        // Code
        if (empty($optasktpl) && empty($subcode) && $code) {
            Debug::trace("OpTaskTpl[{$unicode}] not found 1.");
            $optasktpl = OpTaskTplDao::getOneByCodeSubcode($code, '');
        }

        // 实在查不到
        if (false == $optasktpl instanceof OpTaskTpl) {
            Debug::warn("OpTaskTpl[{$unicode}] not found 2.");
        }

        return $optasktpl;
    }

    // Code + Subcode
    private static function getOneByCodeSubcode ($code, $subcode) {
        $cond = " and code = :code ";

        $bind = [];
        $bind[":code"] = $code;

        if ($subcode) {
            $cond .= " and subcode = :subcode ";
            $bind[":subcode"] = $subcode;
        }

        return Dao::getEntityByCond("OpTaskTpl", $cond, $bind);
    }
}
