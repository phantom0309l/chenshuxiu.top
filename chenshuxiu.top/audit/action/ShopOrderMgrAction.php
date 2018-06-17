<?php

// ShopOrderMgrAction
class ShopOrderMgrAction extends AuditBaseAction
{

    public function doDefault() {
        return self::SUCCESS;
    }

    // 订单(申请单)列表
    public function doList() {
        $mydisease = $this->mydisease;
        $pagesize = XRequest::getValue("pagesize", 50);
        $pagenum = XRequest::getValue("pagenum", 1);
        $fuwu = XRequest::getValue("fuwu", 0);

        //状态分类筛选
        $diseasegroupid = XRequest::getValue('diseasegroupid', 0);
        $type = XRequest::getValue('type', 'all');
        $haveitem = XRequest::getValue('haveitem', 'haveitem');
        $pay = XRequest::getValue('pay', 'pay');
        $orderstatus = XRequest::getValue('orderstatus', 'all');
        $sendout = XRequest::getValue('sendout', 'all');
        $refund = XRequest::getValue('refund', 'refund_not_all');
        $first = XRequest::getValue('first', 'all');
        $pos = XRequest::getValue('pos', 0);

        //角色维度
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $auditorid = XRequest::getValue('auditorid', 0);
        $auditorgroupid = XRequest::getValue('auditorgroupid', 0);

        //时间维度
        $startdate = XRequest::getValue("startdate", date("Y-m-d", (time() - 6 * 86400)));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $patientid = XRequest::getValue('patientid', 0);
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
            $cond .= " and a.pos = 1 ";
        } elseif ($first == 'other') {
            // 非首单
            $cond .= " and a.pos > 1 ";
        }

        if ($pos > 0) {
            $cond .= " and a.pos = :pos ";
            $bind[":pos"] = $pos;
        }

        if ($doctorid > 0) {
            $cond .= " and a.the_doctorid = :doctorid ";
            $bind[":doctorid"] = $doctorid;
        }

        if ($auditorid > 0) {
            $cond .= " and b.auditorid_market = :auditorid ";
            $bind[":auditorid"] = $auditorid;
        }

        if ($auditorgroupid > 0) {
            $auditorids = AuditorGroupRefDao::getAuditorIdsByAuditorGroupId($auditorgroupid);
            $auditoridsstr = implode(",", $auditorids);
            $cond .= " and b.auditorid_market in ( {$auditoridsstr} ) ";
        }

        if ($pay == "unpay") {
            $time_str = "a.createtime";
        } else {
            $time_str = "a.time_pay";
        }

        if ($startdate) {
            $cond .= " and {$time_str} >= :startdate ";
            $bind[":startdate"] = $startdate;
        }

