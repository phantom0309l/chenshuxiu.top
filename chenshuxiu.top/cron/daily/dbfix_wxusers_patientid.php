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

// #4372 wxusers 增加 字段 patientid
class Dbfix_init_wxusers_patientid extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 22:00 检查wxusers表patientid的正确性';
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
        $sql = "select a.id as wxuserid, a.patientid as wxuser_patientid, b.patientid as user_patientid
                from wxusers a
                inner join users b on b.id=a.userid
                where a.patientid <> b.patientid;";

        $rows = Dao::queryRows($sql);

        $unitofwork = BeanFinder::get("UnitOfWork");

        $cnt = count($rows);

        $this->cronlog_brief = $cnt;

        foreach ($rows as $i => $row) {
            $wxuserid = $row['wxuserid'];
            $wxuser_patientid = $row['wxuser_patientid'];
            $user_patientid = $row['user_patientid'];

            $wxuser = WxUser::getById($wxuserid);

            // 修正 wxuser->patientid
            $wxuser->fixPatientId($user_patientid);

            $str = "wxuser[{$wxuserid}]->patientid : {$wxuser_patientid} => {$user_patientid}";

            Debug::warn($str);

            echo "\n {$i} / {$cnt} : {$str}";

            $this->cronlog_content .= "\n{$str}";

            $unitofwork->commitAndInit();
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Dbfix_init_wxusers_patientid(__FILE__);
$process->dowork();
