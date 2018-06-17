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

$unitofwork = BeanFinder::get ( "UnitOfWork" );

$wxuser = WxUser::getById(97);
//$menu_text = WxMenu::getSerializedMenuText(1, 135, $wxuser);
$menu_text = WxMenu::getSerializedMenuText(1, 139, $wxuser);
$pre_text = "您已成功开通{$mypatient->doctor->name}医生院外管理服务，下列为您的功能列表：\n";
$after_text = "正在为您生成服务端菜单，您可以等待10分钟左右再次进入『方寸儿童管理服务平台』微信公众号，或直接使用上述菜单。\n点击【我的特权】可以查看当前拥有的特权内容以及升级到更高服务特权需要做的操作；点击【家长须知】可以了解平台服务内容。\n请注意：平台上的指导均由专业的医生助理提供，如果需要帮助可以点击左下角的小键盘切换到输入页面给助理发消息，对话内容仅所属医生、医生助理和家长个人的微信可见。";
$content = $pre_text . $menu_text . $after_text;
PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);

$wxuser = WxUser::getById(122670905);
$menu_text = WxMenu::getSerializedMenuText(2, 100, $wxuser);
$pre_text = "您已成功绑定方寸管理端下列为您的功能列表：\n";
$after_text = "正在为您生成服务端菜单，您可以等待5分钟左右再次进入方寸管理端或直接使用上述菜单。";
$content = $pre_text . $menu_text . $after_text;

$appendarr = array();
$appendarr["doctorid"] = 11;
$appendarr["auditorid"] = 1;
WechatMsg::sendmsg2wxuser_dwx($wxuser, $content, $appendarr);

$unitofwork->commitAndInit();
Debug::flushXworklog();
