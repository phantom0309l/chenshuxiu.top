<?php

/*
 * WxUserDao
 */
class WxUserDao extends Dao
{

    public function __construct () {
        parent::__construct('WxUser');
    }

    // 名称: getAllWoy
    // 备注:得到所有的woy
    // 创建:
    // 修改:
    public static function getAllWoy () {
        $sql = "select max(woy) from wxusers";
        $max_woy = Dao::queryValue($sql, []);

        $woys = array();
        for ($i = $max_woy; $i >= 1; $i --) {
            $woys[] = $i;
        }

        return $woys;
    }

    // 名称: getArrayOfRef_objidAndShareCnt
    // 备注:
    // 创建:
    // 修改:
    public static function getArrayOfRef_objidAndShareCnt () {
        $ids = UserDao::getTestUseridsstr();

        $sql = "select ref_objid, count(*) as cnt
            from wxusers
            where ref_pcode = 'Share[Fbt]' and ref_objtype='User' and ref_objid not in ({$ids})
            group by ref_objid
            order by cnt desc ";

        return Dao::queryRows($sql, []);
    }

    // 名称: getByOpenid
    public static function getByOpenid ($openid) {
        $cond = " and openid = :openid ";

        $bind = [];
        $bind[':openid'] = $openid;

        return Dao::getEntityByCond('WxUser', $cond, $bind);
    }

