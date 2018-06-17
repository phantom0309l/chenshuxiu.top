<?php
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");

// 系统初始化，需要在入口文件调用
TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[test][beg][test_account.php]=====");

// 开启工作单元
$unitOfWork = BeanFinder::get("UnitOfWork");

$sys = User::getById(1);
$sjp = UserDao::getByMobile('18611820612');
$xuzhe = UserDao::getByMobile('15655142216');

$sysAccount1 = Account::createByBiz($sys, 'chongzhi');
$sysAccount2 = Account::createByBiz($sys, 'tixian');

$sjpAccount1 = Account::createByBiz($sjp, 'yue');
$sjpAccount2 = Account::createByBiz($sjp, 'yajin');

$xuzheAccount1 = Account::createByBiz($xuzhe, 'yue');
$xuzheAccount2 = Account::createByBiz($xuzhe, 'yajin');

PostingRule::createAndProcess($sysAccount1, $xuzheAccount2, 1000, $sjp, 'chongzhi:' . date('Y-m-d-H-i-s'), '充值');

PostingRule::createAndProcess($sjpAccount2, $sjpAccount1, 500, $sjp, 'jiangjin:' . date('Y-m-d-H-i-s'), '学习课程退押金');

// 工作单元提交
$unitOfWork->commit();

Debug::trace("=====[test][end][test_account.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();

