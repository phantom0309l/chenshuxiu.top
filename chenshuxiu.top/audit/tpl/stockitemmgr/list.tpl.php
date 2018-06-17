<?php
$pagetitle = "库存列表";
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
            <form action="/stockitemmgr/list" class="form-horizontal shopOrderForm">
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品：</label>
                    <div class="col-sm-3">
                        <select name="shopproductid" class="js-select2 form-control">
                            <option value="0" <?= $shopproductid == 0 ? 'selected' : ''?>>全部</option>
                            <?php foreach($shopProducts as $shopProduct){ ?>
                                <option value="<?= $shopProduct->id ?>" <?= $shopproductid == $shopProduct->id ? 'selected' : ''?>>
                                    <?= $shopProduct->title ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">来源：</label>
                    <div class="col-sm-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStockItemSourceArray(),"sourse",$sourse,'js-select2 form-control') ?>
                    </div>
                    <label class="control-label col-md-2">过期：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="expire_date" value="<?= $expire_date ?>" placeholder="过期时间" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">账期：</label>
                    <div class="col-sm-2">
                        <input type="text" class="calendar form-control" name="the_date" value="<?= $the_date ?>" placeholder="账期" />
                    </div>
                    <label class="col-sm-2 control-label" style="width: 100px;">付款方式：</label>
                    <div class="col-sm-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStockItemPayTypeArray(),"pay_type",$pay_type,'js-select2 form-control') ?>
                    </div>
                    <label class="col-md-2 control-label" style="width: 100px;">有无发票：</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getStockItemHasInvoiceArray(),"has_invoice",$has_invoice,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">入库：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="in_time_start" value="<?= $in_time_start ?>" placeholder="起始时间" />
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="in_time_end" value="<?= $in_time_end ?>" placeholder="截止时间" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                    <div class="col-md-2">
                        <span class="btn btn-primary btn-block outputBtn">导出明细</span>
                    </div>
                </div>
            </form>
        </div>
        <?php if($shopproductid > 0){ ?>
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <p class="red">新建前确认要录入的商品是否已经录入过了！</p>
            <a class="btn btn-success" href="/stockitemmgr/add?shopproductid=<?= $shopproductid ?>">新建库存单</a>
        </div>
        <?php } ?>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>商品</td>
                    <td>价格</td>
                    <td>入库量</td>
                    <td>当前剩余</td>
                    <td>入库时间</td>
                    <td>过期时间</td>
                    <td>生产批号</td>
                    <td>渠道</td>
                    <td>订货人</td>
                    <td>付款人</td>
                    <td>账期</td>
                    <td>付款方式</td>
                    <td>有无发票</td>
                    <td>最后修改人</td>
                    <td>备注</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($stockItems as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->shopproduct->title ?></td>
                    <td>¥<?= $a->getPrice_yuan() ?></td>
                    <td><?= $a->cnt ?></td>
                    <td><?= $a->left_cnt ?></td>
                    <td><?= substr($a->in_time, 0, 10) ?></td>
                    <td><?= $a->expire_date ?></td>
                    <td><?= $a->batch_number ?></td>
                    <td><?= $a->sourse ?></td>
                    <td><?= $a->order_person ?></td>
                    <td><?= $a->pay_person ?></td>
                    <td><?= $a->the_date ?></td>
                    <td><?= $a->getPayTypeStr() ?></td>
                    <td><?= $a->getHasInvoiceStr() ?></td>
                    <td><?= $a->auditor->name ?></td>
                    <td><?= $a->remark ?></td>
                    <td align="center">
                        <a target="_blank" href="/stockitemmgr/modify?stockitemid=<?=$a->id ?>">修改</a>
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
    $(function(){

        var obj = {
            ".outputBtn" : "/stockitemmgr/listOutput?"
        };

        function outputFunc(node, url){
            node.on("click", function(){
                var me = $(this);
                if(me.hasClass('process')){
                    return
                }
                me.addClass('process');
                me.text('正在导出，请稍等....');
                var queryStr = $(".shopOrderForm").serialize();
                window.location.href = url + queryStr;
            });
        }

        for(var nodeStr in obj){
            outputFunc($(nodeStr), obj[nodeStr]);
        }

    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
