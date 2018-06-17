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


class SendSms6298
{

    public function dowork() {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT a.mobile as mobile
        FROM users a
        INNER JOIN patients b ON b.id=a.patientid
        WHERE b.old_patientid > 0;";

        $sql = "select mobile from users where mobile in (18311374180, 18101272021)";

        $mobiles = Dao::queryValues($sql);

        foreach ($mobiles as $mobile) {
            if(11 != strlen($mobile)){
                echo "此手机号不是合法的手机号！【mobile={$mobile}】\n";
                return;
            }
            if(1 != substr($mobile, 0, 1)){
                echo "此手机号不是合法的手机号！【mobile={$mobile}】\n";
                return;
            }

            $ret = ShortMsg::sendManDaoTemplateSMS_j4now($mobile,  "【肺动脉高压】尊敬的用户，由罕见病中心发起的“爱·延续·肺动脉高压患者全程关爱项目”现已正式启动。请您点击链接【http://t.cn/RBV6nc6】，保存并微信识别二维码，关注微信服务平台参与服务。（退订回TD）", 1);
            Debug::trace($ret);
            if ($ret == NULL) {
                echo "发送错误【mobile={$mobile}】\n";
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][SendSms6298.php]=====");

$process = new SendSms6298();
$process->dowork();

Debug::trace("=====[cron][end][SendSms6298.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
