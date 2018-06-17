<?php

/*
 * DealwithTplDao
 */

class DealwithTplDao extends Dao
{
    // 名称: getListByTypestr
    // 备注:
    // 创建: by txj
    // 修改: by txj
    public static function getListByTypestr($typestr, $condEx = "") {
        $cond = "and typestr = :typestr {$condEx}";

        $bind = [];
        $bind[":typestr"] = $typestr;
        return Dao::getEntityListByCond("DealwithTpl", $cond, $bind);
    }

    // 名称: getListByEname
    // 备注:
    // 创建: by txj
    // 修改: by txj
    public static function getListByEname($ename) {
        $cond = "and ename = :ename";

        $bind = [];
        $bind[":ename"] = $ename;

        return Dao::getEntityListByCond("DealwithTpl", $cond, $bind);
    }

    // 名称: getListByObjid
    // 备注: 这个sql有点奇怪 by sjp
    // 创建: by txj
    // 修改: by txj
    public static function getListByObjid($objid) {
        $cond = "  and objid = :objid order by sendcnt desc ";

        $bind = [];
        $bind[":objid"] = $objid;

        return Dao::getEntityListByCond("DealwithTpl", $cond, $bind);
    }

    // //////////////////////////

    // getDealwithTplListByDiseasegroupid , 全局, 通用模板
    public static function getDealwithTplListByCommon() {
        $cond = "and diseasegroupid=0 and diseaseid = 0 and doctorid = 0 ";
        $cond .= " order by diseasegroupid, diseaseid, doctorid, groupstr, title  ";
        $bind = [];

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }

    // getDealwithTplListByDiseasegroupid , 疾病组, 通用模板
    public static function getDealwithTplListByDiseasegroupid($diseasegroupid = 0) {
        $cond = "and diseasegroupid=:diseasegroupid and diseaseid = 0 and doctorid = 0 ";
        $cond .= " order by groupstr, title ";

        $bind = [];
        $bind[':diseasegroupid'] = $diseasegroupid;

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }

    // getDealwithTplListByDiseasegroupid , 疾病组, 通用模板
    public static function getDealwithTplListByDiseasegroupidAndCommon($diseasegroupid = 0) {
        $cond = "and ( diseasegroupid=:diseasegroupid or diseasegroupid=0 ) and groupstr!='' ";
        $cond .= " order by title ";

        $bind = [];
        $bind[':diseasegroupid'] = $diseasegroupid;

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }

    // getDealwithTplListByDiseaseid , 疾病, 模板
    public static function getDealwithTplListByDiseaseid($diseaseid = 0) {
        $cond = "";
        $bind = [];
        $cond = "and diseaseid=:diseaseid ";
        $cond .= " order by groupstr, title  ";
        $bind[':diseaseid'] = $diseaseid;

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }

    // getDealwithTplListByDoctorid , 医生, 模板
    public static function getDealwithTplListByDoctorid($doctorid = 0) {
        $cond = "";
        $bind = [];
        $cond = "and doctorid=:doctorid ";
        $cond .= " order by groupstr, title  ";
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }

    // getGroupstrArray 分组名数组
    public static function getGroupstrArray($diseasegroupid = 0) {
        $cond ='';
        $bind =[];

        if($diseasegroupid){
            $cond .= " and diseasegroupid = :diseasegroupid ";
            $bind[':diseasegroupid'] =$diseasegroupid;
        }

        $sql = "select groupstr
                from dealwithtpls
                where groupstr<>''
                {$cond}
                group by groupstr
                order by groupstr";

        $arr = Dao::queryValues($sql, $bind);

        if(0 == $diseasegroupid){
            $arr[] = '未分组';
        }

        return $arr;
    }

    // getDealwithTplListByGroupstr , 组内+分组模板
    public static function getDealwithTplListByGroupstr($groupstr = '', $diseasegroupid = 0) {
        if ($groupstr == '未分组') {
            $groupstr = '';
        }

        //多动症目前不用通用的
        if (2 == $diseasegroupid) {
            $cond = "and groupstr=:groupstr and diseasegroupid=:diseasegroupid ";
        } else {
            $cond = "and groupstr=:groupstr and (diseasegroupid=0 or diseasegroupid=:diseasegroupid) ";
        }

        $bind = [];
        $bind[':diseasegroupid'] = $diseasegroupid;
        $bind[':groupstr'] = $groupstr;

        $cond .= " order by title  ";

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }

    // getDealwithTplListByObjtypeObjidObjcode , 多动症
    public static function getDealwithTplListByObjtypeObjidObjcode($objtype = '', $objid = 0, $objcode = '') {
        $cond = "";
        $bind = [];
        if ($objtype) {
            $cond = "and objtype=:objtype ";
            $bind[':objtype'] = $objtype;
        }

        if ($objid) {
            $cond = "and objid=:objid ";
            $bind[':objid'] = $objid;
        }

        if ($objcode) {
            $cond = "and objcode=:objcode ";
            $bind[':objcode'] = $objcode;
        }

        $cond .= " order by diseasegroupid, diseaseid, typestr, objtype, objid, title  ";

        return Dao::getEntityListByCond('DealwithTpl', $cond, $bind);
    }
}
