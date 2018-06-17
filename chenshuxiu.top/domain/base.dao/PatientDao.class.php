<?php

/*
 * PatientDao
 */

class PatientDao extends Dao
{
    // 名称: 市场统计：根据市场人员id与月份得到患者实体
    // 备注:
    // 创建:
    // 修改:
    public static function getBaodaoByMonth($auditorid, $themonth) {
        $patientids_company = PatientDao::getIdsOfCompany();
        $testPatientidStr = implode(",", $patientids_company);
        $cond = '';
        $bind = [];
        if ($auditorid != 0) {
            $cond .= " AND b.auditorid_market = :auditorid_market ";
            $bind[':auditorid_market'] = $auditorid;
        }

        $sql = "SELECT distinct a.*
            FROM patients a
            inner join pcards x on x.patientid = a.id
            inner join doctors b ON b.id = x.doctorid
            WHERE a.status = 1 $cond AND left(a.createtime, 7) = :themonth AND x.diseaseid = 1 AND a.id not in ({$testPatientidStr})";

        $bind[':themonth'] = $themonth;

        return Dao::loadEntityList("Patient", $sql, $bind);
    }

    // 名称: 根据两个日期，取出区间报到的人数(ADHD)
    // 备注:
    // 创建:
    // 修改:
    public static function getBaodaoCntByDate($last_monday, $this_monday) {
        $sql = "select count(*)
                from ( select distinct a.id
                    from patients a
                    inner join users b on a.id = b.patientid
                    inner join wxusers c on c.userid = b.id
                    where a.status=1 and a.subscribe_cnt>0 and c.wxshopid = 1 and (b.id < 10000 or b.id > 20000)
                    and a.createtime >= :last_monday and a.createtime < :this_monday
                    group by a.id) t";

        $bind = [];
        $bind[':last_monday'] = $last_monday;
        $bind[':this_monday'] = $this_monday;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 得到当前医生当前月的报到患者数
    // 备注:
    // 创建:
    // 修改:
    public static function getBaodaoCntByDoctorid($doctorid, $year_month) {
        $sql = " select  count(DISTINCT a.id) as cnt
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b on a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid
            AND LEFT(a.createtime,7) = :year_month
            AND a.status = 1 and a.subscribe_cnt>0 ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':year_month'] = $year_month;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 根据日期查报到患者
    // 备注:
    // 创建:
    // 修改:
    public static function getBaodaoPatientsByTime($doctorid, $begintime, $endtime) {
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':begintime'] = $begintime;
        $bind[':endtime'] = $endtime;

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.createtime > :begintime AND a.createtime < :endtime AND a.status=1 and a.subscribe_cnt>0
            order by a.id";
        return Dao::loadEntityList("Patient", $sql, $bind);
    }

    // 名称: 根据医生id、身份证查询患者
    // 备注:
    // 创建:
    // 修改:
    public static function getByDoctoridAndPrcrid($doctorid, $prcrid) {
        $cond = " and doctorid = :doctorid and prcrid = :prcrid ";
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':prcrid'] = $prcrid;

