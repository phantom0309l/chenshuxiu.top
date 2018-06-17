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

class send_push_msg
{

    public function run ($id) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");
                $userids_test = UserDao::getTestUserids();

                $i = 0;
                while ($i < 50) {
                    $pushmsg = PushMsg::getById($id);
                    if (! $pushmsg) {
                        $i ++;
                        usleep(200000);
                        continue;
                    }
                    break;
                }

                // 没找到消息
                if (! $pushmsg) {
                    Debug::warn(__METHOD__ . ' pushmsg is null id [' . $id . ']');
                    Debug::flushXworklog();
                    return false;
                }

                // 已经推送过了
                if ($pushmsg->send_status != 0) {
                    Debug::trace(__METHOD__ . ' pushmsg msg [' . $id . '] have been sended already!');
                    Debug::flushXworklog();
                    return true;
                }

                // 线上环境推送,开发环境只推送内部人员
                if ('fangcunyisheng.com' == Config::getConfig('key_prefix')) {
                    $pushmsg->sendByCron();
                } else {
                    if (in_array($pushmsg->userid, $userids_test)) {
                        $pushmsg->sendByCron();
                    }
                    $pushmsg->send_status = 2;
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
        Debug::trace('send_push_msg success pushmsgid:' . $id);
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

$obj = new send_push_msg();
$obj->run($id);

Debug::flushXworklog();
