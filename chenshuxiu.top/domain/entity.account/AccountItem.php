<?php
// 账务明细 AccountItem
// 这里的账务模型是双腿事务模型，一个账务事务对应两天账务明细

// owner by sjp
// create by sjp
// review by sjp 20160627
class AccountItem extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            "accounttransid",  // accounttransid
            "accountid",  // accountid
            "amount",  // 整数,精确到分
            "balance",  // 余额快照,精确到分
            "remark");
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            "accounttransid",
            "accountid",
            "amount",
            "balance");
    }

    protected function init_belongtos () {
        $this->_belongtos['account'] = array(
            "type" => "Account",
            "key" => "accountid");
        $this->_belongtos['accountTrans'] = array(
            "type" => "AccountTrans",
            "key" => "accounttransid");
    }

    // 金额 (元)
    public function getAmount_yuan () {
        $str = $this->amount > 0 ? '+' : '';
        return $str . sprintf("%.2f", $this->amount / 100);
    }

    // 余额 (元)
    public function getBalance_yuan () {
        return sprintf("%.2f", $this->balance / 100);
    }

    public function getInOutStr () {
        return $this->amount > 0 ? '收入' : '支出';
    }

    public function otherAccount () {
        $accoutTrans = $this->accountTrans;
        if ($accoutTrans->fromaccountid == $this->accountid) {
            return $accoutTrans->toAccount;
        }
        return $accoutTrans->fromAccount;
    }

    // $row = array();
    // $row["accounttransid"] = $accounttransid;
    // $row["accountid"] = $accountid;
    // $row["amount"] = $amount;
    // $row["balance"] = $balance;
    // $row["remark"] = $remark;
    private static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "AccountItem::createByBiz row cannot empty");

        return new self($row);
    }

    // 推荐使用
    public static function createByAccountTrans (AccountTrans $accountTrans, Account $account, $amount, $balance, $remark = '') {
        $row = array();
        $row["accounttransid"] = $accountTrans->id;
        $row["accountid"] = $account->id;
        $row["amount"] = $amount;
        $row["balance"] = $balance;
        $row["remark"] = $remark;

        self::createByBiz($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 获取账户明细, 适合明细少的账户
    public static function getArrayOfAccount (Account $account) {
        $cond = ' AND accountid=:accountid ';
        $bind = array(
            ':accountid' => $account->id);

        return Dao::getEntityListByCond('AccountItem', $cond, $bind);
    }

    // 获取账户明细
    public static function getArrayOfAccountTrans (AccountTrans $accountTrans) {
        $cond = " AND accounttransid=:accounttransid ";
        $bind = array(
            ':accounttransid' => $accountTrans->id);
        return Dao::getEntityListByCond('AccountItem', $cond, $bind);
    }
    // 用于下拉框
    public static function getAccountItemTypeDefines ($needAll = false) {
        $arr = array();
        if ($needAll) {
            $arr['All'] = "全部";
        }
        $arr['In'] = "收入";
        $arr['Out'] = "支出";
        return $arr;
    }
}
