<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/5/28
 * Time: 15:15
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

/********** 因超时回复，快速通行证延长一个月 **********/
/********** 不要重复执行，每次操作完，记得把 $patientids 数组清空 **********/

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";
$unitofwork = BeanFinder::get("UnitOfWork");

// 每次操作完，记得把patientids数组清空
$patientids = [];

$starttime = date('Y-m-d H:i:s');

foreach ($patientids as $patientid) {
    $patient = Patient::getById($patientid);
    if (false == $patient instanceof Patient) {
        echo "不存在id为{$patientid}的患者，跳过\n";
        continue;
    }
    echo "【Patient】【{$patient->id}】【{$patient->name}】\n";
    // 如果在有效期内，直接把延长的这一个月挂在当前的ServiceOrder上
    // 如果已经过期了，则新建一个ServiceOrder
    $serviceOrder = null;
    $quickpass_serviceitem = QuickPass_ServiceItemDao::getLastValidOneByPatientid($patient->id);
    if ($quickpass_serviceitem instanceof QuickPass_ServiceItem && $quickpass_serviceitem->isValidityPeriod()) {
        echo "已找到未过期的快速通行证，最后到期日为{$quickpass_serviceitem->endtime}\n";
        $starttime = $quickpass_serviceitem->endtime;
        $serviceOrder = $quickpass_serviceitem->serviceorder;
    } else {
        echo "未找到未过期的快速通行证\n";
    }

    if (false == $serviceOrder instanceof ServiceOrder) {
        echo "创建商品id为605663996的一个月快速通行证订单【ServiceOrder】\n";
        // 一个月的快速通行证商品
        $serviceProduct = ServiceProduct::getById(605663996);

        $serviceOrder_row = array();
        $serviceOrder_row["patientid"] = $patient->id;
        $serviceOrder_row["serviceproductid"] = $serviceProduct->id;
        $serviceOrder_row["serviceproduct_type"] = $serviceProduct->type;

        $item_cnt = $serviceProduct->item_cnt;
        // 因为是赠送的，所以amount为0
//        $serviceOrder_row["amount"] = $serviceProduct->price;
        $serviceOrder_row["amount"] = 0;
        $serviceOrder_row["is_pay"] = 1;
        $serviceOrder_row["remark"] = '因超时回复，快速通行证延长一个月';
        $serviceOrder = ServiceOrder::createByBiz($serviceOrder_row);
    }

    $days = 31;

    $endtime = date('Y-m-d H:i:s', strtotime("+{$days} day", strtotime($starttime)));

    $row = array();
    $row["wxuserid"] = $serviceOrder->wxuserid;
    $row["userid"] = $serviceOrder->userid;
    $row["serviceorderid"] = $serviceOrder->id;
    $row["patientid"] = $patient->id;

    $row["starttime"] = $starttime;
    $row["endtime"] = $endtime;
    $row["price"] = 0;
    $row["status"] = 1;
    $quickpass_serviceitem = QuickPass_ServiceItem::createByBiz($row);

    echo "创建快速通行证订单明细【QuickPass_ServiceItem】【{$quickpass_serviceitem->id}】\n\n";
}
echo "\n\n";
$unitofwork->commitAndInit();