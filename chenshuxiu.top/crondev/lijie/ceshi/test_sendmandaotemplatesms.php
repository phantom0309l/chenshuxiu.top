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

class Test_sendmandaotemplatesms
{

    public function dowork($userId) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $user = User::getById($userId);

        if ($user instanceof User) {
            $ret = ShortMsg::sendManDaoTemplateSMS_j4now($user->mobile,  "您的验证码是996669（10分钟内有效）。请在页面中提交验证码完成验证。【方寸医生】");
            Debug::trace($ret);
            if ($ret == NULL) {
                echo "发送错误". "\t";
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Test_sendmandaotemplatesms.php]=====");

$process = new Test_sendmandaotemplatesms();
$process->dowork($userId);

Debug::trace("=====[cron][end][Test_sendmandaotemplatesms.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
