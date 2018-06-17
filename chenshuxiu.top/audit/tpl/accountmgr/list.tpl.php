<?php
$pagetitle = "帐务帐号查询页 Account";
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
        <input class="hidden" name="userid" value="<?=$userid?>">
        <div class="searchBar">
            <?php foreach( $codes as $i => $v ){ ?>
                <a href="/accountmgr/list?code=<?= $i?>" class="btn <?= $i==$code ? 'btn-primary btn-default' : 'btn-default'?>"><?= $v?></a>
            <?php } ?>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <td>帐号id</td>
                    <td>账户user/patient</td>
                    <td>账户名称</td>
                    <td>账户类型</td>
                    <td style="text-align: right">余额(元)</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
            <?php foreach($accounts as $a) { ?>
                    <tr>
                    <td><?= $a->id; ?></td>
                    <td>
                        <?= $a->user->name; ?>
                        <span class="gray"><?= $a->user->patient->name; ?></span>
                    </td>
                    <td><?= $a->getCodeDesc(); ?></td>
                    <td><?= $a->unit == Unit::rmb?'人民币':'积分'  ?></td>
                    <td align="right">
                        <a href="/accountmgr/list?code=<?= $code?>&userid=<?= $userid?>">
                            <?= $a->getBalance_yuan()?>
                        </a>
                    </td>
                    <td><?= $a->status?'正常':'异常' ?> </td>
                    <td>
                        <a href="/accountitemmgr/list?accountid=<?= $a->id ?>">明细</a>
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