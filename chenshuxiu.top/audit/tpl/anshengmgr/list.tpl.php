<?php
$pagetitle = "药品追踪列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
$pageScript = <<<SCRIPT
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/anshengmgr/list" class="form-horizontal shopOrderForm">
                <div class="form-group">
                    <label class="control-label col-md-2">时间：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="start_date" value="<?= $start_date ?>" placeholder="起始时间" />
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="end_date" value="<?= $end_date ?>" placeholder="截止时间" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">类型：</label>
                    <label class="col-md-1">
                        <input type="radio" name="type" value="1" <?= $type == 1 ? 'checked' : ''?>/>
                        <span>出库</span>
                    </label>
                    <label class="col-md-1">
                        <input type="radio" name="type" value="2" <?= $type == 2 ? 'checked' : ''?>/>
                        <span>入库</span>
                    </label>
                </div>
                <?php if($type == 1){ ?>
                <div class="form-group">
                    <?php
                        $shopProduct_jingling10 = ShopProduct::getById(ShopProduct::JINGLING10_ID);
                    ?>
                    <label class="col-md-1" style="padding: 0px 0px 0px 15px; width:90px;">当前库存：</label>
                    <p class="col-md-3"><?= $shopProduct_jingling10->title ?> : <?= $jingling10_left_cnt + $jingling10_gift_left_cnt ?></p>
                </div>
                <?php } ?>
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
                    <td>商品编码</td>
                    <td>商品名称</td>
                    <td>生产企业</td>
                    <td>日期</td>
                    <td>类型</td>
                    <td>机构名称</td>
                    <td>医生</td>
                    <td>续方次数</td>
                    <td>入数量</td>
                    <td>出数量</td>
                    <td>批号</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($result as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $a["code"] ?></td>
                        <td><?= $a["title"] ?></td>
                        <td><?= $a["product_factory"] ?></td>
                        <td><?= $a["date"] ?></td>
                        <td><?= $a["type"] ?></td>
                        <td><?= $a["xname"] ?></td>
                        <td><?= $a["doctor_name"] ?></td>
                        <td><?= $a["pos"] ?></td>
                        <td><?= $a["in_cnt"] ?></td>
                        <td><?= $a["out_cnt"] ?></td>
                        <td><?= $a["batch_number"] ?></td>
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

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
