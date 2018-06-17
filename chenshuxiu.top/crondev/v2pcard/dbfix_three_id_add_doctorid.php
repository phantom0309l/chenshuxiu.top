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

// 有三个字段( wxuserid, userid, patientid )的表增加字段 pcardid
class dbfix_three_id_add_doctorid extends DbFixBase
{

    public function dowork () {

        $tables = $this->getPatientTables();

        // $this->getAlterSqls($tables);

        $this->fixDoctorid($tables);
    }

    public function getAlterSqls ($tables) {
        foreach ($tables as $a) {

            // echo "\n ALTER TABLE `{$a}` DROP `pcardid`; ";

            // echo "\n ALTER TABLE `{$a}` CHANGE `pcardid` `pcardid` BIGINT(20)
            // UNSIGNED NOT NULL DEFAULT '0' COMMENT 'pcardid';";

            echo "\n
ALTER TABLE `{$a}` ADD `doctorid` BIGINT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'doctorid' AFTER `patientid`;
ALTER TABLE `{$a}` ADD INDEX `idx_doctorid` (`doctorid`);";

        }
    }

    public function fixDoctorid ($tables) {
        foreach ($tables as $a) {

            $sql = " update {$a} a inner join patients p on p.id=a.patientid set a.doctorid = p.doctorid where a.doctorid = 0 ; ";
            echo "\n$sql";
            $cnt = Dao::executeNoQuery($sql);
            echo "\ncnt={$cnt}";
        }
    }

    // 获取属于patient的表
    private function getPatientTables () {
        $sql = "show tables";
        $tableNames = Dao::queryValues($sql);

        $arr = array();
        $xx = array();

        foreach ($tableNames as $tableName) {
            $sql = "show full fields from `$tableName`";
            $rows = Dao::queryRows($sql);

            $i = 0;
            $yy = array();
            foreach ($rows as $a) {
                if (in_array($a['field'],
                        array(
                            'patientid',
                            'doctorid'))) {
                    $i ++;
                    $yy[] = $a['field'];
                }

                // if($a['field'] == 'doctorid')
                // {
                // $i--;
                // }
            }

            if ($i == 2) {
                $xx[$tableName] = $yy;
                echo "\n{$tableName}";
                $arr[] = $tableName;
            }
        }

        return $arr;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][dbfix_three_id_add_doctorid.php]=====");

$process = new dbfix_three_id_add_doctorid();
$process->dowork();

Debug::trace("=====[cron][end][dbfix_three_id_add_doctorid.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
