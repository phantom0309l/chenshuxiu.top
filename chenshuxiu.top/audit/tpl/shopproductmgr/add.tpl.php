<?php
$pagetitle = "商城商品 新建";
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
        <?php if($mydisease instanceof Disease){ ?>
        <form action="/shopproductmgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>商品类型</th>
                    <td>
                        <?php if(count($shopProductTypes) > 0){ ?>
                            <?= HtmlCtr::getRadioCtrImp(CtrHelper::toShopProductTypeCtrArray($shopProductTypes, false), 'shopproducttypeid', 0, ''); ?>
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
                        <input type="text" name="sku_code" value="" /> <span class="text-danger">(注意：要通过ERP发货的商品必须填写SKU码)</span>
                    </td>
                </tr>
                <tr>
                    <th>objtype</th>
                    <td>
                        <input type="text" name="objtype" value="<?= $objtype ?>" />
                    </td>
                </tr>
                <tr>
                    <th>objid</th>
                    <td>
                        <input type="text" name="objid" value="<?= $objid ?>" />
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
                        $picture = $obj->picture;
                        $objtype = "ShopProduct";
                        $objid = 0;
                        $objsubtype = "";
                        require_once ("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>商品标题</th>
                    <td>
                        <input style="width: 400px" type="text" name="title" value="<?= $obj ? $obj->getTitleForShopProduct():''; ?>" />
                        *
                    </td>
                </tr>
                <tr>
                    <th>生产厂家</th>
                    <td>
                        <input style="width: 400px" type="text" name="product_factory" value="<?= $obj ? $obj->company_name : ''; ?>" />
                        *
                    </td>
                </tr>
                <tr>
                    <th>是否液体</th>
                    <td>
                        <p class="text-danger"><span>这个标注很重要，一定要标注正确，否则会影响发货</span></p>
                        <?= HtmlCtr::getRadioCtrImp(CtrHelper::getShopProductIs_waterCtrArray(), 'is_water', 0, ''); ?>
                    </td>
                </tr>
                <tr>
                    <th>商品介绍</th>
                    <td>
                        <textarea rows="10" cols="120" name="content"><?= $obj ? $obj->getContentForShopProduct():'' ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>价格</th>
                    <td>
                        <input type="text" name="price_yuan" value="0" />
                        元
                    </td>
                </tr>
                <tr>
                    <th>市场价格</th>
                    <td>
                        <input type="text" name="market_price_yuan" value="0" />
                        元
                    </td>
                </tr>
                <tr>
                    <th>包装单位</th>
                    <td>
                        <input type="text" name="pack_unit" value="<?= $obj ? $obj->pack_unit:'';?>" />
                        如: 盒
                    </td>
                </tr>
                <tr>
                    <th>警戒值</th>
                    <td>
                        <input type="text" name="warning_cnt" value="" />
                    </td>
                </tr>
                <tr>
                    <th>提醒运营线</th>
                    <td>
                        <input type="text" name="notice_cnt" value="" />
                        （一般可设置为警戒值的1.5）
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
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
