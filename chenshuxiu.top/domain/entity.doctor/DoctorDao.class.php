<?php

/*
 * DoctorDao
 */
class DoctorDao extends Dao
{

    // 名称: getByCode
    // 备注: 二维码模糊搜索
    // 创建:
    // 修改:
    public static function getByCode ($str = '') {
        if (empty($str)) {
            return null;
        }

        $str = explode(":", $str);
        $str = $str[0];

        $bind = [];
        $cond = " AND code = :str order by id ";
        $bind[':str'] = $str;

        return Dao::getEntityByCond("Doctor", $cond, $bind);
    }

    // 名称: getListByName
    // 备注:
    // 创建:
    // 修改:
    public static function getListByName ($name = '史老师') {
        $cond = " and name=:name ";
        $bind = [];
        $bind[':name'] = $name;

        return Dao::getEntityListByCond("Doctor", $cond, $bind);
    }

    // 疾病组下的医生
    public static function getListByDiseasegroupid ($diseasegroupid) {
        $sql = "select distinct a.*
            from doctors a
            inner join doctordiseaserefs b on b.doctorid = a.id
            inner join diseases c on c.id = b.diseaseid
            where c.diseasegroupid = :diseasegroupid
            order by a.id ";

        $bind = [];
        $bind[':diseasegroupid'] = $diseasegroupid;

        return Dao::loadEntityList('Doctor', $sql, $bind);
    }

    // 名称: getByName
    // 备注:
    // 创建:
    // 修改:
    public static function getByName ($name = '史老师') {
        $cond = " and name=:name ";
        $bind = [];
        $bind[':name'] = $name;

        return Dao::getEntityByCond("Doctor", $cond, $bind);
    }

    // 名称: getByUserid
    // 备注:
    // 创建:
    // 修改:
    public static function getByUserid ($userid) {
        $cond = " and userid=:userid ";
        $bind = [];
        $bind[':userid'] = $userid;

        return Dao::getEntityByCond("Doctor", $cond, $bind);
    }

    public static function getListByAuditor (Auditor $auditor) {
        $cond = " and auditorid_yunying=:auditorid_yunying ";
        $bind = [];
        $bind[':auditorid_yunying'] = $auditor->id;

        return Dao::getEntityListByCond("Doctor", $cond, $bind);
    }

