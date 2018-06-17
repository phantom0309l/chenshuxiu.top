<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 18-05-03
 * Time: 上午11:44
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$userId = $argv[1];

class Test_sendtemplatesms
{

    public function dowork($userId) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $user = User::getById($userId);

        if ($user instanceof User) {
            $ret = ShortMsg::sendTemplateSMS_j4now($user->mobile,  ["111222"], 254276);
            Debug::trace($ret);
            if ($ret == NULL) {
                echo "发送错误". "\t";
            } else {
                if ($ret->statusCode != 0) {
                    echo "发送失败". "\t". $ret->statusCode . "\t". $ret->statusMsg. "\t";
                } else {
                    echo "发送成功". "\t";
                }
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Test_sendtemplatesms.php]=====");

$process = new Test_sendtemplatesms();
$process->dowork($userId);

Debug::trace("=====[cron][end][Test_sendtemplatesms.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
