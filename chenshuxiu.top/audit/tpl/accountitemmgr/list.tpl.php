<?php
$pagetitle = "帐务明细 AccountItems";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a <?= (false == $account instanceof Account) ?'class="tab-btn-highlight"':'';  ?> href="/accountitemmgr/list">全部账号</a>
            <?php if($account instanceof Account){ ?>
            <span class="tab-btn-highlight"><?= $account->user->name; ?>(<?= $account->user->patient->name; ?>)</span>

                <?php if($account->code=='user_rmb'){ ?>
            <a class="btn btn-success" href="/accountmgr/withdrawRefundPost?accountid=<?=$account->id ?>">余额(<?=$account->getBalance_yuan() ?>元)提现, 原路退款</a>
            <a class="btn btn-success" href="/fixordermgr/add?accountid=<?=$account->id ?>">手工修正单</a>
                <?php } ?>
            <?php }?>
        </div>
        <div class="searchBar">
            <?php
            foreach (AccountItem::getAccountItemTypeDefines(true) as $k => $v) {
                if ($k == $accountitemtype) {
                    ?>
                    <a class="tab-btn-highlight" href="/accountitemmgr/list?accountid=<?=$account->id ?>"><?= $v ?></a>
                <?php
                } else {
                    ?>
                    <a class="tab-btn" href="/accountitemmgr/list?accountid=<?=$account->id ?>&accountitemtype=<?= $k ?>"><?= $v ?></a>
                <?php
                }
                ?>
            <?php
            }
            ?>
        </div>
        <?php if($accountTrans instanceof AccountTrans){ ?>
        <div class="searchBar">
            [ <?= $accountTrans->fromAccount->user->name; ?>(<?= $accountTrans->fromAccount->user->patient->name; ?>)
            => <?= $accountTrans->toAccount->user->name; ?>(<?= $accountTrans->toAccount->user->patient->name; ?>) ]
            [ <?= $accountTrans->objtype?> ][ <?= $accountTrans->objid?> ][ <?= $accountTrans->code?> ]
             <span class="red"><?= $accountTrans->getAmount_yuan(); ?></span>
            元
        </div>
        <?php }?>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>账务明细ID</td>
                    <td>时间</td>
                    <td>账务事务ID</td>
                    <td>账户user/patient</td>
                    <td>账户名称</td>
                    <td style="text-align: center">收支</td>
                    <td style="text-align: right">金额(元)</td>
                    <td style="text-align: right">余额(元)</td>
                    <td>备注</td>
                    <td>对方账户user/patient</td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($accountitems as $a) {
                ?>
                <tr>
                    <td><?= $a->id; ?></td>
                    <td><?= $a->createtime  ?></td>
                    <td>
                        <a href="/accountitemmgr/list?accounttransid=<?= $a->accounttransid?>"><?= $a->accounttransid; ?></a>
                    </td>
                    <td>
                        <a href="/accountitemmgr/list?accountid=<?= $a->account->id?>"><?= $a->account->user->name; ?></a>
                        <span class="gray"><?= $a->account->user->patient->name; ?></span>
                    </td>
                    <td><?= $a->account->getCodeDesc(); ?></td>
                    <td align=center><?= $a->getInOutStr()  ?></td>
                    <td align=right><?= $a->getAmount_yuan()  ?></td>
                    <td align=right><?= $a->getBalance_yuan()  ?></td>
                    <td><?= $a->remark ?> </td>
                    <td><?= $a->otherAccount()->user->name ?> <span class="gray"><?= $a->otherAccount()->user->patient->name ?></span> [<?= $a->otherAccount()->getCodeDesc() ?>]
                    </td>
                </tr>
            <?php
            }
            ?>
                <tr>
                    <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                </tr>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
