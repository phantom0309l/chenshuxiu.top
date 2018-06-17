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

// 开发库数据清理脚本,将非测试数据全部删除
class dbfix_revert_wxuserid_userid_patientid
{

    public function dowork () {
        $env = Config::getConfig('env', '');
        if ($env != 'development') {
            // echo "只能在开发环境运行";
            // return false;
        }

        return false;

        $this->fix3id('answersheets'); // fixPatientId

        $this->fix3id('courseuserrefs');

        $this->fix3id('depositeorders');

        $this->fix3id('lessonuserrefs');

        $this->fix3id('patientnotes');
        $this->fix3id('pipes');
        $this->fix3id('pushmsgs');

        $this->fix3id('tickets');

        $this->fix3id('patientwithdraworders');

        $this->fix3id('wxpicmsgs');
        $this->fix3id('wxtxtmsgs');
        $this->fix3id('xanswersheets');

        // $this->checkPipes ();
        // $this->checkCntWithObjType ( 'xanswersheets', 'LessonUserRef' );
    }

    // 修正userid 和 patientid 和 wxuserid
    private function fix3id ($table) {

        $sql = "select count(*) as cnt
            from fcqxdb.{$table} a
            inner join fcdevdb.{$table} b on a.id=b.id
            where a.wxuserid<>b.wxuserid or a.userid<>b.userid or a.patientid<>b.patientid ";

        $this->queryCntSql($sql);

        $sql = "update fcqxdb.{$table} a
        inner join fcdevdb.{$table} b on a.id=b.id
        set a.wxuserid=b.wxuserid , a.userid=b.userid , a.patientid=b.patientid
        where a.wxuserid<>b.wxuserid or a.userid<>b.userid or a.patientid<>b.patientid ";

        $this->exeSql($sql, false);

    }

    // -----------------------------------

    // queryCntSqls
    public function queryCntSqls (array $sqls) {
        foreach ($sqls as $sql) {
            $this->queryCntSql($sql);
        }
    }

    // queryCntSql
    public function queryCntSql ($sql) {
        echo "\n\n=============\n\n";
        echo $sql;
        $cnt = Dao::queryValue($sql);
        echo "\n\n----------\n";
        echo "cnt={$cnt}";

        return $cnt;
    }

    // 执行查询sqls
    public function querySqls (array $sqls) {
        foreach ($sqls as $sql) {
            $this->querySql($sql);
        }
    }

    // 执行查询sql
    public function querySql ($sql) {
        $db = BeanFinder::get('DbExecuter');

        echo "\n\n=============\n\n";
        echo $sql;
        $rows = $db->query($sql);
        echo "\n\n----------\n";
        print_r($rows);
        // $cnt = "跳过";
        echo "\n\n----------\n";
    }

    // 执行删除sqls
    public function exeSqls (array $sqls) {
        foreach ($sqls as $sql) {
            $this->exeSql($sql);
        }
    }

    // 执行删除sql
    public function exeSql ($sql, $isExe = true) {
        $db = BeanFinder::get('DbExecuter');

        echo "\n\n=============\n\n";
        echo $sql;
        if ($isExe) {
            $cnt = $db->executeNoQuery($sql);
        } else {
            $cnt = "跳过";
        }
        echo "\n\n----------\n";
        echo "cnt={$cnt}";
    }
    // -------------------------
}

echo "\n\n-----begin-----\n\n";

$process = new dbfix_revert_wxuserid_userid_patientid();
$process->dowork();

echo "\n\n-----end-----\n\n";

// Debug::flushXworklog ();
