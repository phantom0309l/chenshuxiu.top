<?php
$pagetitle = "商城商品 ShopProducts";
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
        <div>
            <a class="btn btn-success" href="/shopproductmgr/add">新建商品</a>
        </div>
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/shopproductmgr/list" class="form-horizontal">
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
                    <label class="col-sm-1 control-label">提醒线 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getShopProductNoticeLineTypeCtrArray(),'notice_line', $notice_line, 'css-radio-success')?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                    <div class="col-md-2" style="margin-top:5px;">
                        <a href="/shopproductmgr/ListForSumPrice" target="_blank">更多>></a>
                    </div>
                </div>
            </form>
        </div>
        <form action="/shopproductmgr/posmodifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered col-md-10">
                <thead>
                    <tr>
                        <td width="40">序号</td>
                        <td width="90">id</td>
                        <td>类别</td>
                        <td>objtype objid</td>
                        <td width="120">图片</td>
                        <td>标题</td>
                        <td>单价</td>
                        <td>市场价</td>
                        <td width="140">进货量</td>
                        <td width="60">库存量</td>
                        <td width="80">警戒值</td>
                        <td width="40" class="red">提醒线</td>
                        <td width="40">应急库存量</td>
                        <td width="60">状态</td>
                        <td width="100">操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($shopProducts as $a) {
                        if(in_array($a->id, array(299188646, 299326226))){
                            continue;
                        }
                        ?>
                    <tr>
                        <td>
                            <input class="form-control" style="width: 60px" type="number" name="pos[<?=$a->id?>]" value="<?= $a->pos ?>" />
                        </td>
                        <td>
                            <?= $a->id ?>
                        </td>
                        <td>
                            <a href="/shopproductmgr/list?shopproducttypeid=<?=$a->shopproducttypeid; ?>"><?= $a->shopproducttype->name?></a>
                        </td>
                        <td>
                            <?= $a->objtype?> <?= $a->objid?>
                        </td>
                        <td>
                            <?php if($a->picture instanceof Picture){ ?>
                            <img alt="" src="<?=$a->picture->getSrc(100,100) ?>">
                            <?php } ?>
                        </td>
                        <td>
                            <?= $a->title?>
                        </td>
                        <td align="right">
                            <?= $a->getPrice_yuan()?>
                        </td>
                        <td align="right">
                            <?= $a->getMarket_price_yuan()?>
                        </td>
                        <td>
                            <?php $saleCnt = $a->getSaleCntOfLastWeek() ?>
                            <?= $saleCnt ?> x 1.3 = <?= ceil($saleCnt*1.3) ?> <br/>
                            <?= $saleCnt ?> x 1.5 = <?= ceil($saleCnt*1.5) ?>
                        </td>
                        <td align="right">
                            <?= $a->left_cnt?>(<span class="green"><?= $a->getLeft_cntOfReal() ?></span>)
                        </td>
                        <td>
                            <?= $a->warning_cnt ?>
                        </td>
                        <td>
                            <span class="red"><?= $a->notice_cnt ?>(<?= $a->getMaybeNoticeCnt() ?>)</span>
                            <a target="_blank" href="/shopproductmgr/modify?shopproductid=<?=$a->id ?>">修改</a>
                        </td>
                        <td>
                            <?= $a->getEmergentStockCnt() ?>
                        </td>
                        <td align="right">
                            <?= $a->getStatusDesc()?>
                        </td>
                        <td>
                            <a target="_blank" href="/shopproductmgr/one?shopproductid=<?=$a->id ?>">详情</a>
                            <a target="_blank" href="/shopproductmgr/modify?shopproductid=<?=$a->id ?>">修改</a>
                            <p class="mt10"><a target="_blank" href="/stockitemmgr/list?shopproductid=<?=$a->id ?>" class="btn btn-default">入库记录</a></p>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
            <input class="btn btn-success" type="submit" value="保存序号修改">
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(".diseaseGroupSelect").on("change", function(){
        var diseasegroupid = $(this).val();
        window.location.href = "/shopproductmgr/list?diseasegroupid=" + diseasegroupid + "&shopproducttypeid=0&status=2&medicine_type=all";
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
