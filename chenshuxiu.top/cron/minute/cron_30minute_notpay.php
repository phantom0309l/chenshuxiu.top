<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/4
 * Time: 15:57
 */
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
// Debug::$debug = 'Dev';
class Cron_30minute_notpay extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'minute';
        $row["title"] = "每5分钟, 药门诊下单且30分钟未支付的患者生成一条订单跟进任务，患者进商业组";
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $brief = 0;
        $logcontent = '';

        $ids = $this->getIds();

        foreach ($ids as $id) {
            $shoporder = ShopOrder::getById($id);

            $patient = $shoporder->patient;

            // 每条未支付订只生成一条订单跟进任务
            $optask_order_notpay = $this->getOpTaskByShopOrderUnicode($shoporder, 'shoporder:notpayfollow');
            if (false == $optask_order_notpay instanceof OpTask) {
                OpTaskService::createPatientOpTask($patient, 'shoporder:notpayfollow', $shoporder, date('Y-m-d'));
            }

            // 进入商业组
            $patientgroup = PatientGroupDao::getByTitle('商业组');
            if ($patientgroup instanceof PatientGroup && $patientgroup->id != $patient->patientgroupid) {
                $patient->patientgroupid = $patientgroup->id;
            }

            $brief ++;
            $logcontent .= "{$id->id},";

            echo "{$brief} {$id}\n";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_content = $logcontent;
        $this->cronlog_brief = "cnt={$brief}";

        $unitofwork->commitAndInit();
    }

    public function getIds () {
        $starttime = date('Y-m-d H:i', strtotime("-40 minute", time()));
        $endtime = date('Y-m-d H:i', strtotime("-30 minute", time()));

        $cancer_diseaseids = Disease::getCancerDiseaseidsStr();

        $sql = "select a.id
                from shoporders a
                inner join patients b on b.id = a.patientid
                where a.is_pay = 0 and a.createtime >= '{$starttime}' and a.createtime <= '{$endtime}' and b.diseaseid in ($cancer_diseaseids) ";

        echo "\n{$sql}\n";

        return Dao::queryValues($sql);
    }

    public function getOpTaskByShopOrderUnicode (ShopOrder $shoporder, $unicode) {
        $optasktpl = OpTaskTplDao::getOneByUnicode($unicode);
        $cond = " and objtype = 'ShopOrder' and objid = {$shoporder->id} and optasktplid = {$optasktpl->id} ";

        return Dao::getEntityByCond('OpTask', $cond);
    }
}

// //////////////////////////////////////////////////////

$process = new Cron_30minute_notpay(__FILE__);
$process->dowork();
