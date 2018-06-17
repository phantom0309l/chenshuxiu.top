<?php
$pagetitle = "电话商品 新建";
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
        <form action="/callproductmgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>objtype</th>
                        <td>
                            <input type="text" name="objtype" value="<?= $objtype ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>objid</th>
                        <td>
                            <input type="text" name="objid" value="<?= $objid ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>商品标题</th>
                        <td>
                            <input style="width: 400px" type="text" name="title"
                                   value="<?= $obj ? $obj->getTitleForShopProduct() : ''; ?>"/>
                            *
                        </td>
                    </tr>
                    <tr>
                        <th>商品介绍</th>
                        <td>
                            <textarea rows="10" cols="120"
                                      name="content"><?= $obj ? $obj->getContentForShopProduct() : '' ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>价格</th>
                        <td>
                            <input type="text" name="price_yuan" value="0"/>
                            元
                        </td>
                    </tr>
                    <tr>
                        <th>市场价格</th>
                        <td>
                            <input type="text" name="market_price_yuan" value="0"/>
                            元
                        </td>
                    </tr>
                    <tr>
                        <th>包装单位</th>
                        <td>
                            <input type="text" name="pack_unit" value="<?= $obj ? $obj->pack_unit : ''; ?>"/>
                            如: 盒
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交"/>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
