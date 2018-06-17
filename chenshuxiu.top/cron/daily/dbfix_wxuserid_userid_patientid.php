<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_wxuserid_userid_patientid_proces extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 05:01 补修数据关联 wxuserid_userid_patientid';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $dbfix = new dbfix_wxuserid_userid_patientid();
        $dbfix->dowork();
    }
}

// 线上数据库,数据(正确性,完整性,一致性)修复脚本, wxuserid, userid, patientid
class dbfix_wxuserid_userid_patientid extends DbFixBase
{

    public function dowork () {
        $sumcnt = 0;
        $sumcnt += $this->resetUserIdWithLeftJoinNull();
        $sumcnt += $this->resetPatientIdWithLeftJoinNull();
        $sumcnt += $this->fixByUnionId();

        $this->userid_wxuserids();
        $this->patientid_userids();
        $this->deleteNullXObjLogs();

        if ($sumcnt > 0) {
            echo '\n====================\n';
            Debug::warn("dbfix_wxuserid_userid_patientid break, 请手工处理");
            exit();
        }

        $tables = $this->getPatientTables();

        foreach ($tables as $a) {
            // 跳过,有唯一约束冲突的
            if (in_array($a, array(
                'revisitrecords',
                'revisits'))) {

                continue;
            }

            $this->fix3id($a);
        }

        echo "\n ==== getFixFixSqls [beg] ==== \n\n";

        $sqls = $this->getFixFixSqls();
        $this->exeSqls($sqls);

        echo "\n ==== getFixFixSqls [end] ==== \n\n";

        $this->checkPipes();
        // $this->checkCntWithObjType('xanswersheets', 'LessonUserRef');
    }

    private function userid_wxuserids () {
        echo "\n ==== userid_wxuserids [begin] ==== \n\n";

        $sql = 'delete from userid_wxuserids';
        Dao::executeNoQuery($sql);

        $sql = 'insert into userid_wxuserids
            select userid,id as wxuserid
            from wxusers
            group by userid
            having count(*) = 1 ';
        Dao::executeNoQuery($sql);

        echo "\n ==== userid_wxuserids [end] ==== \n\n";
    }

    private function patientid_userids () {
        echo "\n ==== patientid_userids [begin] ==== \n\n";

        $sql = 'delete from patientid_userids';
        Dao::executeNoQuery($sql);
        $sql = "insert into patientid_userids
            select patientid,id as userid
            from users
            group by patientid
            having count(*) = 1 ";
        Dao::executeNoQuery($sql);

        echo "\n ==== patientid_userids [end] ==== \n\n";
    }

    private function deleteNullXObjLogs () {
        echo "\n ==== jump !!! deleteNullXObjLogs [begin] ==== \n\n";
        return;

        $yesterday = date('Y-m-d', time() - 86400);

        // TODO by sjp 20170829 : 需要修改支持表散列
        $sql = "delete a.*
        from xworkdb.xobjlogs a
        left join pushmsgs b on ( b.id = a.objid and a.objtype = 'PushMsg' )
        where  a.objtype = 'PushMsg' and a.createtime > '{$yesterday} 00:00:00' and b.id is null;";
        $this->exeSql($sql);

        echo "\n ==== deleteNullXObjLogs [end] ==== \n\n";
    }

    // 特殊的修正sqls
    private function getFixFixSqls () {
        $sqls = [];

        $sqls[] = "update pipes a
            left join wxusers b on b.id=a.objid
            set a.objid = a.wxuserid
            where a.objtype='WxUser' and a.objid > 0 and b.id is null ;";

        $sqls[] = "update pipes a
            left join users b on b.id=a.objid
            set a.objid = a.userid
            where a.objtype='User' and a.objid > 0 and b.id is null ;";

        $sqls[] = "update pipes a
            left join patients b on b.id=a.objid
            set a.objid = a.patientid
            where a.objtype='Patient' and a.objid > 0 and b.id is null;";

        $sqls[] = "delete from pipes where wxuserid=0 and userid=0 and patientid=0;;";

        return $sqls;
    }

