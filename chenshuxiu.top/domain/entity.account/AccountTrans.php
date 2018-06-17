<?php
// 账务事务 AccountTrans
// 对发生的账务事件进行记录,同时记录交易对象，譬如某商品

// owner by sjp
// create by sjp
// review by sjp 20160627
class AccountTrans extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            "fromaccountid",  // 出钱账户
            "toaccountid",  // 入钱账户
            "amount",  // 金额,单位分
            "objtype",  // 三元式
            "objid",  // 三元式
            "code",  // 三元式
            "status",  // 状态
            "remark");
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'fromaccountid',
            'toaccountid',
            'objtype',
            'objid',
            'code');
    }

    protected function init_belongtos () {
        $this->_belongtos['fromAccount'] = array(
            "type" => "Account",
            "key" => "fromaccountid");
        $this->_belongtos['toAccount'] = array(
            "type" => "Account",
            "key" => "toaccountid");
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    const status_begin = 1;

    const status_doing = 2;

    const status_finish = 3;

    private $accountItems = array();
    // 账务明细

    // 不做实际数据库操作，只检查各个账户转款是否平
    public function submit () {
        $sum = 0;
        foreach ($this->accountItems as $a) {
            $sum += $a->amount;
        }
        DBC::requireEquals($sum, 0, "事务相关明细不能持平");

        $this->status = self::status_finish;
    }

    // 增加明细
    public function appendItems ($items) {
        foreach ($items as $item) {
            $this->accountItems[] = $item;
        }
    }

    // 明细列表
    public function getAccountItems () {
        return AccountItem::getArrayByTransId($this->id);
    }

    // 金额 (元)
    public function getAmount_yuan () {
        return sprintf("%.2f", $this->amount / 100);
    }

    // $row = array();
    // $row["fromaccountid"] = $fromaccountid;
    // $row["toaccountid"] = $toaccountid;
    // $row["amount"] = $amount;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["code"] = $code;
    // $row["remark"] = $remark;
    private static function createByBizImp ($row) {
        DBC::requireNotEmpty($row, "AccountTrans::createByBiz row cannot empty");

        $row["status"] = self::status_begin;

        return new AccountTrans($row);
    }

    // 创建对象
    public static function createByBiz (Account $fromAccount, Account $toAccount, $amount, EntityBase $obj, $code = '', $remark = '') {
        $entity = self::getOneByObj_Code($obj, $code);
        DBC::requireEmpty($entity, '账务事务重复执行:[' . get_class($obj) . ',' . $obj->id . ',{$code}]');

        $row = array();
        $row["fromaccountid"] = $fromAccount->id;
        $row["toaccountid"] = $toAccount->id;
        $row["amount"] = $amount;
        $row["objtype"] = get_class($obj);
        $row["objid"] = $obj->id;
        $row["code"] = $code;
        $row["remark"] = $remark;

        return self::createByBizImp($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 获取某账户的出账事务列表
    public static function getOutArrayByAccountId ($fromaccountid) {
        $cond = ' AND fromaccountid=:fromaccountid ';
        $bind = array(
            ':fromaccountid' => $fromaccountid);

        return Dao::getEntityListByCond('AccountTrans', $cond, $bind);
    }

    // 获取某账户的入账事务列表
    public static function getInArrayByAccountId ($toaccountid) {
        $cond = " AND toaccountid=:toaccountid ";
        $bind = array(
            ':toaccountid' => $toaccountid);

        return Dao::getEntityListByCond('AccountTrans', $cond, $bind);
    }

    // 获取某类型的事务
    public static function getArrayByCode ($code) {
        $cond = " AND code=:code ";
        $bind = array(
            ":code" => $code);

        return Dao::getEntityListByCond("AccountTrans", $cond, $bind);
    }

    // 获取某对象相关的事务
    public static function getArrayOfObj (EntityBase $obj) {
        $cond = " AND objtype=:objtype AND objid=:objid ";
        $bind = array(
            ":objtype" => get_class($obj),
            ":objid" => $obj->id);

        return Dao::getEntityListByCond('AccountTrans', $cond, $bind);
    }

    // 获取某对象相关的事务
    public static function getOneByObj_Code (EntityBase $obj, $code = '') {
        $cond = " AND objtype=:objtype AND objid=:objid  AND code=:code ";
        $bind = array(
            ":objtype" => get_class($obj),
            ":objid" => $obj->id,
            ":code" => $code);

        return Dao::getEntityListByCond('AccountTrans', $cond, $bind);
    }

    // 获取order的所有objtype
    public static function getObjTypeOfOrder ($needAll = false) {
        $arr = array();
        if ($needAll) {
            $arr['All'] = "全部";
        }
        $arr['DepositeOrder'] = "充值单";
        $arr['RefundOrder'] = "原路退款单";
        $arr['ShopOrder'] = "商城订单";
        $arr['PatientWithdrawOrder'] = "用户提现单";
        $arr['WxRedbagOrder'] = "微信红包单";
        return $arr;
    }

    // 事务类型
    public static function getObjTypeDescStr ($objtype = '') {
        $arr = self::getObjTypeOfOrder();
        return isset($arr[$objtype]) ? $arr[$objtype] : '未知';
    }
}
