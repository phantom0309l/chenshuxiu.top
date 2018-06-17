<?php
$pagetitle = "商品库存金额";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/shopproductmgr/ListForSumPrice" class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-1 control-label">疾病组:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$diseasegroupid,'js-select2 form-control diseaseGroupSelect') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-1 control-label">类型 :</label>
                    <div class="col-md-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::toShopProductTypeCtrArray($shopProductTypes),'shopproducttypeid', $shopproducttypeid, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label">状态 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getStatus_onlineCtrArray(true),'status', $status, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label">药品 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopProductMedicineTypeCtrArray(),'medicine_type', $medicine_type, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">开始：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="startdate" value="<?= $startdate ?>" placeholder="开始时间" />
                    </div>
                    <label class="control-label col-md-2">截止：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="enddate" value="<?= $enddate ?>" placeholder="截至时间" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">时间点:</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="thedate" value="<?= $thedate ?>" placeholder="时间点" />
                    </div>
                    <div class="col-md-4 text-danger" style="margin-top:5px;">
                        (用于查询某一时刻库存情况)
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                    <div class="col-md-2"><input class="btn btn-success btn-block outputBtn" type="button" value="导出"></div>
                </div>
            </form>
        </div>
        <div>
            <div class="table-responsive">
                <table class="table table-bordered col-md-10">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>类别</td>
                        <td>图片</td>
                        <td>标题</td>
                        <td>单价</td>
                        <td>市场价</td>
                        <td>当前库存量</td>
                        <td>当前库存金额</td>
                        <td><?= $thedate ?>库存量</td>
                        <td><?= $thedate ?>库存金额</td>
                        <td>销售数量</td>
                        <td>销售金额</td>
                        <td>成本金额</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($shopProducts as $a) {
                        if(in_array($a->id, array(299188646, 299326226))){
                            continue;
                        }
                        $saled_profile = $a->getSaledProfile($startdate, $enddate);
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td>
                            <a href="/shopproductmgr/list?shopproducttypeid=<?=$a->shopproducttypeid; ?>"><?= $a->shopproducttype->name?></a>
                        </td>
                        <td>
                        <?php if($a->picture instanceof Picture){ ?>
                        <img alt="" src="<?=$a->picture->getSrc(100,100) ?>">
                        <?php } ?>
                        </td>
                        <td><?= $a->title?></td>
                        <td align="right"><?= $a->getPrice_yuan()?></td>
                        <td align="right"><?= $a->getMarket_price_yuan()?></td>
                        <td><?= $a->left_cnt?>(<span class="red"><?= $a->getLeft_cntOfReal() ?></span>)</td>
                        <td><?= $a->getStockSumPrice_yuan() ?></td>
                        <td><?= $a->getStockCnt($thedate) ?></td>
                        <td><?= $a->getStockSumPrice_yuan($thedate) ?></td>
                        <td><?= sprintf("%.0f", $saled_profile["cnt"]) ?></td>
                        <td><?= sprintf("%.2f", $saled_profile["saled_amount"]) ?></td>
                        <td><?= sprintf("%.2f", $saled_profile["cost_amount"]) ?></td>
                        <td>
                            <p class="mt10"><a target="_blank" href="/stockitemmgr/list?shopproductid=<?=$a->id ?>" class="btn btn-default">入库记录</a></p>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){

        var obj = {
            ".outputBtn" : "/shopproductmgr/listForSumPriceOutput?"
        };

        function outputFunc(node, url){
            node.on("click", function(){
                var me = $(this);
                if(me.hasClass('process')){
                    return
                }
                me.addClass('process');
                me.text('正在导出，请稍等....');
                var shopproducttypeid = $(".selected_shopproducttype").data("shopproducttypeid");
                var queryStr = $(".searchBar").find("form").serialize();
                window.location.href = url + queryStr;
            });
        }

        for(var nodeStr in obj){
            outputFunc($(nodeStr), obj[nodeStr]);
        }

        $(".diseaseGroupSelect").on("change", function(){
            var diseasegroupid = $(this).val();
            window.location.href = "/shopproductmgr/ListForSumPrice?diseasegroupid=" + diseasegroupid + "&shopproducttypeid=0&status=2&medicine_type=all";
        })

    })

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
