<?php
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Export_shoporder
{
    public function run ($id) {
        $j = 0;
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");

                $i = 0;
                $exportJob = null;
                while ($i < 50) {
                    $exportJob = Dao::getEntityById('Export_Job', $id);
                    if (! $exportJob) {
                        $i ++;
                        usleep(200000);
                        continue;
                    }
                    break;
                }

                // 没找到任务
                if (false == $exportJob instanceof Export_Job) {
                    Debug::warn(__METHOD__ . ' export job is null id [' . $id . ']');
                    Debug::flushXworklog();
                    return false;
                }

                // 不是初始状态
                if (! $exportJob->isNew()) {
                    Debug::trace(__METHOD__ . 'export job [' . $id . '] status is not new');
                    Debug::flushXworklog();
                    return true;
                }

                // 开始运行导出任务
                // 给任务置一个状态
                $exportJob->status = Export_Job::STATUS_RUNNING;
                $export_job_type = $exportJob->type;
                if($export_job_type == "shoporder_service"){
                    $this->createExcelWhenShoporder_service($exportJob);
                }

                if($export_job_type == "shoporder_service2"){
                    $this->createExcelWhenShoporder_service2($exportJob);
                }

                if($export_job_type == "shoporder_market"){
                    $this->createExcelWhenShoporder_market($exportJob);
                }

                if($export_job_type == "shoporder_detail"){
                    $this->createExcelWhenShoporder_detail($exportJob);
                }

                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                print_r($e->getMessage());
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
                Debug::warn('export job fail ' . $j . ' jobid:' . $id);
            }
        }
        Debug::trace('export job has done success jobid:' . $id);
        Debug::flushXworklog();

        return true;
    }

    //导出订单明细
    private function createExcelWhenShoporder_detail($exportJob){
        $exportJobid = $exportJob->id;
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getNeedIds($exportJob);
        $len = count($ids);
        $m = 0;
        //要合并的行
        $needMergeRowIndexArr = array();
        $data = array();
        $pos = 1;
        $col_index = 2;

        foreach ($ids as $id) {
            //下载进度
            $m++;
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(($m / $len) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }

            $shopOrder = ShopOrder::getById($id);
            if ($shopOrder instanceof ShopOrder) {
                $patient = $shopOrder->patient;

                //获取需要合并的行数组
                $shopOrderItemCnt = $shopOrder->getShopOrderItemCnt();
                if($shopOrderItemCnt > 1){
                    $needMergeRowIndexArr[] = array($col_index, $col_index + $shopOrderItemCnt - 1);
                }

                $shopOrderItems = $shopOrder->getShopOrderItems();
                foreach($shopOrderItems as $a){

                    $stock_price_yaun_all = 0;
                    $shopOrderItemStockItemRefs = ShopOrderItemStockItemRefDao::getListByShopOrderItem($a);
                    foreach($shopOrderItemStockItemRefs as $shopOrderItemStockItemRef){
                        //货物原价
                        $stock_price_yaun_all += ($shopOrderItemStockItemRef->stockitem->getPrice_yuan() * $shopOrderItemStockItemRef->cnt);
                    }

                    $temp = array();
                    $temp[] = $pos;
                    $temp[] = $shopOrder->time_pay;
                    $temp[] = $patient instanceof Patient == true ? $patient->name : "";
                    $temp[] = $patient instanceof Patient == true ? substr($patient->createtime, 0, 10) : "";
                    $temp[] = $patient instanceof Patient == true ? $patient->getLastPipeToTagStrByUser() : "";
                    $temp[] = $shopOrder->thedoctor->name;
                    $temp[] = $shopOrder->thedoctor->marketauditor->name;
                    $temp[] = $shopOrder->pos;
                    $temp[] = $a->shopproduct->title;
                    $temp[] = $shopOrder->isShopping() == true ? "非药品" : "药品";
                    $temp[] = $a->getPrice_yuan();
                    $temp[] = $a->cnt;
                    $temp[] = $a->getAmount_yuan();
                    $temp[] = $shopOrder->getExpress_price_yuan();
                    $temp[] = $shopOrder->getRefund_amount_yuan();
                    $temp[] = $shopOrder->getLeft_amount_yuan();
                    $temp[] = $this->getOrderStatusStr($shopOrder);
                    $temp[] = $shopOrder->shopaddress->linkman_name;
                    $temp[] = $shopOrder->shopaddress->linkman_mobile;
                    $temp[] = $shopOrder->shopaddress instanceof ShopAddress ? $shopOrder->shopaddress->getDetailAddress() : '';
                    $temp[] = $shopOrder->getExpressCompanyStrs();
                    $temp[] = $shopOrder->getExpressNos();
                    $temp[] = $stock_price_yaun_all;
                    $temp[] = $shopOrder->getExpress_price_real_yuan();
                    $temp[] = $a->shopproduct->service_percent;
                    $temp[] = $a->getService_yuan();

                    $data[] = $temp;
                    $col_index++;
                }

                $pos++;
            }
        }

        //要合并的列
        $needMergeColIndexArr = array(0,1,2,3,4,5,6,7,13,14,15,16,17,18,19,20,21,23);

        $headarr = array(
            "序号",
            "支付时间",
            "患者",
            "报到时间",
            "最后活跃",
            "医生",
            "市场",
            "第几次购买",
            "商品名称",
            "商品类型",
            "商品单价",
            "商品数量",
            "总金额",
            "运费",
            "退款",
            "实收金额",
            "订单状态",
            "收货人",
            "联系电话",
            "收货地址",
            "物流公司",
            "物流单号",
            "货物原价",
            "实际运费",
            "服务比例",
            "服务金额"
        );
        $this->writeFile($exportJob, $data, $headarr, $needMergeRowIndexArr, $needMergeColIndexArr);
        $unitofwork->commitAndInit();
    }

    private function getOrderStatusStr ($shopOrder) {
        if (! $shopOrder->is_pay) {
            return "未支付";
        }

        if ($shopOrder->getLeft_amount() < 1) {
            return "全额退款";
        }

        if ($shopOrder->refund_amount > 0 ) {
            return "部分退款";
        }

        return "已支付";
    }

    //市场数据
    private function createExcelWhenShoporder_market($exportJob){
        $exportJobid = $exportJob->id;
        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getNeedIds($exportJob);
        $len = count($ids);
        $m = 0;
        $data = array();
        foreach ($ids as $id) {
            //下载进度
            $m++;
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(($m / $len) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }
            $shopOrder = ShopOrder::getById($id);
            if ($shopOrder instanceof ShopOrder) {
                $patient = $shopOrder->patient;
                if ($patient instanceof Patient) {
                    $temp = array();
                    $temp[] = $shopOrder->time_pay;
                    $temp[] = $patient->name;
                    $temp[] = $patient->getDayCntFromBaodao();
                    $temp[] = $shopOrder->thedoctor->name;
                    $temp[] = $shopOrder->thedoctor->marketauditor->name;
                    $temp[] = $shopOrder->pos;
                    $temp[] = $shopOrder->getRefund_amount_yuan();
                    $temp[] = $shopOrder->getTitleOfShopProducts();
                    $temp[] = $shopOrder->getAmount_yuan();
                    $data[] = $temp;
                }
            }
        }
        $headarr = array(
            "支付时间",
            "患者",
            "报到天数",
            "医生",
            "市场",
            "第几次购买",
            "退款",
            "商品详情",
            "总价");
        $this->writeFile($exportJob, $data, $headarr);
        $unitofwork->commitAndInit();
    }

    //服务2
    private function createExcelWhenShoporder_service2($exportJob){
        $exportJobid = $exportJob->id;
        $unitofwork = BeanFinder::get("UnitOfWork");

        $d = json_decode($exportJob->data, true);
        $the_month = $startdate = isset($d['startdate']) ? $d['startdate'] : date("Y-m-d", (time() - 6 * 86400));

        $data = array();
        $sql = "select id from doctors";
        $ids = Dao::queryValues($sql);
        $len = count($ids);
        $m = 0;

        foreach($ids as $id){
            //下载进度
            $m++;
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(($m / $len) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }

            $doctor = Doctor::getById($id);

            $active_num = 0;
            $doctor_service_order_num = 0;
            $huodong_cnt = 0;
            $first = $doctor->getShopOrderShouyi_firstByThemonth($the_month);
            $service_percent = $doctor->getShopOrderShouyi_serviceByThemonth($the_month);
            if($startdate){
                $active_num = $doctor->getActivePatientShouyiByThemonth($startdate);
                if($doctor->is_sign > 0){
                    $doctor_service_order_num = $doctor->getDoctorServiceOrdersAmount_yuan($startdate);
                }
                $huodong_cnt = $first + $service_percent + $active_num - $doctor_service_order_num;
            }

            //如果总收益为0，不生成记录直接跳过
            $all_cnt = $first + $service_percent + $active_num;
            if($all_cnt == 0 ){
                continue;
            }
            //如果市场是王春生，直接跳过(春生名下是礼来项目的医生)
            if(10003 == $doctor->auditorid_market){
                //continue;
            }

            $temp = array();
            $temp[] = $doctor->name;
            $temp[] = $doctor->menzhen_offset_daycnt > 0 ? "是" : "否";
            $temp[] = $doctor->is_sign > 0 ? "是" : "否";
            $temp[] = $doctor->hospital->name;
            $temp[] = $doctor->marketauditor->name;
            $temp[] = $first;
            $temp[] = $service_percent;
            $temp[] = $active_num;
            $temp[] = $doctor_service_order_num;
            $temp[] = $huodong_cnt;
            $data[] = $temp;
        }

        $headarr = array(
            "医生",
            "开药门诊",
            "签约",
            "医院",
            "市场",
            "首单",
            "服务",
            "活跃",
            "order",
            "活动",
        );

        $this->writeFile($exportJob, $data, $headarr);
        $unitofwork->commitAndInit();
    }

    //服务1
    private function createExcelWhenShoporder_service($exportJob){
        $exportJobid = $exportJob->id;
        $unitofwork = BeanFinder::get("UnitOfWork");

        $data = array();
        $result = array();
        $shopOrderids = $this->getNeedIds($exportJob);
        $len = count($shopOrderids);
        $m = 0;
        $n = 0;

        foreach ($shopOrderids as $shopOrderid) {
            $shopOrder = ShopOrder::getById($shopOrderid);
            if ($shopOrder instanceof ShopOrder) {

                if(false == $shopOrder->isValid()){
                    continue;
                }
                $thedoctor = $shopOrder->thedoctor;
                if(false == $result[$thedoctor->id]){
                    $temp = array();
                    $temp["doctor_name"] = $thedoctor->name;
                    $temp["hospital_name"] = $thedoctor->hospital->name;
                    $temp["marketauditor_name"] = $thedoctor->marketauditor->name;
                    $temp["first"] = 0;
                    $temp["service_percent"] = 0;
                    $result[$thedoctor->id] = $temp;
                }

                //首单且是药品
                if(1 == $shopOrder->pos && ShopOrder::type_chufang == $shopOrder->type){
                    $result[$thedoctor->id]["first"] += 50;
                }

                $result[$thedoctor->id]["service_percent"] += $shopOrder->getService_yuan();
            }
        }

        $d = json_decode($exportJob->data, true);
        $startdate = isset($d['startdate']) ? $d['startdate'] : date("Y-m-d", (time() - 6 * 86400));
        foreach($result as $doctorid => $a){
            //下载进度
            $m++;
            if ($m > 0 && $m % 100 == 0) {
                $exportJob->progress = round(($m / $len) * 100, 1);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $exportJob = Dao::getEntityById('Export_Job', $exportJobid);
            }

            $active_num = 0;
            $doctor_service_order_num = 0;
            $huodong_cnt = 0;
            $first = $a["first"];
            $service_percent = $a["service_percent"];
            if($startdate){
                $doctor = Doctor::getById($doctorid);
                $active_num = $doctor->getActivePatientShouyiByThemonth($startdate);
                $doctor_service_order_num = $doctor->getDoctorServiceOrdersAmount_yuan($startdate);
                $huodong_cnt = $first + $service_percent + $active_num - $doctor_service_order_num;
            }
            $temp = array();
            $temp[] = $a["doctor_name"];
            $temp[] = $a["hospital_name"];
            $temp[] = $a["marketauditor_name"];
            $temp[] = $first;
            $temp[] = $service_percent;
            $temp[] = $active_num;
            $temp[] = $doctor_service_order_num;
            $temp[] = $huodong_cnt;
            $data[] = $temp;
        }

        $headarr = array(
            "医生",
            "医院",
            "市场",
            "首单",
            "服务",
            "活跃",
            "order",
            "活动",
        );

        $this->writeFile($exportJob, $data, $headarr);
        $unitofwork->commitAndInit();
    }

    private function writeFile($exportJob, $data, $headarr, $needMergeRowIndexArr = null, $needMergeColIndexArr = null){
        $username = $exportJob->auditor->user->username;
        if(empty($username)){
            Debug::trace(__METHOD__ . "======exportjob[{$exportJob->id}]==========username is empty==============");
            return;
        }
        $distDir = "/home/xdata/download/shoporder/{$username}";
        if (! is_dir($distDir)) {
            mkdir($distDir, 0755, true);
        }

        $fileName = md5($exportJob->id);
        $fileurl = $distDir . '/' . $fileName . '.xlsx';
        if( empty($needMergeRowIndexArr) ){
            ExcelUtil::createForCron($data, $headarr, $fileurl);
        }else{
            ExcelUtil::createHasMergeCellsForCron($data, $headarr, $fileurl, $needMergeRowIndexArr, $needMergeColIndexArr);
        }

        $exportJob->progress = 100;
        $exportJob->status = Export_Job::STATUS_COMPLETE;
    }

    private function getNeedIds($exportJob){
        $d = json_decode($exportJob->data, true);

        $diseaseid = !empty($d['diseaseid']) ? $d['diseaseid'] : 0;
        $mydisease = Disease::getById($diseaseid);

        //状态分类筛选
        $diseasegroupid = !empty($d['diseasegroupid']) ? $d['diseasegroupid'] : 0;
        $type = !empty($d['type']) ? $d['type'] : 'all';
        $haveitem = !empty($d['haveitem']) ? $d['haveitem'] : "haveitem";
        $pay = !empty($d['pay']) ? $d['pay'] : 'pay';
        $orderstatus = !empty($d['orderstatus']) ? $d['orderstatus'] : 'all';
        $sendout = !empty($d['sendout']) ? $d['sendout'] : 'all';
        $refund = !empty($d['refund']) ? $d['refund'] : 'refund_not_all';
        $first = !empty($d['first']) ? $d['first'] : 'all';
        $pos = !empty($d['pos']) ? $d['pos'] : 0;

        //角色维度
        $doctorid = !empty($d['doctorid']) ? $d['doctorid'] : 0;
        $auditorid = !empty($d['auditorid']) ? $d['auditorid'] : 0;
        $auditorgroupid = !empty($d['auditorgroupid']) ? $d['auditorgroupid'] : 0;

        //时间维度
        $startdate = !empty($d['startdate']) ? $d['startdate'] : date("Y-m-d", (time() - 6 * 86400));
        $enddate = !empty($d['enddate']) ? $d['enddate'] : date('Y-m-d');
        $patientid = !empty($d['patientid']) ? $d['patientid'] : 0;
        $patient = Patient::getById($patientid);

        $cond = '';
        $bind = [];

        if ($type && $type != 'all') {
            $cond .= " and a.type=:type ";
            $bind[':type'] = $type;
        }

        if ($haveitem == 'haveitem') {
            $cond .= " and a.amount > a.express_price ";
        } elseif ($haveitem == 'noitem') {
            $cond .= " and a.amount = a.express_price ";
        }

        if ($pay != 'all') {
            $is_pay = ($pay == 'pay') ? 1 : 0;
            $cond .= " and a.is_pay=:is_pay ";
            $bind[':is_pay'] = $is_pay;
        }

        if ($orderstatus != 'all') {
            $arr = array(
                "unaudit" => 0,
                "pass" => 1,
                "refuse" => 2
            );
            $status = $arr[$orderstatus];
            $cond .= " and a.status=:status ";
            $bind[':status'] = $status;
        }

        if ($sendout != 'all') {
            $is_sendout = ($sendout == 'sendout') ? 1 : 0;
            $cond .= " and e.is_sendout=:is_sendout ";
            $bind[':is_sendout'] = $is_sendout;
        }

        if ($refund == 'refund_all') {
            // 全额退款
            $cond .= " and a.refund_amount = a.amount  ";
        } elseif ($refund == 'refund_part') {
            // 部分退款
            $cond .= " and a.refund_amount > 0 and a.amount > a.refund_amount ";
        } elseif ($refund == 'refund_not') {
            // 未退款
            $cond .= " and a.refund_amount = 0 and a.amount > a.refund_amount ";
        } elseif ($refund == 'refund_not_all') {
            // 未退款+部分退款
            $cond .= " and a.amount > a.refund_amount  ";
        }

        if ($first == 'first') {
            // 首单
            $cond .= " and a.pos = 1  ";
        } elseif ($first == 'other') {
            // 非首单
            $cond .= " and a.pos > 1 ";
        }

        if($pos > 0){
            $cond .= " and a.pos = :pos ";
            $bind[":pos"] = $pos;
        }

        if($doctorid > 0){
            $cond .= " and a.the_doctorid = :doctorid ";
            $bind[":doctorid"] = $doctorid;
        }

        if($auditorid > 0){
            $cond .= " and b.auditorid_market = :auditorid ";
            $bind[":auditorid"] = $auditorid;
        }

        if($auditorgroupid > 0){
            $auditorids = AuditorGroupRefDao::getAuditorIdsByAuditorGroupId($auditorgroupid);
            $auditoridsstr = implode(",", $auditorids);
            $cond .= " and b.auditorid_market in ( {$auditoridsstr} ) ";
        }

        if($pay == "unpay"){
            $time_str = "a.createtime";
        }else{
            $time_str = "a.time_pay";
        }

        if($startdate){
            $cond .= " and {$time_str} >= :startdate ";
            $bind[":startdate"] = $startdate;
        }

        if($enddate){
            $cond .= " and {$time_str} < :enddate ";
            $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));
        }

        if($diseasegroupid == 0){
            if($mydisease instanceof Disease){
                $cond .= " and c.diseaseid = :diseaseid ";
                $bind[":diseaseid"] = $mydisease->id;
            }
        }else{
            $cond .= " and d.diseasegroupid = :diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        $sql = "select distinct a.id
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    inner join doctordiseaserefs c on c.doctorid = b.id
                    inner join diseases d on d.id = c.diseaseid
                    left join shoppkgs e on e.shoporderid=a.id
                    where 1 = 1 {$cond} order by a.time_pay desc, a.id desc";
        $ids = Dao::queryValues($sql, $bind);
        return $ids;
    }
}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " jobid\n";
    exit(1);
}

$id = $argv[1];
if (!$id) {
    echo "jobid ($id) is empty\n";
    exit(2);
}

$obj = new Export_shoporder();
$obj->run($id);

Debug::flushXworklog();
