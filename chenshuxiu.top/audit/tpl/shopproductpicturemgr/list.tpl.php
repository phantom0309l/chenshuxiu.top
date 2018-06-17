<?php
$pagetitle = "商城商品配图 ShopProductPictures";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
            <div class="mt10 mb10 p10 border1">
                <a href="/shopproductmgr/one?shopproductid=<?= $shopProduct->id?>">
                    商品编号: <?= $shopProduct->id?>
                    <br />
                    商品标题: <?= $shopProduct->title?>
                    <br />
                    <?php if ($shopProduct->picture instanceof Picture) { ?>
                        <img alt="" src="<?=$shopProduct->picture->getSrc(100,100) ?>">
                    <?php } ?>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered col-md-10">
                <thead>
                    <tr>
                        <td width="40">序号</td>
                        <td width="90">id</td>
                        <td width="150">创建时间</td>
                        <td width="160">配图</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($shopProductPictures as $a) {
                        ?>
                    <tr>
                        <td><?= $a->pos?></td>
                        <td><?= $a->id ?></td>
                        <td><?= $a->createtime?></td>
                        <td>
                            <?php if ($a->picture instanceof Picture) { ?>
                                <img alt="" src="<?=$a->picture->getSrc(150,150) ?>">
                            <?php } ?>
                        </td>
                        <td>
                            <a target="_blank" href="/shopproductpicturemgr/deletepost?shopproductpictureid=<?=$a->id ?>">删除</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
            <div class="mt10">
                增加新配图:
                <form action="/shopproductpicturemgr/addpost">
                    <input type="hidden" name="shopproductid" value="<?= $shopProduct->id ?>" />
                <?php
                $picWidth = 150;
                $picHeight = 150;
                $pictureInputName = "pictureid";
                $isCut = false;
                $picture = null;
                $objtype = "ShopProductPicture";
                $objid = 0;
                $objsubtype = "";
                require_once ("$dtpl/picture.ctr.php");
                ?>
                    <br />
                    <input type="submit" class="btn btn-success" value="提交" />
                </form>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>