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
        <form action="/shopaddressmgr/modifypost" method="post">
            <input type="hidden" id="shopaddressid" name="shopaddressid" value="<?=$shopaddress->id?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th>患者</th>
                    <td>
                        <?=$shopaddress->patient->name?>
                    </td>
                </tr>
                <tr>
                    <th>联系人姓名</th>
                    <td>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="linkman_name" name="linkman_name" value="<?=$shopaddress->linkman_name?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>联系人号码</th>
                    <td>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="linkman_mobile" name="linkman_mobile" value="<?=$shopaddress->linkman_mobile?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>省/市/区</th>
                    <td>
                        <div class="col-xs-5" style="padding-left: 0px;">
                            <?php echo HtmlCtr::getAddressCtr4New('shopaddress', $shopaddress->xprovinceid, $shopaddress->xcityid, $shopaddress->xcountyid); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>详细地址</th>
                    <td>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="5" cols="60" id="content" name="content"><?=$shopaddress->content?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>邮编</th>
                    <td>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="postcode" name="postcode" value="<?=$shopaddress->postcode?>">
                        </div>
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
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
