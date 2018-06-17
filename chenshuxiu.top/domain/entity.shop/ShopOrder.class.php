<?php

/*
 * ShopOrder
 */

class ShopOrder extends Entity implements PayHandle
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    // 处方类型
    const type_chufang = 'chufang';

    // 委托
    const type_weituo = 'weituo';

    // 购物订单
    const type_shopping = 'shopping';

    public static function getKeysDefine() {
        return array(
            'wxuserid',  // wxuserid
            'userid',  // userid
            'patientid',  // patientid
            'the_doctorid',  // 生成订单时的doctorid
            'recipeid',  // recipeid
            'shopaddressid',  // shopaddressid
            'type',  // chufang 处方, shopping 购物
            'item_sum_price',  // 明细金额合计, 单位分
            'express_price',  // 配送费, 单位分
            'express_price_real',  // 实际真实配送费，单位分
            'is_goodsout',  // 是否出库（0：未出库 1：已出库）
            'is_sendout',  // 是否发货（0：未发货 1：已经发货）
            'express_company',  // 快递公司
            'express_no',  // 快递号
            'time_goodsout',  // 出库时间
            'time_sendout',  // 发货时间
            'eorder_content',  // 电子运单接口成功后返回信息
            'need_push_erp',  // 是否需要推送到erp（0：不推送  1：推送）
            'is_push_erp',  // 是否已推送到erp（0：未推送 1：已推送）
            'time_push_erp',  // 推送到erp的时间
            'remark_push_erp',  // 推送remark
            'guahao_price',  // 挂号费，单位分
            'amount',  // 订单总金额,包括配送费, 单位分
            'refund_amount',  // 已退款金额, 单位分
            'is_pay',  // 已经支付 0:未支付 1：已支付
            'jifen_amount',  // 换算成积分, 单位分
            'is_settle',  // 已经结算
            'is_lead_by_auditor',  // 是否运营转化
            'invoice_no', // 发票号
            'time_pay',  // 支付时间
            'time_refund',  // 退款时间
            'time_pass',  // 通过时间
            'time_refuse',  // 拒绝时间
            'pos',  // 第几单
            'audit_status',  // 运营审核状态
            'status',  // chufang: 0 待医师审核, 1 医师审核通过, 2 医师审核拒绝 ;
            // weituo: 0 待审核, 1 审核通过, 2 审核拒绝 ;
            // shopping: 0 无效, 1 有效
            'remark',  // 用户备注
            'audit_remark',  // 运营备注
        );
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array('wxuserid', 'userid', 'patientid', 'amount');
    }

    protected function init_belongtos() {
        $this->_belongtos = array();

        $this->_belongtos["wxuser"] = array(
            "type" => "WxUser",
            "key" => "wxuserid"
        );
        $this->_belongtos["user"] = array(
            "type" => "User",
            "key" => "userid"
        );
        $this->_belongtos["patient"] = array(
            "type" => "Patient",
            "key" => "patientid"
        );
        $this->_belongtos["thedoctor"] = array(
            "type" => "Doctor",
            "key" => "the_doctorid"
        );
        $this->_belongtos["recipe"] = array(
            "type" => "Recipe",
            "key" => "recipeid"
        );
        $this->_belongtos["shopaddress"] = array(
            "type" => "ShopAddress",
            "key" => "shopaddressid"
        );
    }

    // $row = array();
    // $row["patientid"] = $patientid;
    // $row["amount"] = $amount;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "ShopOrder::createByBiz row cannot empty");

        $default = array();
        $default["wxuserid"] = 0;
        $default["userid"] = 0;
        $default["patientid"] = 0;
        $default["the_doctorid"] = 0;
        $default["recipeid"] = 0;
        $default["shopaddressid"] = 0;
        $default["type"] = ShopOrder::type_shopping;
        $default["item_sum_price"] = 0;
        $default["express_price"] = 0;
        $default["express_price_real"] = 0;
        $default["is_goodsout"] = 0;
        $default["is_sendout"] = 0;
        $default["express_company"] = '';
        $default["express_no"] = '';
        $default["time_goodsout"] = '0000-00-00 00:00:00';
        $default["time_sendout"] = '0000-00-00 00:00:00';
        $default["eorder_content"] = '';
        $default["need_push_erp"] = 0;
        $default["is_push_erp"] = 0;
        $default["time_push_erp"] = '0000-00-00 00:00:00';
        $default["remark_push_erp"] = 0;
        $default["guahao_price"] = 0;
        $default["amount"] = 0;
        $default["refund_amount"] = 0;
        $default["is_pay"] = 0;
        $default["jifen_amount"] = 0;
        $default["is_settle"] = 0;
        $default["is_lead_by_auditor"] = 0;
        $default["invoice_no"] = '';
        $default["time_pay"] = '0000-00-00 00:00:00';
        $default["time_refund"] = '0000-00-00 00:00:00';
        $default["time_pass"] = '0000-00-00 00:00:00';
        $default["time_refuse"] = '0000-00-00 00:00:00';
        $default["pos"] = 0;
        $default["audit_status"] = 0; // 运营审核状态
        $default["status"] = 0; // (shopping)0:无效 1:有效 或 (chufang)0:待医师审核
        // 1:医生已确认 2:医生已拒绝 3:系统自动拒绝
        $default["remark"] = '';
        $default["audit_remark"] = '';

        $row += $default;
        $shoporder = new self($row);

        // #5658 有过购药的患者等级提升为LEVEL_300 (前提：患者当前等级必须小于购药等级)
        if ($shoporder->patient instanceof Patient) {
            $patient = $shoporder->patient;
            if ($patient->level < PatientLevel::LEVEL_300) {
                $patient->level = PatientLevel::LEVEL_300;
            }
        }

        return $shoporder;
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function canPkg() {
        $shopOrderItems = $this->getShopOrderItems();
        foreach ($shopOrderItems as $shopOrderItem) {
            if($shopOrderItem->getCanPkgCnt() > 0){
                return true;
            }
        }
        return false;
    }

    public function getExpressCompanyStrs() {
        $shopPkgs = $this->getShopPkgs();
        $express_companys = [];
        foreach ($shopPkgs as $shopPkg) {
            $express_companys[] = $shopPkg->express_company;
        }
        return implode(',', $express_companys);
    }

    public function getExpressNos() {
        $shopPkgs = $this->getShopPkgs();
        $express_nos = [];
        foreach ($shopPkgs as $shopPkg) {
            $express_nos[] = $shopPkg->express_no;
        }
        return implode(',', $express_nos);
    }

    //是否有shoporderitem
    public function haveValidItem() {
        $shopOrderItems = $this->getShopOrderItems();
        return count($shopOrderItems) > 0;
    }

    public function isGoodsOutAll() {
        $shopPkgs = ShopPkgDao::getListByShopOrder($this);
        foreach ($shopPkgs as $shopPkg) {
            if (0 == $shopPkg->is_goodsout) {
                return false;
            }
        }
        return true;
    }

    public function isChufang() {
        return ShopOrder::type_chufang == $this->type;
    }

    public function isWeituo() {
        return ShopOrder::type_weituo == $this->type;
    }

    public function isShopping() {
        return ShopOrder::type_shopping == $this->type;
    }

    //当前运费超过了默认运费
    public function isGtDefaultFreight() {
        return $this->express_price > FreightService::getDefaultFreight();
    }

    // 通过订单
    public function pass() {
        $this->status = 1;
        $this->time_pass = date("Y-m-d H:i:s", time());

        $shopPkgs = ShopPkgDao::getListByShopOrder($this);
        foreach ($shopPkgs as $shopPkg) {
            $shopPkg->status = 1;
        }
    }

    // 拒绝订单
    public function refuse() {
        $this->status = 2;
        $this->time_refuse = date("Y-m-d H:i:s", time());
    }

    // 系统自动拒绝订单
    public function refuseBySys() {
        $this->status = 3;
        $this->time_refuse = date("Y-m-d H:i:s", time());
    }

    public function isValid() {
        // 已支付, 未退款, 状态有效
        return $this->is_pay && ($this->getLeft_amount() > 0) && ($this->status == 1);
    }

    // chufang 处方, shopping 购物
    public function getTypeDesc() {
        $arr = CtrHelper::getShopOrderTypeCtrArray();

        return $arr[$this->type];
    }

    public function getWxPayUnifiedOrder_Body() {
        $str = "订单ID" . $this->id;
        return $str;
    }

    public function getWxPayUnifiedOrder_Attach() {
        $shopOrderItems = $this->getShopOrderItems();
        $cnt = count($shopOrderItems);
        $str = "购买了{$cnt}件物品";
        return $str;
        foreach ($this->getShopOrderItems() as $a) {
            $str .= "{$a->cnt} {$a->shopproduct->pack_unit} {$a->shopproduct->title}\n";
        }
        $str = trim($str);
        return $str;
    }

    public function getPayAmount() {
        return $this->amount;
    }

    // 修改金额
    public function fixAmount($amount) {
        $this->set4lock('amount', $amount);
    }

    // 修正 wxuserid, userid
    public function fix_wxuserid_userid(WxUser $wxuser, User $user) {
        $this->set4lock('wxuserid', $wxuser->id);
        $this->set4lock('userid', $user->id);
    }

    // 重新计算金额
    public function reCalcAmount() {
        // 支付过的订单,不能重新计算金额
        if ($this->is_pay) {
            return false;
        }

        $shopOrderItems = ShopOrderItemDao::getShopOrderItemsByShopOrder($this);

        $amount = 0;
        foreach ($shopOrderItems as $a) {
            // 修正单价
            $a->fixPrice();

            $amount += $a->getAmount();
        }

        // 明细金额合计
        $this->item_sum_price = $amount;

        // 加上快递费
        //重算运费
        $this->reCalcExpress_price();
        $amount += $this->express_price;

        // 加上挂号费
        $amount += $this->guahao_price;

        if ($this->amount != $amount) {
            // Debug::warn("ShopOrder[{$this->id}]->fixAmount({$this->amount} =>
            // {$amount})");
            $this->fixAmount($amount);
        }
    }

    // 重新计算运费
    public function reCalcExpress_price() {
        $freight = FreightService::getFreight($this);
        $this->express_price = $freight;
    }

    // 商品金额 (元), 明细金额汇总
    public function getItem_sum_price_yuan() {
        return sprintf("%.2f", $this->item_sum_price / 100);
    }

    // 快递费 (元)
    public function getExpress_price_yuan() {
        return sprintf("%.2f", $this->express_price / 100);
    }

    // 真实快递费 (元)
    public function getExpress_price_real_yuan() {
        return sprintf("%.2f", $this->express_price_real / 100);
    }

    // 挂号费 (元)
    public function getGuahao_price_yuan() {
        return sprintf("%.2f", $this->guahao_price / 100);
    }

    // 总金额 (元), 包括快递费
    public function getAmount_yuan() {
        return sprintf("%.2f", $this->amount / 100);
    }

    // 订单退款金额 (元)
    public function getRefund_amount_yuan() {
        return sprintf("%.2f", $this->refund_amount / 100);
    }

    // 剩余金额 (分)
    public function getLeft_amount() {
        return $this->amount - $this->refund_amount;
    }

    // 剩余金额 (元)
    public function getLeft_amount_yuan() {
        return sprintf("%.2f", $this->getLeft_amount() / 100);
    }

    // 服务金额汇总
    public function getService_yuan() {
        $amount = 0;
        foreach ($this->getShopOrderItems() as $shopOrderItem) {
            $amount += $shopOrderItem->getService_yuan();
        }
        return $amount;
    }

    // 是否支付
    public function getIs_payStr() {
        return $this->is_pay ? '<span class="green">已支付</span>' : '<span class="gray">未支付</span>';
    }

    // 是否退款
    public function getRefundStr() {
        if (!$this->is_pay) {
            return '--';
        }

        $str = "";

        if ($this->getLeft_amount() < 1) {
            $str = "<span class='green'>全额退款</span>";
        } elseif ($this->refund_amount > 0) {
            $str = "<span class='green'>已退款(" . $this->getRefund_amount_yuan() . ")元</span>";
        } else {
            $str = '<span class="gray">未退款</span>';
        }

        return $str;
    }

    // 获取对应处方
    public function getPrescription() {
        if ($this->type != ShopOrder::type_chufang) {
            return null;
        }
        return PrescriptionDao::getPrescriptionByShopOrder($this);
    }

    // 订单主状态
    public function getStatusStr() {
        if (!$this->is_pay) {
            return "<span class='gray'>未支付</span>";
        }

        if ($this->getLeft_amount() < 1) {
            return "<span class='gray'>全额退款</span>";
        }

        $str = '';

        if ($this->type == ShopOrder::type_chufang) {
            if ($this->status == 0) {
                $str = "<span class='red'>待医师确认</span>";
            } elseif ($this->status == 1) {
                $str = "<span class='green'>医生已确认</span>";
            } elseif ($this->status == 2) {
                $str = "<span class='red'>医生已拒绝</span>";
            } elseif ($this->status == 3) {
                $str = "<span class='red'>系统自动拒绝</span>";
            }
        } else {
            if ($this->status == 0) {
                $str = "<span class='red'>无效</span>";
            } else {
                $str = "<span class='green'>有效</span>";
            }
        }

        return $str;
    }

    public function getOrderStatusStr() {
        if (!$this->is_pay) {
            return "未支付";
        }

        if ($this->getLeft_amount() < 1) {
            return "全额退款";
        }

        $status = $this->status;
        $str = ["审核中", "成功", "未通过"][$status];

        return $str ? $str : '';
    }

    // 是否结算
    public function getIs_settleStr() {
        if (!$this->is_pay) {
            return '--';
        }
        return $this->is_settle ? '已结算' : '未结算';
    }

    // 支付成功时间
    public function getTime_payStr() {
        return "0000-00-00 00:00:00" == $this->time_pay ? '--' : substr($this->time_pay, 0, 10);
    }

    // 审核通过时间
    public function getTime_passStr() {
        return "0000-00-00 00:00:00" == $this->time_pass ? '--' : substr($this->time_pass, 0, 10);
    }

    // 审核拒绝时间
    public function getTime_refuseStr() {
        return "0000-00-00 00:00:00" == $this->time_refuse ? '--' : substr($this->time_refuse, 0, 10);
    }

    // 退款, 已支付状态
    public function refund($amount, $remark = '') {
        if (false == $this->is_pay) {
            return false;
        }

        $left_amount = $this->getLeft_amount();

        if ($left_amount < 1) {
            return false;
        }

        DBC::requireTrue($amount <= $left_amount, "退款金额:{$amount}不能超过,可退金额:{$left_amount}");

        if ($amount < 0) {
            $amount = 0;
            return false;
        }

        $this->refund_amount += $amount;

        if (empty($remark)) {
            $remark = "申请单[{$this->id}]退款至余额";
            if ($this->type == ShopOrder::type_shopping) {
                $remark = "订单[{$this->id}]退款至余额";
            }
        }

        $sysAccount = Account::getSysAccount('sys_user_shop_out');
        $userRmbAccount = $this->user->getAccount('user_rmb');

        // 部分退款
        if ($amount < $this->amount) {
            $cnt = $this->getRefundAccountTransCnt();
            $code = "refund:" . ($cnt + 1);
        } else {
            $code = 'refund';
        }

        $sysAccount->transto($userRmbAccount, $amount, $this, $code, $remark);

        $this->time_refund = XDateTime::now();

        //退款，将所有的配送单置为无效状态
        $shopPkgs = ShopPkgDao::getListByShopOrder($this);
        foreach ($shopPkgs as $shopPkg) {
            $shopPkg->status = 0;
        }

        return true;
    }

    public function getRefundAccountTransCnt() {
        $sql = "select count(*) from accounttranss where objtype='ShopOrder' and objid=:objid and code like 'refund%';";
        $bind = [];
        $bind[':objid'] = $this->id;
        return 0 + Dao::queryValue($sql, $bind);
    }

    // 账务事务数组
    public function getAccountTransArray() {
        return AccountTrans::getArrayOfObj($this);
    }

    // 获取有效的充值单
    public function getRechargeDepositeOrder() {
        return DepositeOrderDao::getDepositeOrderRechargeOneByEntity($this);
    }

    // 配送单列表
    public function getShopPkgs() {
        return ShopPkgDao::getListByShopOrder($this);
    }

    // 订单数目
    public function getShopOrderItemCnt() {
        return ShopOrderItemDao::getShopOrderItemCntByShopOrder($this);
    }

    // 订单商品总数
    public function getShopProductSumCnt() {
        return ShopOrderItemDao::getShopProductSumCntByShopOrder($this);
    }

    // 订单明细列表
    public function getShopOrderItems() {
        return ShopOrderItemDao::getShopOrderItemsByShopOrder($this);
    }

    // 订单明细（缺货）列表
    public function getShopOrderItem_lacks() {
        return ShopOrderItem_lackDao::getListByShopOrder($this);
    }

    // 获取订单项
    public function getShopOrderItemByShopProduct(ShopProduct $shopProduct) {
        foreach ($this->getShopOrderItems() as $a) {
            if ($a->shopproductid == $shopProduct->id) {
                return $a;
            }
        }

        return null;
    }

    // 获取订单包含的产品
    public function getShopProducts() {
        $arr = array();
        foreach ($this->getShopOrderItems() as $a) {
            $arr[] = $a->shopproduct;
        }

        return $arr;
    }

    // 获取订单对应商品title字符串
    public function getShopProductTitleStr() {
        $str = "";
        foreach ($this->getShopOrderItems() as $a) {
            $shopProduct = $a->shopproduct;
            $left_cntOfReal = $shopProduct->getLeft_cntOfReal() - $a->cnt;
            $str .= "\n" . $shopProduct->title . "[{$left_cntOfReal}]";
        }
        return $str;
    }

    // 获取今天支付成功的订单数
    public function getIs_payCntOfToday() {
        $the_date = date("Y-m-d");
        return ShopOrderDao::getShopOrderCntByTime_paydate($the_date);
    }

    // 获取商品title
    public function getTitleOfShopProducts($split = "|") {
        $title_arr = [];
        foreach ($this->getShopProducts() as $shopProduct) {
            $title_arr[] = $shopProduct->title;
        }
        return implode($split, $title_arr);
    }

    // 获取商品title 包括商品数量
    public function getTitleAndCntOfShopProducts($split = "\n") {
        $title_arr = [];
        foreach ($this->getShopOrderItems() as $a) {
            $shopProduct = $a->shopproduct;
            $cnt = $a->cnt;
            $back_cnt = $a->getHasGoodsBackCnt();
            $cnt = $cnt - $back_cnt;
            $title_arr[] = $shopProduct->title . "[{$cnt}]";
        }
        return implode($split, $title_arr);
    }

    // 尝试支付
    public function tryPay(Account $rmbAccount) {
        $shopOrder = $this;
        // 尚未支付, 去支付
        if (0 == $shopOrder->is_pay) {
            // 判断库存是否满足
            if (false == $shopOrder->checkStock()) {
                Debug::warn("shopOrder[{$shopOrder->id}]该订单因库存不足，提醒运营补货");
            }

            if ($rmbAccount->balance >= $shopOrder->amount) {
                $sysAccount = Account::getSysAccount('sys_user_shop_out');

                $remark = "申请单[{$shopOrder->id}]预付";
                if ($shopOrder->type == ShopOrder::type_shopping) {
                    $remark = "订单[{$shopOrder->id}]支付";
                }

                $rmbAccount->transto($sysAccount, $shopOrder->amount, $shopOrder, 'pay', $remark);
                $shopOrder->is_pay = 1;
                $shopOrder->time_pay = XDateTime::now();
                $shopOrder->the_doctorid = $shopOrder->patient->doctorid;

                $pos = ShopOrderDao::getIsPayShopOrderCntByPatient($shopOrder->patient) + 1;
                $shopOrder->pos = $pos;

                //生成任务
                $shopOrder->createShopOrderOpTask();

                //关闭未支付跟进任务
                $optask = OpTaskDao::getOneByObjUnicode($shopOrder, "shoporder:notpay");
                if ($optask instanceof OpTask) {
                    $optask->close();
                }

                // 生成方寸处方
                $shopOrder->createPrescription();

                //如果有赠品生成赠品shoporderitem
                $shopOrder->tryAddGift();

                //生成配送单和配送单明细
                ShopPkgService::createDefaultShopPkgAndItemsByShopOrder($shopOrder);

                $is_payCntOfToday = $shopOrder->getIs_payCntOfToday() + 1;
                $shopProductTitleStr = $shopOrder->getShopProductTitleStr();

                $content = "Price[{$shopOrder->amount}]Patient[{$shopOrder->patient->name}]Doctor[{$shopOrder->patient->doctor->name}]成功支付订单,ShopOrder[{$shopOrder->id}]\nTodayCnt[{$is_payCntOfToday}]{$shopProductTitleStr}";
                PushMsgService::sendMsgToAuditorBySystem('ShopOrder', 1, $content);

                //假期提醒
                $shopOrder->holidayNotice();

                // 入流
                $pipe = Pipe::createByEntity($shopOrder, 'pay', $shopOrder->wxuserid);
                $pcard = $shopOrder->patient->getMasterPcard();
                $pcard->has_update = 1;
            } else {
                Debug::warn("ShopOrder[{$shopOrder->id}]支付失败, 余额不足, {$rmbAccount->balance} < {$shopOrder->amount}");
            }
        } else {
            Debug::warn("ShopOrder[{$shopOrder->id}]已支付了, 不用再支付了");
        }
    }

    public function holidayNotice() {
        $shopOrder = $this;
        // 春节订单提醒
        if (time() < strtotime("2018-02-22 10:00:00") && time() > strtotime("2018-02-09 15:00:00")) {

            $patient = $shopOrder->patient;
            $patient_name = $patient->name;
            $disease = $patient->disease;
            $isADHD = Disease::isADHD($disease->id);

            $content = "";
            $content1 = "{$patient_name}家长您好，您已支付成功。受春节假期影响，快递公司无法正常接单派送，2月9日15:00之后的订单，会在2月22日10点后陆续安排配送，感谢您的理解！\n如果对派送时间无法接受，可在微信上发消息联系助理办理退款，我们会在24小时内给您回复。";
            $content2 = "{$patient_name}您好，您已支付成功。受春节假期影响，快递公司无法正常接单派送，2月9日15:00之后的订单，会在2月22日10点后陆续安排配送，感谢您的理解！";
            if ($isADHD) {
                $content = $content1;
            } else {
                $content = $content2;
            }
            PushMsgService::sendTxtMsgToWxUserBySystem($shopOrder->wxuser, $content);
        }
    }

    public function checkStock() {
        $temp = array();
        foreach ($this->getShopOrderItems() as $shopOrderItem) {
            $cnt = $shopOrderItem->cnt;
            $shopproduct = $shopOrderItem->shopproduct;
            if ($cnt > $shopproduct->left_cnt) {
                $temp[] = $shopproduct->id;
                $content = "patientid[{$this->patient->id}][{$this->patient->name}]购买[{$shopproduct->title}]时缺货，请及时补货";
                PushMsgService::sendMsgToAuditorBySystem('ShopOrder', 1, $content);
            }
        }
        if (count($temp) > 0) {
            return false;
        }
        return true;
    }

    public function checkStockInTpl() {
        $temp = array();
        foreach ($this->getShopOrderItems() as $shopOrderItem) {
            $cnt = $shopOrderItem->cnt;
            $shopproduct = $shopOrderItem->shopproduct;
            if ($cnt > $shopproduct->left_cnt) {
                $temp[] = $shopproduct->id;
            }
        }
        if (count($temp) > 0) {
            return false;
        }
        return true;
    }

    //生成任务
    public function createShopOrderOpTask() {
        $shopOrder = $this;
        $patient = $shopOrder->patient;
        if (false == $patient instanceof Patient) {
            return;
        }

        //订单跟进任务(目前只针对方寸儿童管理服务平台)
        if (1 == $patient->diseaseid) {
            //药品订单才生成订单跟进任务
            if (ShopOrder::type_chufang != $shopOrder->type) {
                return;
            }
            $dayCntFromBaodao = $patient->getDayCntFromBaodao();
            $ispay_chufang_cnt = ShopOrderDao::getIsPayShopOrderCntByPatientType($patient, ShopOrder::type_chufang);

            if ($dayCntFromBaodao < 29) {
                if ($ispay_chufang_cnt == 0) {
                    //首单跟进任务
                    OpTaskService::createPatientOpTask($patient, 'shoporder:firstfollow', $shopOrder, '', 1);
                } else {
                    //普通日常跟进
                    OpTaskService::createPatientOpTask($patient, 'shoporder:normalfollow', $shopOrder, '', 1);
                }
            } else {
                //普通日常跟进
                OpTaskService::createPatientOpTask($patient, 'shoporder:normalfollow', $shopOrder, '', 1);
            }
        }

    }

    // 生成方寸处方
    public function createPrescription() {
        $shopOrder = $this;
        if ($shopOrder->type != ShopOrder::type_chufang) {
            return;
        }

        $yishi = YiShiDao::getOneYishi();

        $row = [];
        $row["wxuserid"] = $shopOrder->wxuserid;
        $row["userid"] = $shopOrder->userid;
        $row["patientid"] = $shopOrder->patientid;
        $row["shoporderid"] = $shopOrder->id;
        $row["doctorid"] = $shopOrder->the_doctorid;
        $row["yishiid"] = $yishi->id;
        $row["type"] = 1; // 普通
        $row["hospital_name"] = $yishi->hospital_name;
        $row["department_name"] = $yishi->department_name;
        $row["patient_name"] = $shopOrder->patient->name;
        $row["patient_sex"] = $shopOrder->patient->sex;
        $row["patient_birthday"] = $shopOrder->patient->birthday;
        $row["status"] = 0; // 等待医生审核
        $prescription = Prescription::createByBiz($row);
        $prescriptionItems = [];

        foreach ($shopOrder->getShopOrderItems() as $shopOrderItem) {
            $cnt = $shopOrderItem->cnt;
            if ($cnt < 1) {
                continue;
            }

            // 生成处方items
            $shopproduct = $shopOrderItem->shopproduct;
            $medicineProduct = $shopproduct->obj;
            if (false == $medicineProduct instanceof MedicineProduct) {
                continue;
            }
            $row = [];
            $row["prescriptionid"] = $prescription->id;
            $row["medicineproductid"] = $medicineProduct->id;
            $row["medicine_title"] = $medicineProduct->getTitle();
            $row["size_pack"] = $medicineProduct->size_pack;
            $row["pack_unit"] = $medicineProduct->pack_unit;
            $row["drug_way"] = $medicineProduct->drug_way;
            $row["drug_dose"] = $medicineProduct->drug_dose;
            $row["drug_frequency"] = $medicineProduct->drug_frequency;
            $row["cnt"] = $cnt;
            $prescriptionItems[] = PrescriptionItem::createByBiz($row);
        }
    }

    //添加赠品
    public function tryAddGift() {
        $shopOrder = $this;
        foreach ($shopOrder->getShopOrderItems() as $shopOrderItem) {
            $cnt = $shopOrderItem->cnt;
            if ($cnt < 1) {
                continue;
            }
            $shopProduct = $shopOrderItem->shopproduct;

            //如果是赠品跳过
            $is_gift = $shopProduct->isGift();
            if ($is_gift) {
                continue;
            }

            //商品可以附带创建赠品的数量
            $gift_cnt = $shopProduct->canCreateGiftCnt($cnt);
            if ($gift_cnt >= 1) {
                $gift_shopProduct = $shopProduct->getGiftShopProduct();
                if ($gift_shopProduct instanceof ShopProduct) {
                    $gift_shopOrderItem = ShopOrderItemDao::getShopOrderItemByShopOrderShopProduct($shopOrder, $gift_shopProduct);
                    if (false == $gift_shopOrderItem instanceof ShopOrderItem) {
                        //生成赠品shoporderitem
                        $row = array();
                        $row["shoporderid"] = $shopOrder->id;
                        $row["shopproductid"] = $gift_shopProduct->id;
                        $row["price"] = 0;
                        $row["cnt"] = $gift_cnt;
                        ShopOrderItem::createByBiz($row);
                    }
                }
            }
        }
    }

    //判断订单里是否有液体商品
    public function isContainWater() {
        foreach ($this->getShopOrderItems() as $shopOrderItem) {
            $shopProduct = $shopOrderItem->shopproduct;
            if ($shopProduct->isWater()) {
                return true;
            }
        }
        return false;
    }
}
