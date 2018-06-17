<?php
/*
 * PaperDao
 */
class PaperDao extends Dao
{

    public static function getByPaperTplObjtypeObjid (PaperTpl $papertpl, $objtype, $objid) {
        $cond = " and papertplid = :papertplid and objtype = :objtype and objid = :objid ";
        $bind = [
            ':papertplid' => $papertpl->id,
            ':objtype' => $objtype,
            ':objid' => $objid
        ];

        return Dao::getEntityByCond('Paper', $cond, $bind);
    }

    // 名称: getList_adhd_iv
    // 备注:获取渲染SNAP-IV评估图表的数据
    // 创建:
    // 修改:by lijie 2016-09-09 09:38
    public static function getList_adhd_iv ($patientid, $num, $writer = "all") {

        $num = intval($num);

        $cond = "";
        $bind = [];

        if ($writer == 'all') {
            $cond = "";
        } else {
            $name = Paper::getWriterByStr($writer);
            $cond = " and writer = :writer ";
            $bind[':writer'] = $name;
        }

        $cond .= " and ename = 'adhd_iv' and patientid = :patientid ";
        $cond .= " order by id desc limit {$num}";

        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    // 名称: getAnswerSheetListByDay
    // 备注:按天获取量表 dapi下有一处调用点，删除dapi后可删 20170531 by 许喆
    // 创建:
    // 修改:
    public static function getAnswerSheetListByDay ($patientid) {
        $cond = "and patientid = :patientid and groupstr = 'scale' order by id desc";
        $bind = [];
        $bind[':patientid'] = $patientid;

        $papers = Dao::getEntityListByCond("Paper", $cond, $bind);

        $result = array();
        $arr = array();
        $temp = array();
        foreach ($papers as $a) {
            $ename = $a->ename;
            $dateStr = date('Y-m-d', strtotime($a->createtime));
            if (empty($arr[$dateStr])) {
                $arr[$dateStr] = array();
                $temp[$dateStr] = array();
            }
            if (empty($arr[$dateStr][$ename])) {
                $arr[$dateStr][$ename] = $a;

                $qsheetNames = self::getQSheetNameArray();
                $title = $qsheetNames[$ename];
                $remark = $a->getRemarkByQsheetName();
                $url = UrlFor::dmAppAnswerSheet($a->xanswersheetid);
                $temp[$dateStr][] = array(
                    "title" => $title,
                    "remark" => $remark,
                    "url" => $url);
            }
        }
        foreach ($temp as $k => $v) {
            $result[] = array(
                'date' => $k,
                'list' => $v);
        }
        return $result;
    }

    // 名称: getByXAnswerSheetId
    // 备注:
    // 创建:
    // 修改:
    public static function getByXAnswerSheetId ($xanswersheetid) {
        $cond = "AND xanswersheetid = :xanswersheetid ";
        $bind = [];
        $bind[':xanswersheetid'] = $xanswersheetid;

        return Dao::getEntityByCond("Paper", $cond, $bind);
    }

    // 名称: getByDayRange
    // 备注:获取某个时间区间患者所做的量表[)半闭半开区间
    // 创建:
    // 修改:
    public static function getByDayRange ($patientid, $startDate, $endDate) {
        $cond = "AND groupstr = 'scale' AND patientid = :patientid AND createtime > date('{$startDate}') AND createtime < date('{$endDate}')";
        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    // 名称: getByWxuserGroupstr
    // 备注:
    // 创建:
    // 修改:
    public static function getByWxuserGroupstr ($wxuser, $groupstr) {
        $cond = " AND wxuserid = :wxuserid AND groupstr = :groupstr order by id ";
        $bind = [];
        $bind[':wxuserid'] = $wxuser->id;
        $bind[':groupstr'] = $groupstr;

        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    // 名称: getCntByPaperTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPaperTpl (PaperTpl $paperTpl) {
        $sql = "select count(*) as cnt
            from papers
            where papertplid = :papertplid ";

        $bind = [];
        $bind[':papertplid'] = $paperTpl->id;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByPatientid ($patientid) {
        $sql = "select count(*) as cnt
            from papers
            where patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getLastADHD
    // 备注:取最新一次SNAP-IV评估
    // 创建:
    // 修改:
    public static function getLastADHD ($patientid) {
        $cond = "and patientid = :patientid and ename = :ename order by id desc limit 1 ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':ename'] = "adhd_iv";
        return Dao::getEntityByCond("Paper", $cond, $bind);
    }

    // 名称: getLastADHDList
    // 备注:取最新两次SNAP-IV评估 用于判断上升还是下降趋势
    // 创建:
    // 修改:
    public static function getLastADHDList ($patientid, $cnt) {
        $cond = "and patientid = :patientid and ename = :ename order by id desc limit $cnt ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':ename'] = "adhd_iv";
        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    // 名称: getLastByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByPatientid ($patientid) {
        $cond = "and patientid = :patientid order by id desc limit 1 ";
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::getEntityByCond("Paper", $cond, $bind);
    }

    // 名称: getLastByPatientidPapertplid
    // 备注:
    // 创建:
    // 修改:
    public static function getLastByPatientidPapertplid ($patientid, $papertplid, $condEx = "") {
        $cond = " AND papertplid = :papertplid AND patientid = :patientid {$condEx} order by createtime desc";
        $bind = [];
        $bind[':papertplid'] = $papertplid;
        $bind[':patientid'] = $patientid;
        return Dao::getEntityByCond('Paper', $cond, $bind);
    }

    // 名称: getLastScaleOfPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getLastScaleOfPatient ($patientid, $ename = "") {
        $cond = " AND patientid = :patientid AND groupstr = 'scale'";
        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($ename) {
            $cond .= " AND ename = :ename ";
            $bind[':ename'] = $ename;
        }

        $cond .= " order by id desc limit 1";

        return Dao::getEntityByCond("Paper", $cond, $bind);
    }

    // 名称: getListByPatientid
    // 备注: 由调用点保证 $condEx 正确性
    // 创建:
    // 修改:
    public static function getListByPatientid ($patientid, $condEx = "") {
        $cond = " AND patientid = :patientid {$condEx}";
        $bind = [];
        $bind[':patientid'] = $patientid;
        return Dao::getEntityListByCond('Paper', $cond, $bind);
    }

    // 名称: getListByPaperTpl
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPaperTpl (PaperTpl $paperTpl, $cnt = 200) {
        $cnt = intval($cnt);

        $cond = " AND papertplid = :papertplid order by id desc limit {$cnt} ";
        $bind = [];
        $bind[':papertplid'] = $paperTpl->id;
        return Dao::getEntityListByCond('Paper', $cond, $bind);
    }

    // 名称: getMissMedicineList
    // 备注:获取漏服药列表
    // 创建:
    // 修改:
    public static function getMissMedicineList ($patientid, $num) {
        $cond = "and ename = 'medicine_parent' and patientid = :patientid order by id desc";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $papers = Dao::getEntityListByCond("Paper", $cond, $bind);

        $i = 1;
        $arr = array();
        foreach ($papers as $a) {
            if ($i > $num) {
                break;
            }
            $result = $a->getMissedMedicine();
            if (! empty($result["name"]) && ! empty($result["day"])) {
                $arr[] = $result;
                $i ++;
            }
        }
        return $arr;
    }

    // 名称: getQSheetNameArray
    // 备注:
    // 创建:
    // 修改:
    public static function getQSheetNameArray () {
        return array(
            "adhd_iv" => "SNAP-IV评估",
            "medicine_parent" => "疗效评估",
            "sideeffect" => "副反应评估",
            "conners" => "Conners父母用量表");
    }

    // 名称: getScaleListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getScaleListByPatient ($patientid, $ename = "") {
        $cond = " AND patientid = :patientid AND groupstr = 'scale' ";
        $bind = [];
        $bind[':patientid'] = $patientid;

        if ($ename) {
            $cond .= " AND ename = :ename ";
            $bind[':ename'] = $ename;
        }

        $cond .= " order by id ";

        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    // 名称: getWritersByPatientid
    // 备注:
    // 创建:
    // 修改:
    public static function getWritersByPatientid ($patientid, $needall = false) {
        $sql = "SELECT DISTINCT writer
            FROM papers
            WHERE patientid = :patientid AND ename = 'adhd_iv' ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        $writers = Dao::queryRows($sql, $bind);

        $arr = array();

        foreach ($writers as $k => $v) {
            if ($v[writer] != '') {
                $arr[] = $v[writer];
            }
        }
        if ($needall) {
            $arr[] = '全部';
        }

        return $arr;
    }

    // 名称: getBedtktListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatientidPaperTplId ($patientid, $papertplid) {
        $cond = " AND patientid = :patientid AND papertplid = :papertplid  ORDER BY id ASC";
        $bind = [];
        $bind[':patientid'] = $patientid;
        $bind[':papertplid'] = $papertplid;

        return Dao::getEntityListByCond("Paper", $cond, $bind);
    }

    // 名称: getAEPCCntByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getAEPCCntByPatient (Patient $patient) {
        $cond = " and papertplid in (275143816, 275209326, 312586776)";
        $bind = [];

        $cond .= ' and patientid=:patientid ';
        $bind[':patientid'] = $patient->id;

        $sql = "select count(*) from papers where 1=1 {$cond}";
        return Dao::queryValue($sql, $bind) + 0;
    }

    // 名称: getAEPCByPatientAscOrderById
    // 备注:
    // 创建:
    // 修改:
    public static function getAEPCByPatientAscOrderById (Patient $patient, $asc = true) {
        $cond = " AND patientid = :patientid AND papertplid in (275143816, 275209326, 312586776) ";
        $bind = [];
        $bind[':patientid'] = $patient->id;

        if($asc){
            $cond .= " ORDER BY id ASC ";
        }else{
            $cond .= " ORDER BY id DESC ";
        }

        return Dao::getEntityByCond("Paper", $cond, $bind);
    }
}
