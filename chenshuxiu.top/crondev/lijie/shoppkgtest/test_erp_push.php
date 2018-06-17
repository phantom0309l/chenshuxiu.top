<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 18-05-30
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

$shopPkgId = $argv[1];

class Test_erp_push
{

    public function dowork($shopPkgId) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        if(false == isset($shopPkgId)){
            echo "请输入参数[shoppkgid]！";
            exit();
        }

        $shopPkg = ShopPkg::getById($shopPkgId);

        $isBalance = ShopOrderService::isBalance($shopPkg->shoporder);
        if(false == $isBalance){
            Debug::warn("自动推送配送单到erp时，订单【shoporderid={$shopPkg->shoporderid}】的商品没有完全分配到配送单！！！");
            return;
        }

        if ($shopPkg instanceof ShopPkg) {
            $result = GuanYiService::tradeAddByShopPkg($shopPkg);
            $success = $result["success"];
            if($success){
                echo "推送成功！\n";
                $shopPkg->is_push_erp = 1;
                $shopPkg->time_push_erp = date("Y-m-d H:i:s");
                $shopPkg->remark_push_erp = "";
            }else{
                echo "推送失败！\n";
                $errorDesc = $result["errorDesc"];
                $shopPkg->remark_push_erp = $errorDesc;
                Debug::warn("shopPkg[{$shopPkg->id}]订单推送ERP失败[{$errorDesc}]");
            }
            $this->cronlog_content .= "{$shopPkg->id}\n";
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Test_erp_push.php]=====");

$process = new Test_erp_push();
$process->dowork($shopPkgId);

Debug::trace("=====[cron][end][Test_erp_push.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
