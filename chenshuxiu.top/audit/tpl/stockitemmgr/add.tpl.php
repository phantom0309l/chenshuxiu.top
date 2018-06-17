<?php
$pagetitle = "入库存";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/stockitemmgr/addpost" method="post">
            <input type="hidden" value="<?= $shopProduct->id ?>" name="shopproductid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td width='140'>商品</td>
                    <td>
                        <p><?= $shopProduct->title ?></p>
                        <p><img src="<?= $shopProduct->picture->getSrc(100,100) ?>"/></p>
                    </td>
                </tr>
                <tr>
                    <td>进货价</td>
                    <td>
                        <input type="text" name="price" value="" /> (元)
                        <span class="red">请慎重填写，一旦填写不可修改！！！</span>
                    </td>
                </tr>
                <tr>
                    <td>进货数量</td>
                    <td>
                        <input type="text" name="cnt" value="" />
                        <span class="red">请慎重填写，一旦填写不可修改！！！</span>
                    </td>
                </tr>
                <tr>
                    <td>入库时间</td>
                    <td>
                        <input type="text" name="in_time" value="" class="calendar"/>
                    </td>
                </tr>
                <tr>
                    <td>过期时间</td>
                    <td>
                        <input type="text" name="expire_date" value="" class="calendar"/>
                    </td>
                </tr>
                <tr>
                    <td>生产批号</td>
                    <td>
                        <input type="text" name="batch_number" value="" />
                    </td>
                </tr>
                <tr>
                    <td>渠道来源</td>
                    <td>
                        <div class="col-sm-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStockItemSourceArray(false),"sourse",'','js-select2 form-control') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>订货人</td>
                    <td>
                        <input type="text" name="order_person" value="" />
                    </td>
                </tr>
                <tr>
                    <td>付款人</td>
                    <td>
                        <input type="text" name="pay_person" value="" />
                    </td>
                </tr>
                <tr>
                    <td>账期</td>
                    <td>
                        <input type="text" name="the_date" value="" class="calendar"/>
                    </td>
                </tr>
                <tr>
                    <td>付款方式</td>
                    <td>
                        <div class="col-sm-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStockItemPayTypeArray(false),"pay_type",'','js-select2 form-control') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>有无发票</td>
                    <td>
                        <div class="col-sm-2">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStockItemHasInvoiceArray(false),"has_invoice",'','js-select2 form-control') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>备注</td>
                    <td>
                        <textarea rows="10" cols="120" name="remark"></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
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

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
