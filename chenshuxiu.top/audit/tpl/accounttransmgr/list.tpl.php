<?php
$pagetitle = "帐务单据查询页 AccountTrans";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/accounttransmgr/list" method="get" class="">
                请选择帐务单据类型：
                <?php echo HtmlCtr::getSelectCtrImp(AccountTrans::getObjTypeOfOrder(true),'objtype',$objtype); ?>
                <input type="submit" value="查询" />
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th>单据id</th>
                    <th>出钱账户</th>
                    <th>=></th>
                    <th>入钱账户</th>
                    <td style="text-align: right">金额(元)</td>
                    <th>objtype</th>
                    <th>objid</th>
                    <th>code</th>
                    <th>状态</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($accounttranss as $a) {
                $fromAccount = Account::getById($a->fromaccountid);
                $toAccount = Account::getById($a->toaccountid);
                ?>
                <tr>
                    <td><?= $a->id; ?></td>
                    <td>
                        [<?= $fromAccount->getCodeDesc(); ?>]
                        <span class="blue"><?= $fromAccount->user->name; ?></span>
                        <span class="gray"><?= $fromAccount->user->patient->name; ?></span>
                    </td>
                    <td>=></td>
                    <td>
                        [<?= $toAccount->getCodeDesc(); ?>]
                        <span class="blue"><?= $toAccount->user->name; ?></span>
                        <span class="gray"><?= $toAccount->user->patient->name; ?></span>
                    </td>
                    <td align=right><?= $a->getAmount_yuan()  ?></td>
                    <td><?= AccountTrans::getObjTypeDescStr($a->objtype) ?></td>
                    <td><?= $a->objid ?> </td>
                    <td><?= $a->code ?> </td>
                    <td><?= $a->status?'转账成功':'转账失败' ?> </td>
                    <td><?= $a->remark ?> </td>
                    <td>
                        <a href="/accountitemmgr/list?accounttransid=<?= $a->id ?>">明细</a>
                    </td>
                </tr>
            <?php }  ?>
                <tr>
                    <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                </tr>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
