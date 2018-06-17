<?php
$pagetitle = "修改";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <input type="hidden" value="<?= $shopPkgItem->id ?>" name="shoppkgitemid"/>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <td>Id</td>
                    <td>
                        <input type="text" name="id" value="<?= $shopPkgItem->id ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>创建时间</td>
                    <td>
                        <input type="text" name="createtime" value="<?= $shopPkgItem->createtime ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>商品名</td>
                    <td>
                        <input type="text" name="shopproduct-title" value="<?= $shopPkgItem->shopproduct->title ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>单价</td>
                    <td>
                        <input type="text" name="price" value="<?= $shopPkgItem->getPrice_yuan() ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>数量</td>
                    <td>
                        <input type="text" name="cnt" value="<?= $shopPkgItem->cnt ?>"/>
                    </td>
                </tr>
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
