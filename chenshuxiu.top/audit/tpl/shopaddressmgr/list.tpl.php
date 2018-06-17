<?php
$pagetitle = "配送地址 ShopAddresss";
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
    <section class="col-md-10">
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="90">id</td>
                    <td width="140">创建时间</td>
                    <td>微信号</td>
                    <td>患者</td>
                    <td>联系人</td>
                    <td>联系电话</td>
                    <td>省</td>
                    <td>市</td>
                    <td>区</td>
                    <td>详细地址</td>
                    <td>邮编</td>
                </tr>
            </thead>
            <tbody>
                    <?php
                    foreach ($shopAddresss as $a) {
                        ?>
                    <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getCreateDayHi() ?></td>
                    <td><?= $a->wxuser->nickname?></td>
                    <td><?= $a->patient->name?></td>
                    <td><?= $a->linkman_name?></td>
                    <td><?= $a->linkman_mobile?></td>
                    <td><?= $a->xprovince->name?></td>
                    <td><?= $a->xcity->name?></td>
                    <td><?= $a->xcounty->name?></td>
                    <td><?= $a->content?></td>
                    <td><?= $a->postcode?></td>
                </tr>
                    <?php } ?>
                <tr>
                    <td colspan=12>
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
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