    // 名称: getCntByDate
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByDate ($fromdate, $todate, $scan = null, $baodao = null) {
        $sql = "select count(*) as cnt
            from wxusers a
            inner join users b on a.userid = b.id
            where a.wxshopid = 1 and (b.id < 10000 or b.id > 20000)";

        $bind = [];

        if (! is_null($scan)) {
            if ($scan) {
                $sql .= " and a.wx_ref_code <> '' ";
            } else {
                $sql .= " and a.wx_ref_code = '' ";
            }
        }

        if (! is_null($baodao)) {
            if ($baodao) {
                $sql .= " and b.patientid > 0 ";
            } else {
                $sql .= " and b.patientid = 0 ";
            }
        }

        if ($fromdate) {
            $sql .= " and a.createtime >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate) {
            $sql .= " and a.createtime < :todate ";
            $bind[':todate'] = $todate;
        }

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getCntByRefobj3
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByRefobj3 ($refpcode, $refobjtype, $refobjid) {
        $sql = "select count(*) as cnt
            from wxusers
            where wxshopid=3 and subscribe=1
                and ref_pcode = :ref_pcode and ref_objtype = :ref_objtype and ref_objid = :ref_objid ";

        $bind = [];
        $bind[':ref_pcode'] = $refpcode;
        $bind[':ref_objtype'] = $refobjtype;
        $bind[':ref_objid'] = $refobjid;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: getListByDate
    // 备注:
    // 创建:
    // 修改:
    public static function getListByDate ($pagesize = 20, $pagenum = 1, $fromdate, $todate, $scan = null, $baodao = null) {
        $bind = [];

        $sql = "select a.*
            from wxusers a
            inner join users b on a.userid = b.id
            where a.wxshopid = 1 and (b.id < 10000 or b.id > 20000)";

        if (! is_null($scan)) {
            if ($scan) {
                $sql .= " and a.wx_ref_code <> '' ";
            } else {
                $sql .= " and a.wx_ref_code = '' ";
            }
        }

        if (! is_null($baodao)) {
            if ($baodao) {
                $sql .= " and b.patientid > 0 ";
            } else {
                $sql .= " and b.patientid = 0 ";
            }
        }

        if ($fromdate) {
            $sql .= " and a.createtime >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
        }

        if ($todate) {
            $sql .= " and a.createtime < :todate ";
            $bind[':todate'] = $todate;
        }
        $sql .= " order by a.createtime desc";

        return Dao::loadEntityList4Page("WxUser", $sql, $pagesize, $pagenum, $bind);
    }

    // 名称: getListByPatient
    // 备注: 基于patientid字段添加
    // 修改:
    public static function getListByPatient (Patient $patient) {
        $bind = [];
        $cond = " and patientid = :patientid order by subscribe desc, wxshopid asc, id asc ";
        $bind[':patientid'] = $patient->id;

        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: getListByPatient
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPatient_bak (Patient $patient) {
        $sql = "select w.*
            from wxusers w
            inner join users u on u.id = w.userid
            where u.patientid = :patientid";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        return Dao::loadEntityList('WxUser', $sql, $bind);
    }

    // 名称: getListByPcard
    // 备注:
    // 创建:
    // 修改:
    public static function getListByPcard (Pcard $pcard) {
        $sql = "select distinct a.*
            from wxusers a
            inner join users b on b.id=a.userid
            inner join doctorwxshoprefs c on c.wxshopid=a.wxshopid
            where b.patientid = :patientid and c.doctorid = :doctorid
            order by a.subscribe desc, a.wxshopid asc, a.id asc;";

        $bind = [];
        $bind[':patientid'] = $pcard->patientid;
        $bind[':doctorid'] = $pcard->doctorid;

        return Dao::loadEntityList('WxUser', $sql, $bind);
    }

    // 名称: getListByRefobj3
    // 备注:
    // 创建:
    // 修改:
    public static function getListByRefobj3 ($refpcode, $refobjtype, $refobjid) {
        $cond = " and ref_pcode = :refpcode and ref_objtype = :refobjtype and ref_objid = :refobjid
            order by id desc";

        $bind = [];
        $bind[':refpcode'] = $refpcode;
        $bind[':refobjtype'] = $refobjtype;
        $bind[':refobjid'] = $refobjid;
        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: getListByUnionId
    // 备注:
    // 创建:
    // 修改:
    public static function getListByUnionId ($unionid) {
        if (! $unionid) {
            return array();
        }
        $cond = " and unionid = :unionid";

        $bind = [];
        $bind[":unionid"] = $unionid;

        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: getListByUserId
    // 备注: 排序规则比较重要
    // 修改: 修改排序规则 by sjp 2017-03-02
    public static function getListByUserId ($userid) {
        $bind = [];
        $cond = " and userid = :userid order by subscribe desc, wxshopid asc, id asc ";
        $bind[':userid'] = $userid;

        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: getListByUserIdAndWxShopId
    //此处一个user下取多个wxuser是由于方寸管理端一个user会绑定多个wxuser
    public static function getListByUserIdAndWxShopId ($userid, $wxshopid) {
        $bind = [];

        $cond = " and userid = :userid ";
        $bind[':userid'] = $userid;

        $cond .= " and wxshopid = :wxshopid ";
        $bind[':wxshopid'] = $wxshopid;

        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: getListByWxshopid_Ops
    // 备注: 给运营发送监控消息
    public static function getListByWxshopid_Ops ($wxshopid) {
        $cond = 'and wxshopid=:wxshopid and is_ops=1 ';
        $bind = [];
        $bind[':wxshopid'] = $wxshopid;

        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: get_ref_objid_cnt_RowsForDY
    // 备注: 取方寸课堂里做了代言的用户
    // 创建:
    // 修改:
    public static function get_ref_objid_cnt_RowsForDY () {
        $sql = "select count(*) as cnt, ref_objid
            from wxusers
            where wxshopid=3 and ref_pcode='Share[DY]' and createtime < date('2016-03-04')
            group by ref_objid
            order by cnt desc";

        return Dao::queryRows($sql, []);
    }

    // 名称: getOpsWxusertListBySomeParams
    // 备注: 根据条件查运营的wxuser列表, 用以自动bindUser
    // 创建: txj
    // 修改: by sjp
    public static function getOpsWxusertListBySomeParams ($nickname, $sex, $province, $city) {
        $cond = " AND userid > 10000 AND userid < 20000 ";
        $cond .= " AND nickname = :nickname AND sex = :sex AND province = :province AND city = :city ";

        $bind = [];
        $bind[':nickname'] = $nickname;
        $bind[':sex'] = $sex;
        $bind[':province'] = $province;
        $bind[':city'] = $city;

        return Dao::getEntityListByCond('WxUser', $cond, $bind);
    }

    // 名称: getMasterWxUserByPatientId
    // 创建: by sjp 2017-03-01
    // 通过患者尽量找一个有效的wxuser,排序很重要
    // subscribe desc 优先订阅的
    // wxshopid asc 是考虑方寸儿童管理服务平台和方寸课堂的情况
    // 参数 wxshopid 应该一般不需要
    public static function getMasterWxUserByPatientId ($patientid, $wxshopid = 0) {
        $cond = " and u.patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patientid;

        // 一般不需要
        if ($wxshopid > 0) {
            $cond .= " and w.wxshopid = :wxshopid ";
            $bind[':wxshopid'] = $wxshopid;
        }

        $sql = "select w.*
            from wxusers w
            inner join users u on u.id = w.userid
            where 1=1 $cond
            order by w.subscribe desc, w.wxshopid asc, w.id asc";

        return Dao::loadEntity('WxUser', $sql, $bind);
    }

    // 名称: getMasterWxUserByUserId
    public static function getMasterWxUserByUserId ($userid, $wxshopid = 0) {

        // 优先取 doctorid > 0
        $wxuser = self::getMasterWxUserByUserIdImp($userid, $wxshopid, true);

        // 取不到, 再随便取一个
        if (false == $wxuser instanceof WxUser) {
            Debug::warn("WxUser::getMasterWxUserByUserIdImp({$userid}, {$wxshopid}, true) : not found wxuser ");
            $wxuser = self::getMasterWxUserByUserIdImp($userid, $wxshopid, false);
        }

        return $wxuser;
    }

    // 名称: getMasterWxUserByUserIdImp
    private static function getMasterWxUserByUserIdImp ($userid, $wxshopid = 0, $mustBindDoctor = true) {
        $bind = [];

        $cond = "";
        if ($mustBindDoctor) {
            $cond .= " and doctorid > 0 ";
        }

        $cond = " and userid = :userid ";
        $bind[':userid'] = $userid;

        // 一般不需要
        if ($wxshopid > 0) {
            $cond .= " and wxshopid = :wxshopid ";
            $bind[':wxshopid'] = $wxshopid;
        }

        $cond .= " order by subscribe desc, wxshopid asc, id asc ";

        return Dao::getEntityByCond('WxUser', $cond, $bind);
    }

    // 名称: getByUserIdAndWxShopId
    public static function getByUserIdAndWxShopId ($userid, $wxshopid) {
        $bind = [];

        $cond = " and userid = :userid ";
        $bind[':userid'] = $userid;

        $cond .= " and wxshopid = :wxshopid order by id desc ";
        $bind[':wxshopid'] = $wxshopid;

        return Dao::getEntityByCond('WxUser', $cond, $bind);
    }

    // 名称: getNotBaodaoByDoctorid
    // 备注: 根据医生和月份拿到这个月，这个医生当月关注但未报到的患者列表
    // 创建:
    // 修改: 修改为bind 模式 by lijie 2017-03-06
    public static function getNotBaodaoByDoctorid ($doctorid, $themonth) {
        $bind = [];

        $cond = " and doctorid = :doctorid and left(a.createtime, 7) = :themonth ";
        $bind[':doctorid'] = $doctorid;
        $bind[':themonth'] = "{$themonth}";

        $sql = "select a.*
            from wxusers a
            inner join users b on a.userid = b.id
            where b.patientid = 0 " . $cond;

        return Dao::loadEntityList("WxUser", $sql, $bind);
    }

    // 名称: getNotBaodaoByMonth
    // 备注: 根据市场人员和月份拿到这个月，这个市场人员当月关注但未报到的患者列表
    // 创建:
    // 修改: 修改为bind 模式 by lijie 2017-03-06
    public static function getNotBaodaoByMonth ($auditorid, $themonth) {
        $bind = [];

        $cond = " and left(a.createtime, 7) = :themonth ";
        $bind[':themonth'] = "{$themonth}";

        if ($auditorid != 0) {
            $cond .= " and c.auditorid_market = :auditorid_market ";
            $bind[':auditorid_market'] = $auditorid;
        }

        $sql = "select a.*
            from wxusers a
            inner join users b on a.userid = b.id
            inner join doctors c on a.doctorid = c.id
            where b.patientid = 0 " . $cond;

        return Dao::loadEntityList("WxUser", $sql, $bind);
    }

    // 名称: getRptGroupbyDoctorMonth
    // 备注: 获取按医生和月份分组汇总的报表
    // 创建:
    // 修改:
    public static function getRptGroupbyDoctorMonth () {
        $sql = "select doctorid, left(createtime,7) as themonth , count(*) as cnt
            from wxusers
            where wxshopid != 3 and doctorid > 0 and subscribe = 1
            group by doctorid,left(createtime,7)
            order by doctorid,createtime desc";

        return Dao::queryRows($sql, []);
    }

    // 名称: getRptGroupbyDoctorWoy
    // 备注: 获取按医生和woy分组汇总的报表
    // 创建:
    // 修改:
    public static function getRptGroupbyDoctorWoy () {
        $sql = "select doctorid, woy , count(*) as cnt
            from wxusers
            group by doctorid, woy
            order by doctorid,woy desc";

        return Dao::queryRows($sql, []);
    }

    // 名称: getSubscribedNum
    // 备注: 根据传入的wxshopid查询出当前公众号的关注总人数
    // 创建:
    // 修改: 修改备注 by lijie 2017-03-06
    public static function getSubscribedNum ($wxshopid) {
        $cond = " and wxshopid = :wxshopid ";

        $bind = [];
        $bind[':wxshopid'] = $wxshopid;

        $sql = "select count(*) as cnt
            from wxusers
            where subscribe=1 " . $cond;

        return Dao::queryValue($sql, $bind);
    }
}
