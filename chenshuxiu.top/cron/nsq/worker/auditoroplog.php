<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

Config::setConfig("update_need_check_version", false);

class create_auditoroplog
{
    public function run ($row) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");

                $i = 0;
                while ($i < 50) {
                    $auditoroplog = AuditorOpLog::createByBiz($row);
                    if (false == $auditoroplog instanceof AuditorOpLog) {
                        $i ++;
                        usleep(200000);
                        continue;
                    }
                    break;
                }

                // 创建失败
                if (false == $auditoroplog instanceof AuditorOpLog) {
                    Debug::warn(__METHOD__ . ' AuditorOpLog row null');
                    Debug::flushXworklog();
                    return false;
                }

                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
            }
        }
        Debug::trace('create_auditoroplog success : auditoroplogid=' . $auditoroplog->id);
        Debug::flushXworklog();

        return true;
    }
}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " auditoroplog row\n";
    exit(1);
}

$json = $argv[1];
$row = json_decode($json, true);
if (!is_array($row)) {
    echo "auditoroplog row $json is empty\n";
    exit(2);
}

$obj = new create_auditoroplog();
$obj->run($row);

Debug::flushXworklog();
