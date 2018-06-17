<?php
$pagetitle = "电话商品 修改";
$cssFiles = [
    "{$img_uri}/v5/plugin/weditor/css/wangEditor.css",
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v5/plugin/weditor/js/wangEditor.min.js",
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-10">
        <form action="/callproductmgr/modifypost" method="post">
            <input type="hidden" name="callproductid" value="<?= $callProduct->id; ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>callproductid</th>
                    <td>
                            <?= $callProduct->id?>
                        </td>
                </tr>
                <tr>
                    <th>商品标题</th>
                    <td>
                        <input style="width: 400px" type="text" name="title" value="<?= $callProduct->title; ?>" />
                        *
                    </td>
                </tr>
                <tr>
                    <th>商品介绍</th>
                    <td>
                        <textarea name="content" id="content" style="height: 500px; width:95%"><?= $callProduct->content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>价格</th>
                    <td>
                        <input type="text" name="price_yuan" value="<?= $callProduct->getPrice_yuan(); ?>" />
                        元
                    </td>
                </tr>
                <tr>
                    <th>市场价格</th>
                    <td>
                        <input type="text" name="market_price_yuan" value="<?= $callProduct->getMarket_price_yuan(); ?>" />
                        元
                    </td>
                </tr>
                <tr>
                    <th>包装单位</th>
                    <td>
                        <input type="text" name="pack_unit" value="<?= $callProduct->pack_unit; ?>" />
                        如: 盒
                    </td>
                </tr>
                <tr>
                    <th>service_percent</th>
                    <td>
                        <input type="text" name="service_percent" value="<?= $callProduct->service_percent; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>关联obj</th>
                    <td>
                            <?= $callProduct->objtype?>
                            <?= $callProduct->objid?>
                        </td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td>
                            <?= HtmlCtr::getRadioCtrImp(CtrHelper::getStatus_onlineCtrArray(), "status", $callProduct->status, ''); ?>
                        </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" class="btn btn-success" value="提交" />
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        var editor = new wangEditor('content');
        editor.config.hideLinkImg = true;
        editor.config.uploadImgFileName = 'imgurl'
        editor.config.uploadImgUrl = '/picture/uploadimagepost/?w=150&h=150&isCut=&type=ShopProduct&fromWeditor=1';

        editor.create();
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