        $patient = Dao::getEntityByCond('Patient', $cond, $bind);
        return $patient;
    }

    // 名称: 手机号模糊查询
    // 备注:
    // 创建:
    // 修改:
    public static function getByLikeMobile($mobile) {
        $bind = [];
        $cond = " and id = (
            select patientid
            from xpatientindexs
            where type = 'mobile' and word = :word
            limit 1
        ) ";
        $bind[':word'] = $mobile;

        return Dao::getEntityByCond("Patient", $cond, $bind);
    }

    // 名称: 身份证查询患者
    // 备注:
    // 创建:
    // 修改:
    public static function getByPrcrid ($prcrid) {
        $patient = PatientDao::getByPrcridImp(strtolower($prcrid));
        return $patient;
    }

    // 名称: 身份证查询患者
    // 备注:
    // 创建:
    // 修改:
    public static function getByPrcridImp ($prcrid) {
        DBC::requireNotEmpty($prcrid, "身份证号为空");
        $cond = " AND (prcrid = :prcrid OR prcrid = :prcrid2) ";
        $bind = [
            ':prcrid' => strtolower($prcrid),
            ':prcrid2' => strtoupper($prcrid),
        ];

        $patient = Dao::getEntityByCond('Patient', $cond, $bind);
        return $patient;
    }

    // 名称: getByCreateuserid
    // 备注:
    // 创建:
    // 修改:
    public static function getByCreateuserid ($createuserid) {
        $cond = " and createuserid = :createuserid ";
        $bind = [];
        $bind[':createuserid'] = $createuserid;

        $patient = Dao::getEntityByCond('Patient', $cond, $bind);
        return $patient;
    }

    // 名称: 通过name查找患者总数
    // 备注:
    // 创建:
    // 修改:
    public static function getCntByName($name) {
        $sql = "select count(*) as cnt from patients where name = :name ";

        $bind = [];
        $bind[':name'] = $name;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 得到当前医生当前服药患者
    // 备注:
    // 创建:
    // 修改:
    public static function getDoDrugPatientids($doctorid) {
        $sql = " SELECT DISTINCT a.id as patientid
            FROM patients a
            inner join pcards x on x.patientid = a.id
            inner join  patientmedicinerefs b on b.patientid = x.patientid
            WHERE  x.doctorid = :doctorid AND b.medicineid > 0 and x.diseaseid = 1 AND b.status = 1
            AND a.status =1 and a.subscribe_cnt>0  ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::queryValues($sql, $bind);
    }

    // 名称: 得到当前医生当前不服药患者
    // 备注:
    // 创建:
    // 修改:
    public static function getExceptDotDrugPatientids($doctorid) {
        $sql = " SELECT DISTINCT a.id as patientid
            from patients a
            inner join pcards x on x.patientid = a.id
            where x.doctorid = :doctorid AND a.status =1 and a.subscribe_cnt>0 AND a.id NOT IN (
                SELECT DISTINCT a.id as patientid
                from patients a
                inner join pcards x on x.patientid = a.id
                inner join  patientmedicinerefs b  on a.id = b.patientid
                where x.doctorid = :doctorid
                    AND b.medicineid > 0
                    AND x.diseaseid = 1
                    AND b.status = 1
                    AND a.status = 1 and a.subscribe_cnt > 0 ) ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::queryValues($sql, $bind);
    }

    // 名称: 参加培训课的患者
    // 备注:
    // 创建:
    // 修改:
    public static function getFbtPatientsOfDoctor($doctorid, $days = 7) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
            AND a.id in ( SELECT patientid from lessonuserrefs where courseid in (101561077,100839705)  group by patientid ) ";

        $cnt = Dao::queryValue($sql, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.createtime > :fromtime AND a.status=1 and a.subscribe_cnt>0
            AND a.id in (SELECT patientid from lessonuserrefs where courseid in (101561077,100839705) group by patientid )
            order by a.id";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $patients = Dao::loadEntityList("Patient", $sql, $bind);

        return array(
            $patients,
            $cnt);
    }

    // 名称: 得到当前医生当前月的关注患者数
    // 备注:
    // 创建:
    // 修改:
    public static function getGuanzhuCntByDoctorid($doctorid, $year_month) {
        $sql = " select count(distinct unionid) as cnt
                from wxusers
                where doctorid = :doctorid and LEFT(createtime,7) = :year_month ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':year_month'] = $year_month;

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 获取内部人员的测试患者id数组
    // 备注:
    // 创建:
    // 修改:
    public static function getIdsOfCompany() {
        $sql = " SELECT distinct a.patientid
            FROM users a
            inner join auditors b ON a.id = b.userid
            WHERE a.patientid > 0 ";

        return Dao::queryValues($sql, []);
    }

    // 名称: 通过doctorid查找患者列表
    public static function getListByDoctorid($doctorid) {
        $cond = " and doctorid = :doctorid ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        return Dao::getEntityListByCond('Patient', $cond, $bind);
    }

    // 名称: 通过name查找患者列表
    // 备注:
    // 创建:
    // 修改:
    public static function getListByName($name) {
        $cond = " AND name = :name order by id ";

        $bind = [];
        $bind[':name'] = $name;

        return Dao::getEntityListByCond("Patient", $cond, $bind);
    }

    // 名称: 医生的用药患者
    // 备注: 返回值是 patientlist 和 totalcnt
    // 创建: by wgy
    // 修改:
    public static function getListIsDrugOfDoctor($doctorid, $days = 7, $level = 9) {
        $fromtime = XDateTime::getNow()->addDay(-$days)->toShortString();

        $toltalSql = "select count( distinct(a.id) )
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join patientmedicinerefs b on a.id = b.patientid
            where x.doctorid = :doctorid
            AND b.medicineid > 0 and x.diseaseid = 1 AND b.status = 1 AND a.status =1 and a.subscribe_cnt>0
            ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $toltalCnt = Dao::queryValue($toltalSql, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join patientmedicinerefs b on a.id = b.patientid
            where x.doctorid = :doctorid AND b.first_start_date > :fromtime
            AND b.medicineid > 0 and x.diseaseid = 1 AND b.status = 1 AND a.status =1 and a.subscribe_cnt>0
            ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime;

        return array(
            Dao::loadEntityList("Patient", $sql, $bind),
            $toltalCnt);
    }

    // 名称: 通过clone_by_patientid查找患者
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByClone_by_patientid($clone_by_patientid) {
        $cond = " AND clone_by_patientid = :clone_by_patientid";

        $bind = [];
        $bind[':clone_by_patientid'] = $clone_by_patientid;

        return Dao::getEntityByCond("Patient", $cond, $bind);
    }

    // 名称: 通过clone_by_patientid查找患者
    // 备注:
    // 创建:
    // 修改:
    public static function getOneByNameBirthdayMothername($name, $birthday, $mother_name) {
        $cond = " AND name = :name AND birthday = :birthday AND mother_name = :mother_name";

        $bind = [];
        $bind[':name'] = $name;
        $bind[':birthday'] = $birthday;
        $bind[':mother_name'] = $mother_name;

        return Dao::getEntityByCond("Patient", $cond, $bind);
    }

    // 名称: 近一个月全部患者数(不含已删除)
    // 备注:
    // 创建:
    // 修改:
    public static function getPaitentCnt_lastmonths($doctorid, $notest = false) {
        $fromtime = XDateTime::getNow()->addDay(-30);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.createtime > :fromtime AND a.status=1 and a.subscribe_cnt>0 ";
        if ($notest) {
            $sql .= "and a.createuserid > 20000 and a.name not like '%测试%'";
        }

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 近7天全部患者数(不含已删除)
    // 备注:
    // 创建:
    // 修改:
    public static function getPaitentCnt_lastdays($doctorid) {
        $fromtime = XDateTime::getNow()->addDay(-30);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.createtime > :fromtime AND a.status=1 and a.subscribe_cnt>0 ";

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 近7天非扫码报到数
    // 备注:
    // 创建:
    // 修改:
    public static function getPaitentCnt_lastdays_notscan($doctorid) {
        $fromtime = XDateTime::getNow()->addDay(-30);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = "select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :fromtime  AND  c.wx_ref_code='' AND a.status=1 and a.subscribe_cnt>0 ";

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 近7天扫码数
    // 备注:
    // 创建:
    // 修改:
    public static function getPaitentCnt_lastdays_scan($doctorid, $days = 7) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = "select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :fromtime  AND  c.wx_ref_code<>'' ";

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 近7天扫码报到数
    // 备注:
    // 创建:
    // 修改:
    public static function getPaitentCnt_lastdays_scan_baodao($doctorid) {
        $fromtime = XDateTime::getNow()->addDay(-30);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = "select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :fromtime  AND  c.wx_ref_code<>''
            AND a.status=1 and a.subscribe_cnt>0 ";

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 全部报到患者
    // 备注:
    // 创建:
    // 修改:
    public static function getPaitentCntOfDoctor($doctorid, $notest = false) {
        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards x on x.patientid = a.id
            where a.status=1 and a.subscribe_cnt>0 AND x.status=1 AND x.doctorid = :doctorid ";
        if ($notest) {
            $sql .= " and a.createuserid > 20000 and a.name not like '%测试%'";
        }

        return Dao::queryValue($sql, $bind);
    }

    // 名称: 全部患者id
    // 备注: 可传入condfix来满足多种查询
    // 创建:
    // 修改:
    public static function getPatientCnt($condFix = "") {
        $sql = "select count( distinct(a.id) ) as cnt
                from patients a
                inner join pcards b on b.patientid = a.id
                where 1=1 " . $condFix;
        return Dao::queryValue($sql);
    }

    // 名称: 近7天全部患者数(不含已删除) 加了默认时间
    // 备注: for app
    // 创建: by wgy
    // 修改:
    public static function getPatients_lastdays($doctorid, $days = 7) {
        $fromtime = XDateTime::getNow()->addDay(-$days);
        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.createtime > :fromtime AND a.status=1 and a.subscribe_cnt>0
            order by a.id";
        return Dao::loadEntityList("Patient", $sql, $bind);
    }

    // 名称: 近7天填写量表患者
    // 备注:
    // 创建:
    // 修改:
    public static function getPatients_lastdays_answersheet($doctorid, $days = 14) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
                AND a.id in (select patientid from xanswersheets group by patientid )";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sql, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
                AND a.id in (select patientid from xanswersheets where createtime > :fromtime
            group by patientid )";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $patients = Dao::loadEntityList("Patient", $sql, $bind);

        return array(
            $patients,
            $cnt);
    }

    // 名称: 近7天提问患者
    // 备注:
    // 创建:
    // 修改:
    public static function getPatients_lastdays_ask($doctorid, $days = 14) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
                AND a.id in (select patientid from wxtxtmsgs group by patientid )";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sql, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
                AND a.id in (select patientid from wxtxtmsgs
                    where createtime > :fromtime
                    group by patientid)";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $patients = Dao::loadEntityList("Patient", $sql, $bind);
        return array(
            $patients,
            $cnt);
    }

    // 名称: 近7天报到患者(不含已删除) 加了默认时间
    // 备注: for app
    // 创建: by wgy
    // 修改:
    public static function getPatients_lastdays_baodao($doctorid, $days = 7) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0 ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sql, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.createtime > :fromtime AND a.status=1 and a.subscribe_cnt>0
            order by a.id";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $patients = Dao::loadEntityList("Patient", $sql, $bind);

        return array(
            $patients,
            $cnt);
    }

    // 名称: 近7天记日记患者
    // 备注:
    // 创建:
    // 修改:
    public static function getPatients_lastdays_note($doctorid, $days = 14) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $sql = " select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
                AND a.id in (select patientid from patientnotes group by patientid )";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sql, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = :doctorid AND a.status=1 and a.subscribe_cnt>0
                AND a.id in (select patientid from patientnotes where createtime > :fromtime
            group by patientid)";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $patients = Dao::loadEntityList("Patient", $sql, $bind);
        return array(
            $patients,
            $cnt);
    }

    // 名称: 近7天非扫码报到患者
    // 备注:
    // 创建:
    // 修改:
    public static function getPatients_lastdays_notscan($doctorid) {
        $fromtime = XDateTime::getNow()->addDay(-30);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :fromtime
            AND a.status = 1 and a.subscribe_cnt>0 AND  c.wx_ref_code='' ";

        return Dao::loadEntityList("Patient", $sql, $bind);
    }

    // 名称: 近7天扫码患者(不含已删除)
    // 备注:
    // 创建:
    // 修改:
    public static function getPatients_lastdays_scan($doctorid, $days = 7) {
        $fromtime = XDateTime::getNow()->addDay(-$days);

        $sqlcnt = "select count( distinct(a.id) ) as cnt
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid  AND a.status=1 and a.subscribe_cnt>0 AND  c.wx_ref_code<>'' ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;

        $cnt = Dao::queryValue($sqlcnt, $bind);

        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :fromtime
            AND a.status=1 and a.subscribe_cnt>0 AND  c.wx_ref_code<>''
            order by a.id ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();

        return array(
            Dao::loadEntityList("Patient", $sql, $bind),
            $cnt);
    }

    // 名称: 近7天扫码报到患者
    // 备注:
    // 创建:
    // 修改:
    public static function getPatients_lastdays_scan_baodao($doctorid) {
        $fromtime = XDateTime::getNow()->addDay(-30);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':fromtime'] = $fromtime->toShortString();
        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :fromtime
            AND a.status = 1 and a.subscribe_cnt>0 AND  c.wx_ref_code<>'' order by a.id ";

        return Dao::loadEntityList("Patient", $sql, $bind);
    }

    // 名称: 获取按医生和月份分组汇总的报表
    // 备注:
    // 创建:
    // 修改:
    public static function getRptGroupbyDoctorMonth($condEx = "") {
        $sql = "SELECT x.doctorid,LEFT(a.createtime,7) AS themonth,COUNT(distinct a.id) AS cnt
            FROM patients a
            INNER JOIN pcards x ON x.patientid = a.id
            WHERE 1=1 {$condEx}
            GROUP BY x.doctorid,LEFT(a.createtime,7)
            ORDER BY x.doctorid,a.createtime DESC";

        return Dao::queryRows($sql);
    }

    // 名称: 获取按医生和月份分组汇总的 用于市场绩效的报表
    // 备注:
    // 创建:
    // 修改:
    public static function getRptGroupbyDoctorMonthForMarket() {
        $sql = "SELECT tt.first_doctorid AS doctorid, LEFT(tt.createtime,7) AS themonth,COUNT(tt.id) AS cnt
                FROM (SELECT a.id,a.first_doctorid,a.createtime
                    FROM patients a
                    INNER JOIN users b ON b.patientid = a.id
                    INNER JOIN wxusers c ON c.userid = b.id
                    WHERE a.status = 1
                        AND a.doubt_type = 0
                        AND ( c.unsubscribe_time = '0000-00-00 00:00:00' OR c.subscribe_time > c.unsubscribe_time OR (UNIX_TIMESTAMP(c.unsubscribe_time) - UNIX_TIMESTAMP(a.createtime) >= 86400) )
                        AND (b.id < 10000 OR b.id > 20000 )
                        AND c.wx_ref_code != ''
                        AND c.ref_objtype = 'Doctor'
                        AND c.wxshopid = 1
                    GROUP BY a.id
                )tt
                GROUP BY tt.first_doctorid, LEFT(tt.createtime,7)
                ORDER BY tt.first_doctorid, tt.createtime DESC";

        return Dao::queryRows($sql, []);
    }

    // 名称: 获取按医生和woy分组汇总的报表
    // 备注:
    // 创建:
    // 修改:
    public static function getRptGroupByDoctorWoy($isAll = false) {
        $cond = ' AND a.status=1 ';
        if ($isAll) {
            $cond = '';
        }

        $sql = "SELECT x.doctorid as doctorid,a.woy,COUNT(DISTINCT a.id) AS cnt
            FROM patients a
            inner join pcards x on x.patientid = a.id
            WHERE 1=1 $cond
            GROUP BY x.doctorid,a.woy
            ORDER BY x.doctorid,a.woy DESC";

        return Dao::queryRows($sql, []);
    }

    // 名称: 根据两个日期，取出区间扫码报到的患者id(ADHD)
    // 备注:
    // 创建:
    // 修改:
    public static function getScanBaodaoCntByDate($last_monday, $this_monday) {
        $sql = "select a.id
            from patients a
            inner join users b on a.id = b.patientid
            inner join wxusers c on c.userid = b.id
            where a.status=1 and a.subscribe_cnt>0 and c.wxshopid = 1 and (b.id < 10000 or b.id > 20000)
                and a.createtime >= :last_monday and a.createtime < :this_monday
                and c.wx_ref_code !='' group by a.id ";

        $bind = [];
        $bind[':last_monday'] = $last_monday;
        $bind[':this_monday'] = $this_monday;

        return Dao::queryValues($sql, $bind);
    }

    // 名称: 获取扫码患者
    // 备注:
    // 创建:
    // 修改:
    public static function getScanPatients($num) {
        $num = intval($num);

        $sql = "select a.*
            from patients a
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE a.status=1 and a.subscribe_cnt>0 AND  c.wx_ref_code != ''
            order by a.id DESC
            limit {$num}";

        return Dao::loadEntityList("Patient", $sql, []);
    }

    // 名称: 根据日期查扫码患者
    // 备注:
    // 创建:
    // 修改:
    public static function getScanPatientsByTime($doctorid, $begintime, $endtime) {
        $sql = "select distinct a.*
            from patients a
            inner join pcards x on x.patientid = a.id
            inner join users b ON a.id = b.patientid
            inner join wxusers c ON b.id = c.userid
            WHERE x.doctorid = :doctorid AND a.createtime > :begintime
                AND a.createtime < :endtime AND a.status=1 and a.subscribe_cnt>0 AND c.wx_ref_code<>''
            order by a.id ";

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':begintime'] = $begintime;
        $bind[':endtime'] = $endtime;

        return Dao::loadEntityList("Patient", $sql, $bind);
    }

    // 名称: 获取全部有效的patientid
    // 备注:
    // 创建:
    // 修改:
    public static function getValidPatientids() {
        $sql = " select id
            from patients
            where status=1 ";
        return Dao::queryValues($sql, []);
    }

    /**
     * 通过患者分组获取患者列表
     * @param string $patientgroupid
     * @return array
     */
    public static function getListByPatientGroupid($patientgroupid) {
        $cond = " AND patientgroupid = :patientgroupid ";

        $bind = [];
        $bind[':patientgroupid'] = $patientgroupid;

        return Dao::getEntityListByCond('Patient', $cond, $bind);
    }
}