    // 获取属于patient的表
    private function getPatientTables () {
        $tableNames = $this->allTables;

        $arr = array();

        foreach ($tableNames as $tableName) {
            $sql = "show full fields from `{$tableName}`";
            $rows = Dao::queryRows($sql);

            $i = 0;
            foreach ($rows as $a) {
                if (in_array($a['field'], array(
                    'wxuserid',
                    'userid',
                    'patientid'))) {
                    $i ++;
                }
            }

            if ($i == 3) {
                $arr[] = $tableName;
            }
        }
        return $arr;
    }

    // 前置修正,userid
    private function resetUserIdWithLeftJoinNull () {
        echo "\n ==== resetUserIdWithLeftJoinNull [begin] ==== \n\n";

        $sql = "select count(a.id) as cnt
        from wxusers a
        left join users b on b.id=a.userid
        where b.id is null and a.userid > 0";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "update wxusers a
            left join users b on b.id=a.userid
            set a.userid=0
            where b.id is null and a.userid > 0";

            $this->exeSql($sql, false);
        }

        echo "\n ==== resetUserIdWithLeftJoinNull [end] ==== \n\n";

        return $cnt;
    }

    // 前置修正,patientid
    private function resetPatientIdWithLeftJoinNull () {
        echo "\n ==== resetPatientIdWithLeftJoinNull [begin] ==== \n\n";

        $sql = "select count(a.id) as cnt
        from users a
        left join patients b on b.id=a.patientid
        where b.id is null and a.patientid > 0";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "update users a
            left join patients b on b.id=a.patientid
            set a.patientid=0
            where b.id is null and a.patientid > 0";

            $this->exeSql($sql, false);
        }

        echo "\n ==== resetPatientIdWithLeftJoinNull [end] ==== \n\n";

        return $cnt;
    }

    private function fixByUnionId () {
        echo "\n ==== fixByUnionId [begin] ==== \n\n";

        $sql = "select count(*) as cnt
            from wxusers a
            inner join wxusers b on a.unionid=b.unionid
            where a.id <> b.id and a.unionid<>'' and a.userid<>b.userid and b.userid=0";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "update wxusers a
                inner join wxusers b on a.unionid=b.unionid
                set b.userid=a.userid
                where a.id <> b.id and a.unionid<>'' and a.userid<>b.userid and b.userid=0";

            $this->exeSql($sql, true);
        }

        echo "\n ==== fixByUnionId [end] ==== \n\n";

        return $cnt;
    }

    // 修正userid 和 patientid 和 wxuserid
    private function fix3id ($table) {
        echo "\n\n ======================== \n";
        echo "==== fix3id {$table} [begin] ====";
        echo "\n ======================== \n\n";

        // 一次性修正
        $this->fixNullWxUserId($table);
        $this->fixNullPatientId($table);

        // 先关联修正 userid by createuserid
        $this->fixNullPatientId2($table);

        $this->fixNullUserId($table); // 修第一遍
        $this->fixNullUserId($table); // 修第二遍
        $this->fixNullUserId3($table);

        // 正向加反向修userid
        $this->fixUserId($table);

        // 反向修wxuserid , 需要先修userid
        $this->fixWxUserId($table);

        // 正向修patientid
        $this->fixPatientId($table);

        // 检查userid=0
        $this->checkOneUserIdIsNull($table);

        echo "\n ======================== \n";
        echo "\n ==== fix3id {$table} [end] ==== \n";
        echo "\n ======================== \n\n";
    }

    // 正向加反向修userid
    private function fixUserId ($table) {
        echo "\n ==== fixUserId {$table} [begin] ==== \n\n";

        // 正向修userid
        $sql = $this->getCntSqlFixUserIdByWxuserId($table);
        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $this->doFixUserIdByWxuserId($table);
        }

        // 反向修userid, userid=0 , 当前的patientid 指向了一个孤岛数据
        $sql = "select count(*) as cnt
            from {$table} a
            inner join patients b on b.id = a.patientid
            inner join users c on c.id=b.createuserid
            left join users u on u.patientid=b.id
            where a.userid=0 and u.id is null ; ";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id, b.createuserid
                from {$table} a
                inner join patients b on b.id = a.patientid
                inner join users c on c.id=b.createuserid
                left join users u on u.patientid=b.id
                where a.userid=0 and u.id is null ; ";

            $rows = Dao::queryRows($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);

            foreach ($rows as $i => $row) {
                $entity = Dao::getEntityById($entityType, $row['id']);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixUserId] {$entityType} [{$row['id']}]->userid : {$entity->userid} => {$row['createuserid']} \n";

                $entity->set4lock('userid', $row['createuserid']);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        // 反向修userid, userid=0 or userid>0
        $arr = array(
            0,
            1);

        foreach ($arr as $fixall) {
            $sql = $this->getCntSqlFixUserIdByPatientId($table, $fixall);
            $cnt = $this->queryCntSql($sql);

            if ($cnt > 0) {
                echo "\n\n ==== need update UserId {$cnt} ==== \n\n";
                $this->doFixUserIdByPatientId($table, $fixall);
            }
        }

        $sql = $this->getAllCntSqlFixUserIdByPatientId($table);
        $cnt = $this->queryCntSql($sql);
        if ($cnt > 0) {
            echo "\n\n ==== getAllCntSqlFixUserIdByPatientId ==== \n\n";
        }

        echo "\n ==== fixUserId {$table} [end] ==== \n\n";
    }

    // 修正patientid
    private function fixPatientId ($table) {
        echo "\n\n ==== fixPatientId {$table} [begin] ==== \n";

        $sql = $this->getCntSqlFixPatientIdByUserId($table);
        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $this->doFixPatientIdByUserId($table);
        }

        echo "\n ==== fixPatientId {$table} [end] ==== \n\n";
    }

    // checkOneUserIdIsNull
    private function checkOneUserIdIsNull ($table) {
        echo "\n\n ==== checkOneUserIdIsNull {$table} [begin] ==== \n";

        $sql = "
            select count(*) as cnt
            from {$table} a
            inner join patientid_userids b on a.patientid = b.patientid
            where a.userid=0 ";
        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            echo "\n\n====== need update userid=0 {$cnt} =======\n\n";
        }

        echo "\n ==== checkOneUserIdIsNull {$table} [end] ==== \n\n";
    }

    // 修正wxuserid
    private function fixWxUserId ($table) {
        echo "\n\n ==== fixWxUserId {$table} [begin] ==== \n";

        // 反向修wxuserid , wxuserid=0 or wxuserid>0
        $arr = array(
            0,
            1);

        foreach ($arr as $fixall) {
            $sql = $this->getCntSqlFixWxUserIdByUserId($table, $fixall);
            $cnt = $this->queryCntSql($sql);

            if ($cnt > 0) {
                echo "\n\n ==== need update WxUserId {$cnt} ==== \n\n";
                $this->doFixWxUserIdByUserId($table, $fixall);
            }
        }

        $sql = $this->getAllCntSqlFixWxUserIdByUserId($table);
        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            echo "\n\n ==== getAllCntSqlFixWxUserIdByUserId ==== \n\n";
        }

        echo "\n ==== fixWxUserId {$table} [end] ==== \n\n";
    }

    // -----------------------------------

    // 修正 null wxuserid , 一次性修正
    private function fixNullWxUserId ($table) {
        echo "\n\n ==== fixNullWxUserId {$table} [begin] ==== \n";

        $sql = "select count(*) as cnt
            from {$table} a
            left join wxusers b on b.id = a.wxuserid
            where a.wxuserid > 0 and b.id is null;";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id
                from {$table} a
                left join wxusers b on b.id = a.wxuserid
                where a.wxuserid > 0 and b.id is null;";

            $ids = Dao::queryValues($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);
            foreach ($ids as $i => $id) {
                $entity = Dao::getEntityById($entityType, $id);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixNullWxUserId] {$entityType} [{$id}]->wxuserid : {$entity->wxuserid} => 0 \n";

                $entity->set4lock('wxuserid', 0);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        echo "\n\n ==== fixNullWxUserId {$table} [end] ==== \n";
    }

    // 修正 null patientid , 一次性修正
    private function fixNullPatientId ($table) {
        echo "\n\n ==== fixNullPatientId {$table} [begin] ==== \n";

        $sql = "select count(*) as cnt
            from {$table} a
            left join patients c on c.id=a.patientid
            left join patienthistorys d on d.id=a.patientid
            where a.patientid > 0 and c.id is null and d.id is null";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id
                from {$table} a
                left join patients c on c.id=a.patientid
                left join patienthistorys d on d.id=a.patientid
                where a.patientid > 0 and c.id is null and d.id is null";

            $ids = Dao::queryValues($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);
            foreach ($ids as $i => $id) {
                $entity = Dao::getEntityById($entityType, $id);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixNullPatientId] {$entityType} [{$id}]->patientid : {$entity->patientid} => 0 \n";

                $entity->set4lock('patientid', 0);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        echo "\n\n ==== fixNullPatientId {$table} [end] ==== \n";
    }

    // 修正 null patientid ,关联修正createuserid
    private function fixNullPatientId2 ($table) {
        echo "\n\n ==== fixNullPatientId2 {$table} [begin] ==== \n";

        $sql = "select count(*) as cnt
            from {$table} a
            inner join patienthistorys p on p.id=a.patientid
            left join patients b on b.id=a.patientid
            where a.patientid > 0 and a.userid=0 and a.wxuserid=0 and b.id is null;";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id, p.createuserid
                from {$table} a
                inner join patienthistorys p on p.id=a.patientid
                left join patients b on b.id=a.patientid
                where a.patientid > 0 and a.userid=0 and a.wxuserid=0 and b.id is null;";

            $rows = Dao::queryRows($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);
            foreach ($rows as $i => $row) {
                $entity = Dao::getEntityById($entityType, $row['id']);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixNullPatientId2] {$entityType} [{$row['id']}]->userid : {$entity->userid} => {$row['createuserid']} \n";

                $entity->set4lock('userid', $row['createuserid']);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        echo "\n\n ==== fixNullPatientId2 {$table} [end] ==== \n";
    }

    // 修正 null userid , 将userid尝试修正成 patient.createuserid, 一次性修正, 连修2次
    private function fixNullUserId ($table) {
        echo "\n\n ==== fixNullUserId {$table} [begin] ==== \n";

        $sql = "select count(*) as cnt
            from {$table} a
            inner join patients p on p.id=a.patientid
            left join users b on b.id=a.userid
            where a.userid > 0 and a.userid<>p.createuserid and b.id is null ";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id, p.createuserid
            from {$table} a
            inner join patients p on p.id=a.patientid
            left join users b on b.id=a.userid
            where a.userid > 0 and a.userid<>p.createuserid and b.id is null ";

            $rows = Dao::queryRows($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);
            foreach ($rows as $i => $row) {
                $entity = Dao::getEntityById($entityType, $row['id']);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixNullUserId](patients) {$entityType} [{$row['id']}]->userid : {$entity->userid} => {$row['createuserid']} \n";

                $entity->set4lock('userid', $row['createuserid']);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        $sql = "select count(*) as cnt
            from {$table} a
            inner join patienthistorys p on p.id=a.patientid
            left join users b on b.id=a.userid
            where a.userid > 0 and a.userid<>p.createuserid and b.id is null ";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id, p.createuserid
                from {$table} a
                inner join patienthistorys p on p.id=a.patientid
                left join users b on b.id=a.userid
                where a.userid > 0 and a.userid<>p.createuserid and b.id is null ";

            $rows = Dao::queryRows($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);
            foreach ($rows as $i => $row) {
                $entity = Dao::getEntityById($entityType, $row['id']);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixNullUserId](patienthistorys) {$entityType} [{$row['id']}]->userid : {$entity->userid} => {$row['createuserid']} \n";

                $entity->set4lock('userid', $row['createuserid']);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        echo "\n\n ==== fixNullUserId {$table} [end] ==== \n";
    }

    // 修正 null userid , set userid=0 , 需要后执行
    private function fixNullUserId3 ($table) {
        echo "\n\n ==== fixNullUserId3 {$table} [begin] ==== \n";

        $sql = "select count(*) as cnt
            from {$table} a
            left join users b on b.id=a.userid
            where a.userid > 0
            and b.id is null";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            $sql = "select a.id
                from {$table} a
                left join users b on b.id=a.userid
                where a.userid > 0
                and b.id is null";
            $this->exeSql($sql, true);

            $ids = Dao::queryValues($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($table);
            foreach ($ids as $i => $id) {
                $entity = Dao::getEntityById($entityType, $id);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [fixNullUserId3] {$entityType} [{$id}]->userid : {$entity->userid} => 0 \n";

                $entity->set4lock('userid', 0);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        echo "\n\n ==== fixNullUserId3 {$table} [end] ==== \n";
    }

    // 正修UserId 数目, a.userid<>b.userid
    private function getCntSqlFixUserIdByWxuserId ($tablea) {
        return "select count(*) as cnt
            from {$tablea} a
            inner join wxusers b on b.id=a.wxuserid
            where a.userid<>b.userid";
    }

    // 正修UserId, a.userid<>b.userid
    private function doFixUserIdByWxuserId ($tablea) {
        $sql = "select a.id, b.userid
            from {$tablea} a
            inner join wxusers b on b.id=a.wxuserid
            where a.userid<>b.userid";

        $rows = Dao::queryRows($sql);

        $unitofwork = BeanFinder::get('UnitOfWork');
        $entityType = $this->table2entityType($tablea);
        foreach ($rows as $i => $row) {
            $entity = Dao::getEntityById($entityType, $row['id']);

            $time = date('Y-m-d H:i:s');
            echo "\n[{$time}] [doFixUserIdByWxuserId] {$entityType} [{$row['id']}]->userid : {$entity->userid} => {$row['userid']} \n";

            $entity->set4lock('userid', $row['userid']);

            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }
        $unitofwork->commitAndInit();

        return count($rows);
    }

    // 数目 a.patientid<>b.patientid
    private function getCntSqlFixPatientIdByUserId ($tablea) {
        return "select count(*) as cnt
            from {$tablea} a
            inner join users b on b.id=a.userid
            where a.patientid<>b.patientid";
    }

    // 修正 a.patientid<>b.patientid
    private function doFixPatientIdByUserId ($tablea) {
        $sql = "select a.id, b.patientid
            from {$tablea} a
            inner join users b on b.id=a.userid
            where a.patientid<>b.patientid";

        $rows = Dao::queryRows($sql);

        $unitofwork = BeanFinder::get('UnitOfWork');
        $entityType = $this->table2entityType($tablea);
        foreach ($rows as $i => $row) {
            $entity = Dao::getEntityById($entityType, $row['id']);

            $time = date('Y-m-d H:i:s');
            echo "\n[{$time}] [doFixPatientIdByUserId] {$entityType} [{$row['id']}]->patientid : {$entity->patientid} => {$row['patientid']} \n";

            $entity->set4lock('patientid', $row['patientid']);

            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }
        $unitofwork->commitAndInit();

        return count($rows);
    }

    // 数目 of 反向修正 a.patientid > 0 , userid=0
    private function getAllCntSqlFixUserIdByPatientId ($tablea) {
        return "select count(*) as cnt
            from {$tablea} a
            where a.patientid > 0 and a.userid=0";
    }

    // 数目 of 反向修正 a.patientid > 0 , patient只有一个user (userid=0 or userid>0)
    private function getCntSqlFixUserIdByPatientId ($tablea, $fixall = false) {
        $cond = "a.userid=0";
        if ($fixall) {
            $cond = "a.userid<>tt.userid";
        }

        return "select count(*) as cnt
            from {$tablea} a
            inner join patientid_userids tt on a.patientid=tt.patientid
            where $cond";
    }

    // 反向修正 a.patientid > 0 , userid=0 且 patient只有一个user
    private function doFixUserIdByPatientId ($tablea, $fixall = false) {
        $cond = "a.userid=0";
        if ($fixall) {
            $cond = "a.userid<>tt.userid";
            return 0;
        }

        $sql = "select a.id, tt.userid
            from {$tablea} a
            inner join patientid_userids tt on a.patientid=tt.patientid
            where $cond";

        $rows = Dao::queryRows($sql);

        $unitofwork = BeanFinder::get('UnitOfWork');
        $entityType = $this->table2entityType($tablea);

        $entityType = trim($entityType);
        if(empty($entityType))
        {
            echo "{$tablea} => [EntityType] is null";
            return;
        }

        foreach ($rows as $i => $row) {
            $entity = Dao::getEntityById($entityType, $row['id']);

            $time = date('Y-m-d H:i:s');
            echo "\n[{$time}] [doFixUserIdByPatientId] {$entityType} [{$row['id']}]->userid : {$entity->userid} => {$row['userid']} \n";

            $entity->set4lock('userid', $row['userid']);

            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }
        $unitofwork->commitAndInit();

        return count($rows);
    }

    // 数目 of 反向修正 a.userid > 0 , wxuserid=0
    private function getAllCntSqlFixWxUserIdByUserId ($tablea) {
        return "select count(*) as cnt
            from {$tablea} a
            where a.userid > 0 and a.wxuserid=0";
    }

    // 数目 of 反向修正 a.userid > 0 ,user只有一个wxuser ( wxuserid=0 or wxuserid>0)
    private function getCntSqlFixWxUserIdByUserId ($tablea, $fixall = false) {
        $cond = "a.wxuserid=0";
        if ($fixall) {
            $cond = "a.wxuserid<>tt.wxuserid";
        }

        return "select count(*) as cnt
            from {$tablea} a
            inner join userid_wxuserids tt on a.userid=tt.userid
            where $cond";
    }

    // 反向修正 of 反向修正 a.userid > 0 ,user只有一个wxuser ( wxuserid=0 or wxuserid>0)
    private function doFixWxUserIdByUserId ($tablea, $fixall = false) {
        $cond = "a.wxuserid=0";
        if ($fixall) {
            $cond = "a.wxuserid<>tt.wxuserid";
            return 0;
        }

        $sql = "select a.id, tt.wxuserid
            from {$tablea} a
            inner join userid_wxuserids tt on a.userid=tt.userid
            where $cond";

        $rows = Dao::queryRows($sql);

        $unitofwork = BeanFinder::get('UnitOfWork');
        $entityType = $this->table2entityType($tablea);

        foreach ($rows as $i => $row) {
            $entity = Dao::getEntityById($entityType, $row['id']);

            $time = date('Y-m-d H:i:s');
            echo "\n[{$time}] [doFixWxUserIdByUserId] {$entityType} [{$row['id']}]->wxuserid : {$entity->wxuserid} => {$row['wxuserid']} \n";

            $entity->set4lock('wxuserid', $row['wxuserid']);

            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }
        $unitofwork->commitAndInit();

        return count($rows);
    }

    // 检查答卷表,user重复答卷
    private function check_xanswersheets () {
        $sql = "select userid,xquestionsheetid,count(*) as cnt
            from xanswersheets
            group by userid,xquestionsheetid
            having cnt > 1";
    }

    // 检查流表
    private function checkPipes () {
        $sql = "select objtype from pipes group by objtype";
        $objtypes = Dao::queryValues($sql);
        foreach ($objtypes as $objtype) {

            if (in_array($objtype, array(
                'WxUser',
                'User',
                'Patient'))) {
                continue;
            }

            // 删除野指针
            $this->deleteAByObjIdIsNull('pipes', $objtype);

            // 检查pipes 和 obj 表 wxuserid,userid,patientid 是否一致, 20170711注释掉了
            // $this->checkCntWithObjType('pipes', $objtype);
        }
    }

    // 删除孤岛数据
    private function deleteAByObjIdIsNull ($tablea, $objtype, $isExe = true) {
        $tableb = strtolower($objtype);
        $tableb .= 's';

        // if b.id is null then delete a.*
        $sql = "select count(*) as cnt
            from {$tablea} a
            left join {$tableb} b on b.id=a.objid
            where a.objtype='{$objtype}' and b.id is null";

        $cnt = $this->queryCntSql($sql);
        if ($cnt > 0) {
            $sql = "delete a.*
                from {$tablea} a
                left join {$tableb} b on b.id=a.objid
                where a.objtype='{$objtype}' and b.id is null";

            $this->exeSql($sql, $isExe);
        }
    }

    // 检查是否不一致:patientid,userid,wxuserid
    private function checkCntWithObjType ($tablea, $objtype) {
        $tableb = strtolower($objtype);
        $tableb .= 's';

        // patientid 不一致
        // a.id,a.createtime,a.patientid,b.patientid
        $sql = "select count(*) as cnt
            from {$tablea} a
            inner join {$tableb} b on a.objid=b.id
            inner join wxusers c on c.id = a.wxuserid
            where a.objtype='{$objtype}' and a.patientid <> b.patientid
            and c.openid not like 'clone%' ";

        $cnt = $this->queryCntSql($sql);
        if ($cnt > 0) {
            echo "\n\n====== update : patientid 不一致 =======\n\n";
        }

        // userid 不一致
        // a.id,a.createtime,a.userid,b.userid
        $sql = "select count(*) as cnt
            from {$tablea} a
            inner join {$tableb} b on a.objid=b.id
            inner join wxusers c on c.id = a.wxuserid
            where a.objtype='{$objtype}' and a.userid <> b.userid
            and c.openid not like 'clone%' ";

        $cnt = $this->queryCntSql($sql);
        if ($cnt > 0) {
            echo "\n\n====== update : userid 不一致 =======\n\n";
        }

        // wxuserid 不一致
        // a.id,a.createtime,a.wxuserid,b.wxuserid
        $sql = "select count(*) as cnt
            from {$tablea} a
            inner join {$tableb} b on a.objid=b.id
            inner join wxusers c on c.id = b.wxuserid
            where a.objtype='{$objtype}' and a.wxuserid <> b.wxuserid and a.wxuserid=0
            and c.openid not like 'clone%' ";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {

            $sql = "select a.id, b.wxuserid
                from {$tablea} a
                inner join {$tableb} b on a.objid=b.id
                inner join wxusers c on c.id = b.wxuserid
                where a.objtype='{$objtype}' and a.wxuserid <> b.wxuserid and a.wxuserid=0
                and c.openid not like 'clone%' ";

            $rows = Dao::queryRows($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($tablea);
            foreach ($rows as $i => $row) {
                $entity = Dao::getEntityById($entityType, $row['id']);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [checkCntWithObjType][1] {$entityType} [{$row['id']}]->wxuserid : {$entity->wxuserid} => {$row['wxuserid']} \n";

                $entity->set4lock('wxuserid', $row['wxuserid']);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        // wxuserid 不一致
        // a.id,a.createtime,a.wxuserid,b.wxuserid
        $sql = "select count(*) as cnt
            from {$tablea} a
            inner join {$tableb} b on a.objid=b.id
            inner join wxusers c on c.id = a.wxuserid
            where a.objtype='{$objtype}' and a.wxuserid <> b.wxuserid and b.wxuserid=0
            and c.openid not like 'clone%' ";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {

            $sql = "select b.id, a.wxuserid
                from {$tablea} a
                inner join {$tableb} b on a.objid=b.id
                inner join wxusers c on c.id = a.wxuserid
                where a.objtype='{$objtype}' and a.wxuserid <> b.wxuserid and b.wxuserid=0
                and c.openid not like 'clone%' ";

            $rows = Dao::queryRows($sql);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $entityType = $this->table2entityType($tableb);
            foreach ($rows as $i => $row) {
                $entity = Dao::getEntityById($entityType, $row['id']);

                $time = date('Y-m-d H:i:s');
                echo "\n[{$time}] [checkCntWithObjType][2] {$entityType} [{$row['id']}]->wxuserid : {$entity->wxuserid} => {$row['wxuserid']} \n";

                $entity->set4lock('wxuserid', $row['wxuserid']);

                if ($i % 100 == 0) {
                    $unitofwork->commitAndInit();
                }
            }
            $unitofwork->commitAndInit();
        }

        // wxuserid 不一致
        // a.id,a.createtime,a.wxuserid,b.wxuserid
        $sql = "select count(*) as cnt
            from {$tablea} a
            inner join {$tableb} b on a.objid=b.id
            inner join wxusers c on c.id = a.wxuserid
            where a.objtype='{$objtype}' and a.wxuserid <> b.wxuserid
            and c.openid not like 'clone%' ";

        $cnt = $this->queryCntSql($sql);

        if ($cnt > 0) {
            echo "\n\n====== need update wxuserid {$cnt} =======\n\n";
        }
    }
}

// //////////////////////////////////////////////////////

$process = new Dbfix_wxuserid_userid_patientid_proces(__FILE__);
$process->dowork();