        if ($enddate) {
            $cond .= " and {$time_str} < :enddate ";
            $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));
        }

        if ($diseasegroupid == 0) {
            if ($mydisease instanceof Disease) {
                $cond .= " and c.diseaseid = :diseaseid ";
                $bind[":diseaseid"] = $mydisease->id;
            }
        } else {
            $cond .= " and d.diseasegroupid = :diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        //获得实体
        $sql = "select distinct a.*
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    inner join doctordiseaserefs c on c.doctorid = b.id
                    inner join diseases d on d.id = c.diseaseid
                    left join shoppkgs e on e.shoporderid=a.id
                    where 1 = 1 {$cond} order by a.time_pay desc, a.id desc";
        $shopOrders = Dao::loadEntityList4Page("ShopOrder", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopOrders", $shopOrders);

        //获得分页
        $countSql = "select count(distinct a.id)
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    inner join doctordiseaserefs c on c.doctorid = b.id
                    inner join diseases d on d.id = c.diseaseid
                    left join shoppkgs e on e.shoporderid=a.id
                    where 1=1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shopordermgr/list?diseasegroupid={$diseasegroupid}&type={$type}&haveitem={$haveitem}&pay={$pay}&orderstatus={$orderstatus}&sendout={$sendout}&refund={$refund}&first={$first}&doctorid={$doctorid}&auditorid={$auditorid}&auditorgroupid={$auditorgroupid}&startdate={$startdate}&enddate={$enddate}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        if ($patientid > 0) {
            $type = 'all';
            $haveitem = 'all';
            $pay = 'all';
            $orderstatus = 'all';
            $sendout = 'all';
            $diseasegroupid = 0;

            $cond = " and patientid=:patientid ";
            $bind = [];
            $bind[':patientid'] = $patientid;
            $shopOrders = Dao::getEntityListByCond('ShopOrder', $cond, $bind);
            XContext::setValue("shopOrders", $shopOrders);
            XContext::setValue("pagelink", null);
        }

        // 重新计算价格
        foreach ($shopOrders as $a) {
            $a->reCalcAmount();
        }

        //单数和销售额
        $row = $this->getSaleCntAndSaleAmount();
        $shop_order_cnt = $row["cnt"];
        $left_amount_yuan_all = sprintf("%.2f", $row["sale_amount"] / 100);

        XContext::setValue('diseasegroupid', $diseasegroupid);
        XContext::setValue('type', $type);
        XContext::setValue('haveitem', $haveitem);
        XContext::setValue('pay', $pay);
        XContext::setValue('orderstatus', $orderstatus);
        XContext::setValue('sendout', $sendout);
        XContext::setValue('refund', $refund);
        XContext::setValue('first', $first);
        XContext::setValue('pos', $pos);

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('auditorid', $auditorid);
        XContext::setValue('auditorgroupid', $auditorgroupid);
        XContext::setValue('startdate', $startdate);
        XContext::setValue('enddate', $enddate);

        XContext::setValue('patient', $patient);

        XContext::setValue('left_amount_yuan_all', $left_amount_yuan_all);
        XContext::setValue('shop_order_cnt', $shop_order_cnt);

        XContext::setValue('fuwu', $fuwu);

        if ($this->mydisease instanceof Disease) {
            $doctors = DoctorDao::getListByDiseaseid($this->mydisease->id);
        }
        XContext::setValue('doctors', $doctors);
        return self::SUCCESS;
    }

    public function doListOfPatient() {
        $patientid = XRequest::getValue("patientid", 0);

        $patient = Patient::getById($patientid);
        $shopOrders = ShopOrderDao::getShopOrdersByPatient($patient);

        XContext::setValue('patient', $patient);
        XContext::setValue('shopOrders', $shopOrders);
        return self::SUCCESS;
    }

    // 订单(申请单)列表
    public function doListForMarket() {
        $pagesize = XRequest::getValue("pagesize", 50);
        $pagenum = XRequest::getValue("pagenum", 1);

        //状态分类筛选
        $type = XRequest::getValue('type', 'all');

        $startdate = XRequest::getValue("startdate", date("Y-m-d", (time() - 6 * 86400)));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $cond = '';
        $bind = [];

        //商品类型
        if ($type && $type != 'all') {
            $cond .= " and a.type=:type ";
            $bind[':type'] = $type;
        }

        // 已支付订单
        $cond .= " and a.is_pay=1 ";

        // 未退款+部分退款
        $cond .= " and a.amount > a.refund_amount ";

        //当前运营的管辖医生
        $myauditor = $this->myauditor;

        $auditorid = $myauditor->id;
        if ($auditorid > 0) {
            $cond .= " and b.auditorid_market = :auditorid ";
            $bind[":auditorid"] = $auditorid;
        }

        if ($startdate) {
            $cond .= " and a.time_pay >= :startdate ";
            $bind[":startdate"] = $startdate;
        }

        if ($enddate) {
            $cond .= " and a.time_pay < :enddate ";
            $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));
        }

        //获得实体
        $sql = "select distinct a.*
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    where 1 = 1 {$cond} order by a.time_pay desc, a.id desc";
        $shopOrders = Dao::loadEntityList4Page("ShopOrder", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("shopOrders", $shopOrders);

        //获得分页
        $countSql = "select count(distinct a.id)
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    where 1=1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shopordermgr/list?type={$type}&startdate={$startdate}&enddate={$enddate}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        // 重新计算价格
        foreach ($shopOrders as $a) {
            $a->reCalcAmount();
        }

        //实收, 不包含运费
        $left_amount_yuan_all = 0;
        $sql = "select distinct a.id
                    from shoporders a
                    inner join doctors b on b.id = a.the_doctorid
                    where 1=1 {$cond}";
        $ids = Dao::queryValues($sql, $bind);
        $shop_order_cnt = count($ids);
        foreach ($ids as $id) {
            $shopOrder = ShopOrder::getById($id);
            if (false == $shopOrder->isValid()) {
                continue;
            }
            //实收, 不包含运费
            $left_amount_yuan_all += $shopOrder->getLeft_amount_yuan() - $shopOrder->getExpress_price_yuan();
        }

        XContext::setValue('type', $type);
        XContext::setValue('startdate', $startdate);
        XContext::setValue('enddate', $enddate);

        XContext::setValue('left_amount_yuan_all', $left_amount_yuan_all);
        XContext::setValue('shop_order_cnt', $shop_order_cnt);
        return self::SUCCESS;
    }

    // 运营审核订单(申请单)列表
    public function doListForAudit() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $patientid = XRequest::getValue("patientid", 0);

        //状态分类筛选
        $hasrecipe = XRequest::getValue('hasrecipe', 'no');
        $audit_status = XRequest::getValue('audit_status', 'no');

        //支付时间维度
        $startdate = XRequest::getValue("startdate", date("Y-m-d", (time() - 7 * 86400)));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $cond = '';
        $bind = [];

        if ($hasrecipe != 'all') {
            if ('yes' == $hasrecipe) {
                $cond .= " and recipeid>0 ";
            }
            if ('no' == $hasrecipe) {
                $cond .= " and recipeid=0 ";
            }
        }

        if ($audit_status != 'all') {
            if ('yes' == $audit_status) {
                $cond .= " and audit_status=1 ";
            }
            if ('no' == $audit_status) {
                $cond .= " and audit_status=0 ";
            }
        }

        if ($startdate) {
            $cond .= " and time_pay >= :startdate ";
            $bind[":startdate"] = $startdate;
        }

        if ($enddate) {
            $cond .= " and time_pay < :enddate ";
            $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));
        }

        if ($patientid) {
            $cond .= " and patientid = :patientid ";
            $bind[":patientid"] = $patientid;
        }

        $cond .= " and type='chufang' and status=1 ";

        $shopOrders = Dao::getEntityListByCond4Page("ShopOrder", $pagesize, $pagenum, $cond, $bind);
        XContext::setValue("shopOrders", $shopOrders);

        //获得分页
        $countSql = "select count(id) from shoporders where 1=1 {$cond}";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/shopordermgr/listforaudit?patientid={$patientid}&hasrecipe={$hasrecipe}&audit_status={$audit_status}&startdate={$startdate}&enddate={$enddate}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('patientid', $patientid);
        XContext::setValue('hasrecipe', $hasrecipe);
        XContext::setValue('audit_status', $audit_status);
        XContext::setValue('startdate', $startdate);
        XContext::setValue('enddate', $enddate);

        return self::SUCCESS;
    }

    public function doOneForAudit() {
        $shoporderid = XRequest::getValue("shoporderid", 0);
        $shoporder = ShopOrder::getById($shoporderid);
        $patient = $shoporder->patient;

        $cond = " and patientid = :patientid order by thedate desc";
        $bind[":patientid"] = $patient->id;

        $recipe = Dao::getEntityByCond("Recipe", $cond, $bind);

        XContext::setValue("shoporder", $shoporder);
        XContext::setValue("recipe", $recipe);
        XContext::setValue("patient", $patient);
        return self::SUCCESS;
    }

    public function doBindJson() {
        $shoporderid = XRequest::getValue("shoporderid", 0);
        $recipeid = XRequest::getValue("recipeid", 0);
        $shoporder = ShopOrder::getById($shoporderid);
        $recipe = Recipe::getById($recipeid);

        if ($shoporder instanceof ShopOrder && $recipe instanceof Recipe) {
            $shoporder->recipeid = $recipeid;
            echo "success";
            return self::BLANK;
        }

        echo "false";
        return self::BLANK;
    }

    public function doPassJson() {
        $shoporderid = XRequest::getValue("shoporderid", 0);
        $shoporder = ShopOrder::getById($shoporderid);

        $shoporder->audit_status = 1;

        echo "success";
        return self::BLANK;
    }

    public function doRemarkJson() {
        $shoporderid = XRequest::getValue("shoporderid", 0);
        $audit_remark = XRequest::getValue("audit_remark", '');
        $shoporder = ShopOrder::getById($shoporderid);

        $shoporder->audit_remark = $audit_remark;

        if ($shoporder->recipeid > 0) {
            $recipe = $shoporder->recipe;
            $recipe->remark = $audit_remark;
        }

        echo "success";
        return self::BLANK;
    }

    //导出市场报表
    public function doListOutput() {
        $this->result = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => '');

        $this->createExportJobAndTriggerNSQ("shoporder_market");
        return self::TEXTJSON;
    }

    //导出订单明细
    public function doListOutputShopOrderDetail() {
        $this->result = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => '');

        $this->createExportJobAndTriggerNSQ("shoporder_detail");
        return self::TEXTJSON;
    }

    //导出服务1
    public function doListOutputService() {
        $this->result = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => '');

        $this->createExportJobAndTriggerNSQ("shoporder_service");
        return self::TEXTJSON;
    }

    //导出服务2
    public function doListOutputService2() {
        $this->result = array(
            'errno' => 0,
            'errmsg' => '',
            'data' => '');

        $this->createExportJobAndTriggerNSQ("shoporder_service2");
        return self::TEXTJSON;
    }

    private function createExportJobAndTriggerNSQ($type) {
        $myauditor = $this->myauditor;
        $cnf = XRequest::getValue('cnf', '');
        DBC::requireNotEmpty($cnf, '配置项不能为空');
        $cnt = Export_JobDao::getActiveJobCntByAuditorid($myauditor->id);
        DBC::requireTrue($cnt < 2, '同时只能运行2个导出任务');

        $row = [];
        $row['type'] = $type;
        $row['data'] = json_encode($cnf, JSON_UNESCAPED_UNICODE);
        $row['auditorid'] = $myauditor->id;

        $export_job = Export_Job::createByBiz($row);
        $job = Job::getInstance();
        $job->doBackground('export_shoporder', $export_job->id);
        return $export_job;
    }

    private function getSaleCntAndSaleAmount() {
        $mydisease = $this->mydisease;
        //状态分类筛选
        $diseasegroupid = XRequest::getValue('diseasegroupid', 0);
        $type = XRequest::getValue('type', 'all');
        $haveitem = XRequest::getValue('haveitem', 'haveitem');
        $pay = XRequest::getValue('pay', 'pay');
        $orderstatus = XRequest::getValue('orderstatus', 'all');
        $sendout = XRequest::getValue('sendout', 'all');
        $refund = XRequest::getValue('refund', 'refund_not_all');
        $first = XRequest::getValue('first', 'all');
        $pos = XRequest::getValue('pos', 0);

        //角色维度
        $doctorid = XRequest::getValue('doctorid', 0);
        $auditorid = XRequest::getValue('auditorid', 0);
        $auditorgroupid = XRequest::getValue('auditorgroupid', 0);

        //时间维度
        $startdate = XRequest::getValue("startdate", date("Y-m-d", (time() - 6 * 86400)));
        $enddate = XRequest::getValue("enddate", date('Y-m-d'));

        $patientid = XRequest::getValue('patientid', 0);
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

        if ($pos > 0) {
            $cond .= " and a.pos = :pos ";
            $bind[":pos"] = $pos;
        }

        if ($doctorid > 0) {
            $cond .= " and a.the_doctorid = :doctorid ";
            $bind[":doctorid"] = $doctorid;
        }

        if ($auditorid > 0) {
            $cond .= " and b.auditorid_market = :auditorid ";
            $bind[":auditorid"] = $auditorid;
        }

        if ($auditorgroupid > 0) {
            $auditorids = AuditorGroupRefDao::getAuditorIdsByAuditorGroupId($auditorgroupid);
            $auditoridsstr = implode(",", $auditorids);
            $cond .= " and b.auditorid_market in ( {$auditoridsstr} ) ";
        }

        if ($pay == "unpay") {
            $time_str = "a.createtime";
        } else {
            $time_str = "a.time_pay";
        }

        if ($startdate) {
            $cond .= " and {$time_str} >= :startdate ";
            $bind[":startdate"] = $startdate;
        }

        if ($enddate) {
            $cond .= " and {$time_str} < :enddate ";
            $bind[":enddate"] = date("Y-m-d", (strtotime($enddate) + 86400));
        }

        if ($diseasegroupid == 0) {
            if ($mydisease instanceof Disease) {
                $cond .= " and c.diseaseid = :diseaseid ";
                $bind[":diseaseid"] = $mydisease->id;
            }
        } else {
            $cond .= " and d.diseasegroupid = :diseasegroupid ";
            $bind[":diseasegroupid"] = $diseasegroupid;
        }

        $sql = "select
                    count(distinct t1.id) as cnt, sum(t1.amount - t1.refund_amount) as sale_amount
                from (
                    select
                    a.id, a.amount, a.refund_amount
                    from
                    shoporders a inner join doctors b on b.id = a.the_doctorid
                    inner join doctordiseaserefs c on c.doctorid = b.id
                    inner join diseases d on d.id = c.diseaseid
                    left join shoppkgs e on e.shoporderid = a.id
                    where 1 = 1 {$cond} group by a.id order by a.time_pay desc, a.id desc
                )t1";
        return Dao::queryRow($sql, $bind);
    }

    // 订单详情
    public function doOne() {
        $shoporderid = XRequest::getValue('shoporderid', 0);

        $shopOrder = ShopOrder::getById($shoporderid);
        $shopOrderItems = $shopOrder->getShopOrderItems();

        $shopPkgs = $shopOrder->getShopPkgs();

        $shopOrder->reCalcAmount();

        if ($shopOrder->user instanceof User) {
            $userRmbAccount = $shopOrder->user->getAccount('user_rmb');
        }

        $shopOrderPictures = array();
        if ($shopOrder->isWeituo()) {
            $shopOrderPictures = ShopOrderPictureDao::getListByShopOrder($shopOrder);
        }

        XContext::setValue('userRmbAccount', $userRmbAccount);
        XContext::setValue('shopOrder', $shopOrder);
        XContext::setValue('shopOrderItems', $shopOrderItems);
        XContext::setValue('shopOrderPictures', $shopOrderPictures);

        XContext::setValue('shopPkgs', $shopPkgs);

        $isXP = false;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $regex = '/Windows NT 5.1/';
        if (preg_match($regex, $agent, $match)) {
            $isXP = true;
        }
        XContext::setValue('isXP', $isXP);

        return self::SUCCESS;
    }

    // 订单修改配送费
    public function doModifyExpress_pricePost() {
        $shoporderid = XRequest::getValue('shoporderid', 0);
        $express_price_yuan = XRequest::getValue('express_price_yuan', 0);

        $shopOrder = ShopOrder::getById($shoporderid);
        $shopOrder->express_price = $express_price_yuan * 100;

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shoporderid}&preMsg=" . urlencode("配送价格已修改为{$express_price_yuan}元"));

        return self::SUCCESS;
    }

    // 手动审核通过订单
    public function doPassPost() {
        $shoporderid = XRequest::getValue('shoporderid', 0);

        $shopOrder = ShopOrder::getById($shoporderid);
        $shopOrder->pass();

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shoporderid}&preMsg=" . urlencode("审核通过"));

        return self::SUCCESS;
    }

    // 手动审核通过订单
    public function doRefusePost() {
        $shoporderid = XRequest::getValue('shoporderid', 0);

        $shopOrder = ShopOrder::getById($shoporderid);
        $shopOrder->refuse();

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shoporderid}&preMsg=" . urlencode("审核拒绝"));

        return self::SUCCESS;
    }

    // 退款至余额
    public function doRefundToAccountPost() {
        $shoporderid = XRequest::getValue('shoporderid', 0);
        $shopOrder = ShopOrder::getById($shoporderid);

        $amount_yuan = XRequest::getValue('amount_yuan', 0);
        DBC::requireTrue($amount_yuan > 0, "退款额度不能为0");
        $remark = XRequest::getValue('remark', '');

        $shopOrder->refund($amount_yuan * 100, $remark);

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shoporderid}&preMsg=" . urlencode("订单[{$shopOrder->id}]退款至余额"));

        return self::SUCCESS;
    }

    //余额支付
    public function doBalancePayPost() {
        $shoporderid = XRequest::getValue('shoporderid', 0);
        $shopOrder = ShopOrder::getById($shoporderid);

        $rmbAccount = Account::getByUserAndCode($shopOrder->user, 'user_rmb');
        $shopOrder->tryPay($rmbAccount);

        XContext::setJumpPath("/shopordermgr/one?shoporderid={$shoporderid}&preMsg=" . urlencode("余额支付成功"));

        return self::SUCCESS;

    }

    // 修改is_lead_by_auditor
    public function doChangeLeadAuditorJson() {
        $shoporderid = XRequest::getValue('shoporderid', 0);
        $is_lead_by_auditor = XRequest::getValue('is_lead_by_auditor', 0);

        $shoporder = ShopOrder::getById($shoporderid);
        $shoporder->is_lead_by_auditor = $is_lead_by_auditor;

        echo 'success';

        return self::BLANK;
    }

    public function doPkgJson() {
        $shopOrderId = XRequest::getValue('shoporderid', 0);
        $shopOrder = ShopOrder::getById($shopOrderId);
        DBC::requireTrue($shopOrder instanceof ShopOrder, "未找到订单【shoporderid={$shopOrderId}】");

        DBC::requireTrue($shopOrder->canPkg(), "该订单没有剩余可配商品【shoporderid={$shopOrderId}】");

        $row = array();
        $row["wxuserid"] = $shopOrder->wxuserid;
        $row["userid"] = $shopOrder->userid;
        $row["patientid"] = $shopOrder->patientid;
        $row["shoporderid"] = $shopOrder->id;
        $row["status"] = $shopOrder->status;

        $shopPkg = ShopPkg::createByBiz($row);
        $shopPkg->fangcun_platform_no = $shopPkg->id;

        $shopPkgs = $shopOrder->getShopPkgs();
        $shopPkgs[] = $shopPkg;
        ShopPkgService::reCalcuExpressPrice($shopOrder, $shopPkgs);

        $shopPkgItems = [];
        $shopOrderItems = $shopOrder->getShopOrderItems();
        foreach ($shopOrderItems as $shopOrderItem) {
            if($shopOrderItem->getCanPkgCnt() > 0){
                $row = array();
                $row["shoppkgid"] = $shopPkg->id;
                $row["shopproductid"] = $shopOrderItem->shopproductid;
                $row["price"] = $shopOrderItem->price;
                $row["cnt"] = $shopOrderItem->getCanPkgCnt();

                $shopPkgItems[] = ShopPkgItem::createByBiz($row);
            }
        }
        //配送单是否需要推送erp设置
        $shopPkg->need_push_erpSet($shopPkgItems);
        //重新设置物流公司
        $shopPkg->express_company = ShopPkgService::getExpress_company($shopPkgItems);

        XContext::setValue('json', $this->result);
        return self::TEXTJSON;
    }
}
