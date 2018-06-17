<?php
$pagetitle = "商城商品 修改";
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
        <?php if($mydisease instanceof Disease){ ?>
        <form action="/shopproductmgr/modifypost" method="post">
            <input type="hidden" name="shopproductid" value="<?= $shopProduct->id; ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>shopproductid</th>
                    <td>
                        <?= $shopProduct->id?>
                    </td>
                </tr>
                <tr>
                    <th>商品类型</th>
                    <td>
                        <?php if(count($shopProductTypes) > 0){ ?>
                            <?= HtmlCtr::getRadioCtrImp(CtrHelper::toShopProductTypeCtrArray($shopProductTypes, false), 'shopproducttypeid', $shopProduct->shopproducttypeid, ''); ?>
                        <?php }else { ?>
                            <p class="mt10 red">
                                <span>当前疾病下没有商品类型</span>
                                <a href="/shopproducttypemgr/list" target="_blank">新建商品类型</a>
                            </p>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th>SKU码</th>
                    <td>
                        <input type="text" name="sku_code" value="<?= $shopProduct->sku_code ?>" />
                    </td>
                </tr>
                <tr>
                    <th>图片</th>
                    <td>
                        <?php
                        $picWidth = 150;
                        $picHeight = 150;
                        $pictureInputName = "pictureid";
                        $isCut = false;
                        $picture = $shopProduct->picture;
                        $objtype = "ShopProduct";
                        $objid = 0;
                        $objsubtype = "";
                        require_once ("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>配图</th>
                    <td>
                        <?php foreach($shopProduct->getShopProductPictures() as $a){ ?>
                            <?php if ($a->picture instanceof Picture) { ?>
                                <img class="border1" src="<?= $a->picture->getSrc(100,100)?>">
                            <?php }?>
                        <?php } ?>
                            <a target="_blank" href="/shopproductpicturemgr/list?shopproductid=<?=$shopProduct->id ?>">修改配图</a>
                    </td>
                </tr>
                <tr>
                    <th>商品标题</th>
                    <td>
                        <input style="width: 400px" type="text" name="title" value="<?= $shopProduct->title; ?>" />
                        *
                    </td>
                </tr>
                <tr>
                    <th>生产厂家</th>
                    <td>
                        <input style="width: 400px" type="text" name="product_factory" value="<?= $shopProduct->product_factory ?>" />
                        *
                    </td>
                </tr>
                <tr>
                    <th>是否液体</th>
                    <td>
                        <p class="text-danger"><span>这个标注很重要，一定要标注正确，否则会影响发货</span></p>
                        <?= HtmlCtr::getRadioCtrImp(CtrHelper::getShopProductIs_waterCtrArray(), 'is_water', $shopProduct->is_water, ''); ?>
                    </td>
                </tr>
                <tr>
                    <th>商品介绍</th>
                    <td>
                        <textarea name="content" id="content" style="height: 500px; width:95%"><?= $shopProduct->content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>价格</th>
                    <td>
                        <input type="text" name="price_yuan" value="<?= $shopProduct->getPrice_yuan(); ?>" />
                        元
                    </td>
                </tr>
                <tr>
                    <th>市场价格</th>
                    <td>
                        <input type="text" name="market_price_yuan" value="<?= $shopProduct->getMarket_price_yuan(); ?>" />
                        元
                    </td>
                </tr>
                <tr>
                    <th>包装单位</th>
                    <td>
                        <input type="text" name="pack_unit" value="<?= $shopProduct->pack_unit; ?>" />
                        如: 盒
                    </td>
                </tr>
                <tr>
                    <th>警戒值</th>
                    <td>
                        <input type="text" name="warning_cnt" value="<?= $shopProduct->warning_cnt; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>提醒运营线</th>
                    <td>
                        <input type="text" name="notice_cnt" value="<?= $shopProduct->notice_cnt; ?>" /> (<?= $shopProduct->getMaybeNoticeCnt() ?>)
                    </td>
                </tr>
                <tr>
                    <th>初始购买数量</th>
                    <td>
                        <input type="text" name="buy_cnt_init" value="<?= $shopProduct->buy_cnt_init; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>最大购买数量</th>
                    <td>
                        <input type="text" name="buy_cnt_max" value="<?= $shopProduct->buy_cnt_max; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>service_percent</th>
                    <td>
                        <input type="text" name="service_percent" value="<?= $shopProduct->service_percent; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>关联obj</th>
                    <td>
                        <?= $shopProduct->objtype?>
                        <?= $shopProduct->objid?>
                    </td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td>
                        <?= HtmlCtr::getRadioCtrImp(CtrHelper::getStatus_onlineCtrArray(), "status", $shopProduct->status, ''); ?>
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
        <?php }else { ?>
            <p class="mt10 red">
                <span>请选择一个疾病</span>
            </p>
        <?php } ?>
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
