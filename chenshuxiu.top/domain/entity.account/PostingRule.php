<?php
// 过账规则
// 本过账规则主要针对双腿事务

// owner by sjp
// create by sjp
// review by sjp 20160627

class PostingRule
{

    // 出账户
    private $fromAccount;

    // 入账户
    private $toAccount;

    // 金额
    private $amount;

    private function __construct (Account $fromAccount, Account $toAccount, $amount) {
        $this->fromAccount = $fromAccount;
        $this->toAccount = $toAccount;
        $this->amount = $amount;
    }

    // 执行过账事务
    private function process (AccountTrans $accountTrans) {
        $this->fromAccount->balance -= $this->amount;
        $this->toAccount->balance += $this->amount;
        $items = array();
        $items[] = AccountItem::createByAccountTrans($accountTrans, $this->fromAccount, 0 - $this->amount, $this->fromAccount->balance,
                '出账:' . $accountTrans->remark);
        $items[] = AccountItem::createByAccountTrans($accountTrans, $this->toAccount, $this->amount, $this->toAccount->balance, '入账:' . $accountTrans->remark);

        $accountTrans->appendItems($items);
    }

    // 创建并执行过账事务
    public static function createAndProcess (Account $fromAccount, Account $toAccount, $amount, EntityBase $obj, $code = 'process', $remark = '') {
        $accountTrans = AccountTrans::createByBiz($fromAccount, $toAccount, $amount, $obj, $code, $remark);
        $postRule = new PostingRule($fromAccount, $toAccount, $amount);
        $postRule->process($accountTrans);
        return $accountTrans;
    }
}
