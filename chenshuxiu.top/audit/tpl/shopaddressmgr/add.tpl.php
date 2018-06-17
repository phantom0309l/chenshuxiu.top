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
        <form action="/shopaddressmgr/addpost" method="post">
            <input type="hidden" id="patientid" name="patientid" value="<?=$patient->id?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th>患者</th>
                    <td>
                        <?=$patient->name?>
                    </td>
                </tr>
                <tr>
                    <th>联系人姓名</th>
                    <td>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="linkman_name" name="linkman_name">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>联系人号码</th>
                    <td>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="linkman_mobile" name="linkman_mobile">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>省/市/区</th>
                    <td>
                        <div class="col-xs-5" style="padding-left: 0px;">
                            <?php echo HtmlCtr::getAddressCtr4New('shopaddress'); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>详细地址</th>
                    <td>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="5" cols="60" id="content" name="content"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>邮编</th>
                    <td>
                        <div class="col-md-2">
                            <input class="form-control" type="text" id="postcode" name="postcode">
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
