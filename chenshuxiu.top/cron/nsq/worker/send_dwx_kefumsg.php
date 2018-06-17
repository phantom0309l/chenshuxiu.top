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

class send_dwx_kefumsg
{

    public function run ($id) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");
                $doctorids_test = Doctor::getIdsOfCompany();

                $i = 0;
                while ($i < 50) {
                    $dwx_kefumsg = Dwx_kefumsg::getById($id);
                    if (! $dwx_kefumsg) {
                        $i ++;
                        usleep(200000);
                        continue;
                    }
                    break;
                }

                // 没找到消息
                if (! $dwx_kefumsg) {
                    Debug::warn(__METHOD__ . ' dwx_kefumsg is null id [' . $id . ']');
                    Debug::flushXworklog();
                    return false;
                }

                // 已经推送过了
                if ($dwx_kefumsg->send_status != 0) {
                    Debug::trace(__METHOD__ . ' dwx_kefumsg msg [' . $id . '] have been sended already!');
                    Debug::flushXworklog();
                    return true;
                }

                // 线上环境推送,开发环境只推送内部人员
                if ('fangcunyisheng.com' == Config::getConfig('key_prefix')) {
                    $dwx_kefumsg->sendByCron();
                } else {
                    if (in_array($dwx_kefumsg->doctorid, $doctorids_test)) {
                        $dwx_kefumsg->sendByCron();
                        $dwx_kefumsg->send_status = 2;
                    }
                    $dwx_kefumsg->send_status = 2;
                }
                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
                // Debug::warn('send_push_msg fail ' . $j . ' pushmsgid:' .
            // $id);
            }
        }
        Debug::trace('send_dwx_kefumsg success dwx_kefumsgid:' . $id);
        Debug::flushXworklog();

        return true;
    }
}
if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " pushmsgid\n";
    exit(1);
}

$id = $argv[1];
if (! $id) {
    echo "pushmsg id ($id) is empty\n";
    exit(2);
}

$obj = new send_dwx_kefumsg();
$obj->run($id);

Debug::flushXworklog();

