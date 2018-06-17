<?php
// 账户 Account
// 同一个人相同类型(code)账户只能有一个

// owner by sjp
// create by sjp
// review by sjp 20160627
class Account extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'userid',  // userid
            "code",  // 账户类型编码,英文字符串
            "unit",  // 单位
            "balance",  // 余额,精确到分
            "status"); // 状态
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            "userid",
            'code',
            "unit");
    }

    protected function init_belongtos () {
        $this->_belongtos['user'] = array(
            "type" => 'User',
            "key" => "userid");
    }

    // 无效
    const status_invalid = 0;

    // 有效
    const status_valid = 1;

    // 冻结
    const status_freeze = 2;

    // 账户类型
    public static function getCodeDescs ($needAll = false) {

        // 用户账户,后缀 Acct
        $arr = array();
        if ($needAll) {
            $arr['All'] = '全部';
        }

        // 患者账户
        $arr['user_rmb'] = '用户账户';
        $arr['user_rmb_freeze'] = '用户冻结账户';
        $arr['user_jifen'] = '用户积分账户';
        $arr['user_jifen_freeze'] = '用户积分冻结账户';

        // 医生账户
        $arr['doctor_rmb'] = '医生账户';
        $arr['doctor_rmb_freeze'] = '医生冻结账户';
        $arr['doctor_jifen'] = '医生积分账户';
        $arr['doctor_jifen_freeze'] = '医生积分冻结账户';

        // 系统账户
        // Fund = 用于给用户账户转账的基金, 余额 <= 0
        // Out = 用于用户账户转出(消费, 提现, 红包等)的系统回流账户

        // 用户角度看是收入
        $arr["sys_user_deposite_fund"] = "患者充值基金"; // x => user_rmb
        $arr["sys_user_jifen_fund"] = "患者积分基金"; // x => user_jifen

        // 用户角度看是支出
        $arr["sys_user_shop_out"] = "患者消费支出"; // user_rmb => x
        $arr["sys_user_refund_out"] = "患者提现原路退款"; // user_rmb_freeze => x
        $arr["sys_user_wxredbag_out"] = "微信红包提现支出"; // user_rmb_freeze => x
        $arr["sys_user_jifen_out"] = "积分兑换支出"; // user_jifen => x

        // 医生角度看是收入
        $arr["sys_doctor_income_fund"] = "医生收益基金"; // x => doctor_rmb
        $arr["sys_doctor_jifen_fund"] = "医生积分基金"; // x => doctor_jifen

        // 医生角度看是支出
        $arr["sys_doctor_withdraw_out"] = "医生收益提现支出"; // doctor_rmb => x
        $arr["sys_doctor_jifen_out"] = "医生积分兑换支出"; // doctor_jifen => x

        // 账户修正基金,用于运营手工充值,手工扣款
        $arr["sys_fix_fund"] = "账户修正基金"; // x => y (user_rmb)

        return $arr;
    }

    private static $maxid = 0;

    // 初始化系统账户
    public static function initSystemUserAccounts () {
        $sql = "select max(id) from accounts where id < 100";
        self::$maxid = Dao::queryValue($sql);

        self::createSysAccount('sys_user_deposite_fund');
        self::createSysAccount('sys_user_jifen_fund', Unit::jifen);

        self::createSysAccount('sys_user_shop_out');
        self::createSysAccount('sys_user_refund_out');
        self::createSysAccount('sys_user_wxredbag_out');
        self::createSysAccount('sys_user_jifen_out', Unit::jifen);

        self::createSysAccount('sys_doctor_income_fund');
        self::createSysAccount('sys_doctor_jifen_fund', Unit::jifen);

        self::createSysAccount('sys_doctor_withdraw_out');
        self::createSysAccount('sys_doctor_jifen_out', Unit::jifen);

        self::createSysAccount('sys_fix_fund');
    }

    // 账户类型
    public function getCodeDesc () {
        $arr = self::getCodeDescs();
        return isset($arr[$this->code]) ? $arr[$this->code] : $this->code;
    }

    // 获取明细 item
    public function getAccountItems () {
        return AccountItem::getArrayOfAccount($this);
    }

    // 转账给某账户, code 用于生成trans
    public function transto (Account $toaccount, $amount, EntityBase $obj, $code = "process", $remark = "") {
        return PostingRule::createAndProcess($this, $toaccount, $amount, $obj, $code, $remark);
    }

    // 余额 (元)
    public function getBalance_yuan () {
        return sprintf("%.2f", $this->balance / 100);
    }

    // /////////////////////
    // 内存缓存
    private static $_accountCache = array();

    // 从内存缓存中获取
    private static function getFromCache (User $user, $code = 'user_rmb') {
        $key = "{$user->id}:{$code}";
        if (isset(self::$_accountCache[$key])) {
            return self::$_accountCache[$key];
        } else {
            return false;
        }
    }

    // 压人内存缓存
    private static function pushCache (Account $account) {
        $key = "{$account->user->id}:{$account->code}";
        self::$_accountCache[$key] = $account;
    }

    // 创建对象
    public static function createByBiz (User $user, $code = 'user_rmb', $unit = Unit::rmb) {
        $entity = self::getFromCache($user, $code);
        if ($entity instanceof Account) {
            return $entity;
        }

        $entity = self::getByUserAndCodeImp($user, $code);
        if (false == $entity instanceof Account) {
            $row = array();
            $row["userid"] = $user->id;
            $row["code"] = $code;
            $row["unit"] = $unit;
            $row["balance"] = 0;
            $row["status"] = self::status_valid;
            $entity = new Account($row);
        }

        self::pushCache($entity);

        return $entity;
    }

    // createSysAccount
    private static function createSysAccount ($code, $unit = Unit::rmb) {
        $user = User::getById(1);

        $account = self::getByUserAndCodeImp($user, $code);

        if (false == $account instanceof Account) {

            self::$maxid ++;

            $row = array();
            $row["id"] = self::$maxid;
            $row["userid"] = 1;
            $row["code"] = $code;
            $row["unit"] = $unit;
            $row["balance"] = 0;
            $row["status"] = self::status_valid;
            $account = new Account($row);
        }

        return $account;
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////

    // 获取[系统]账户
    public static function getSysAccount ($code = 'sys_user_deposite_fund', $unit = Unit::rmb) {
        $user = User::getById(1); // userid=1
        return self::getByUserAndCode($user, $code, $unit);
    }

    // 获取[某人+类型]账户
    public static function getByUserAndCode (User $user, $code, $unit = Unit::rmb) {
        return self::createByBiz($user, $code, $unit);
    }

    // 获取[某人+类型]账户,Imp
    public static function getByUserAndCodeImp (User $user, $code) {
        $cond = "AND userid=:userid AND code=:code ";
        $bind = array(
            ":userid" => $user->id,
            ":code" => $code);

        return Dao::getEntityByCond("Account", $cond, $bind);
    }

    // 获取某人账户列表
    public static function getArrayByUser (User $user) {
        $cond = " AND userid=:userid ";
        $bind = array(
            ':userid' => $user->id);

        return Dao::getArrayByCond('Account', $cond, $bind);
    }

    // 获取全部账户
    public static function getAllArray4page ($pagesize, $pagenum) {
        return Dao::getEntityListByCond4Page('Account', $pagesize, $pagenum);
    }
}