    // 名称: getCntOfHospital
    // 备注:
    // 创建:
    // 修改:
    public static function getCntOfHospital ($hospitalid, $isneedvalid = false) {
        $bind = [];
        $sql = "select count(*) as cnt from doctors where hospitalid = :hospitalid ";
        $bind[':hospitalid'] = $hospitalid;

        if ($isneedvalid) {
            $sql .= ' and status=1 ';
        }

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getDealPrefWoy
    // 备注:处理原始数组（woy）getDealPrefWoy
    // 创建:
    // 修改:
    public static function getDealPrefWoy (array $arr) {
        $doctorpref = array();
        $woys = WxUserDao::getAllWoy();

        $doctorid_woy_cnt_array = array();
        foreach ($arr as $a) {
            $doctorid = $a['doctorid'];
            $woy = $a['woy'];
            $cnt = $a['cnt'];

            if (false == isset($doctorid_woy_cnt_array[$doctorid])) {
                $doctorid_woy_cnt_array[$doctorid] = array();
            }

            $doctorid_woy_cnt_array[$doctorid][$woy] = $cnt;
        }

        $doctorid_woy_cnt_array_new = array();
        foreach ($doctorid_woy_cnt_array as $doctorid => $a) {
            foreach ($woys as $w) {
                if (false == isset($doctorid_woy_cnt_array_new[$doctorid])) {
                    $doctorid_woy_cnt_array_new[$doctorid] = array();
                }
                if (false == isset($a[$w])) {
                    $doctorid_woy_cnt_array_new[$doctorid][$w] = 0;
                } else {
                    $doctorid_woy_cnt_array_new[$doctorid][$w] = $a[$w];
                }
            }
        }

        // print_r($doctorid_woy_cnt_array_new);exit;

        return $doctorid_woy_cnt_array_new;
    }

    // 名称: getListByDiseaseid
    // 备注:getListByDiseaseid doctordiseaserefs
    // 创建:
    // 修改:
    public static function getListByDiseaseid ($diseaseid = 0, $isneedvalid = false) {
        $sql = "select distinct a.*
                from doctors a
                inner join doctordiseaserefs b on b.doctorid=a.id
                where 1=1 ";

        $bind = [];
        if ($diseaseid > 0) {
            $sql .= " AND b.diseaseid = :diseaseid ";
            $bind[':diseaseid'] = $diseaseid;
        }

        if ($isneedvalid) {
            $sql .= ' AND a.status=1 ';
        }

        $sql .= " order by a.id ";

        return Dao::loadEntityList("Doctor", $sql, $bind);
    }

    // 名称: getListByHospital
    // 备注:
    // 创建:
    // 修改:
    public static function getListByHospital ($hospitalid, $isneedvalid = false) {
        $bind = [];
        $cond = " AND hospitalid = :hospitalid order by id ";
        $bind[':hospitalid'] = $hospitalid;

        if ($isneedvalid) {
            $cond .= ' and status=1 ';
        }

        return Dao::getEntityListByCond("Doctor", $cond, $bind);
    }

    // 名称: getListByAuditorid_market
    // 备注:
    // 创建:
    // 修改:
    public static function getListByAuditorid_market ($auditorid_market, $isneedvalid = false) {
        $bind = [];
        $cond = " AND auditorid_market = :auditorid_market order by id ";
        $bind[':auditorid_market'] = $auditorid_market;

        if ($isneedvalid) {
            $cond .= ' and status=1 ';
        }

        return Dao::getEntityListByCond("Doctor", $cond, $bind);
    }

    // 名称: getMonthPrefs
    // 备注:取前几个月的报到/扫码数 的总计
    // 创建:
    // 修改:
    public static function getMonthPrefs ($rpt, $doctors) {
        $ids = array();
        foreach ($doctors as $a) {
            $ids[] = $a->id;
        }

        $arr = array();
        foreach ($rpt as $doctorid => $month_cnt_array) {

            if (false == in_array($doctorid, $ids)) {
                continue;
            }

            foreach ($month_cnt_array as $month => $cnt) {
                $arr[$month] += $cnt;
            }
        }
        return $arr;
    }

    // 名称: getRptMonths
    // 备注:得到当前日期和前5个月日期还有 以前，总计
    // 创建:
    // 修改:
    public static function getRptMonths ($month_cnt = 3, $lastmonths = false) {
        $ym = date('Y-m', time());

        if ($lastmonths) {
            $ym = date("Y-m", strtotime("last month", strtotime($ym)));
        }
        list ($y, $m) = explode('-', $ym);

        $months = array();
        for ($i = 0; $i < $month_cnt; $i ++) {
            $month = $m - $i;
            if ($month > 0) {
                if ($month < 10) {
                    $month = "0" . $month;
                }
                $months[$i] = $y . "-" . $month;
            } else {
                $month += 12;
                if ($month < 10) {
                    $month = "0" . $month;
                }
                $year = $y - 1;
                $months[$i] = $year . "-" . $month;
            }
        }
        $months[$month_cnt] = "beforecnt";
        $months[$month_cnt + 1] = "allcnt";

        return $months;
    }

    // 名称: getWoyPrefs
    // 备注:获取每一个woy的报到/扫码数 的总计
    // 创建:
    // 修改:
    public static function getWoyPrefs ($rpt, $doctors) {
        $ids = array();
        foreach ($doctors as $a) {
            $ids[] = $a->id;
        }

        $arr = array();
        foreach ($rpt as $doctorid => $woy_cnt_array) {

            if (false == in_array($doctorid, $ids)) {
                continue;
            }

            foreach ($woy_cnt_array as $woy => $cnt) {
                $arr[$woy] += $cnt;
            }
        }
        return $arr;
    }
}
