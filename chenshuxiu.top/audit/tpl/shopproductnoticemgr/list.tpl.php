<?php
$pagetitle = "列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/shopproductnoticemgr/list" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-1 control-label">状态 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopProductNoticeCtrArray(),'status', $status, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label">商品：</label>
                    <div class="col-sm-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::toShopProductCtrArray($shopProducts),"shopProductId",$shopProductId,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>createtime</td>
                    <td>微信名</td>
                    <td>患者</td>
                    <td>商品</td>
                    <td>患者欲购买数量</td>
                    <td class="red">当前可售库存</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($shopProductNotices as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->wxuser->nickname ?></td>
                    <td><?= $a->patient->name ?></td>
                    <td><?= $a->shopproduct->title ?></td>
                    <td><?= $a->cnt ?></td>
                    <td class="red"><?= $a->shopproduct->getLeft_cntOfReal() ?></td>
                    <td class="status"><?= $a->getStatusStr() ?></td>
                    <td>
                        <span class="notice btn <?= $a->isNotNotice() ? 'btn-primary' : 'btn-default' ?>" data-id="<?= $a->id ?>">通知</span>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    App.initHelper('select2');

    $(".notice").on('click', function () {
        var me = $(this);
        var shopProductNoticeId = me.data('id');

        if (me.hasClass('btn-default')) {
            return;
        }

        $.ajax({
            url: '/shopProductNoticeMgr/pushJson',
            type: 'get',
            dataType: 'json',
            data: {
                shopProductNoticeId: shopProductNoticeId
            },
            "success": function (data) {
                if (data.errno == 0) {
                    me.addClass('btn-default').removeClass('btn-primary');
                    if(data.errmsg != ''){
                        alert(data.errmsg);
                        me.parents('tr').find('.status').text(data.errmsg);
                    }
                }
            }
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
