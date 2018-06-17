<?php
$pagetitle = "修改疾病菜单";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
    $img_uri . "/v5/page/audit/checkuptplmenumgr/edit.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/page/audit/checkuptplmenumgr/edit.js",
    $img_uri . "/v5/page/audit/checkuptplmenumgr/modify/modify.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT
    var data = {$menus};
    var subData = data.concat();
    data.unshift({
    id: 'parent_node',
    text: '父菜单'
    })

    var myData = {$myMenus};
SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php"; ?>
    <div class="content-div">
        <section class="col-md-12">
            <!-- 疾病 Begin -->
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">疾病</h3>
                </div>
                <div class="block-content" style="background-color: #f2f2f2;">
                    <div class="mb20">
                        <?php
                        foreach ($diseases as $disease) { ?>
                            <label class="css-input css-checkbox css-checkbox-primary mr5">
                                <input class="J_disease" type="checkbox" name="diseaseids"
                                       value="<?= $disease->id ?>"
                                       disabled
                                    <?= $disease->id == $diseaseid ? 'checked' : '' ?>
                                ><span></span> <?= $disease->name ?>
                            </label>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>
            <!-- 疾病 End -->
            <!-- 菜单 Begin -->
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <ul class="block-options">
                        <li>
                            <button type="button" data-toggle="block-option" data-action="content_toggle"><i
                                        class="si si-arrow-up"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">菜单</h3>
                </div>
                <div class="block-content">
                    <div class="mb20"
                         style="background-color: rgb(228,232,241); padding-top: 20px; padding-bottom: 20px;">
                        <div class="col-md-12 mb10">
                            <button id="create_parent_menu" class="btn btn-primary">创建父菜单</button>
                        </div>
                        <div class="clear"></div>
                        <ul id="menus" class="nav-main" style="padding-left: 15px;">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12 tc mb20">
                <button class="btn btn-minw btn-success J_submit" type="submit" data-doctorid="<?= $doctor->id ?>"
                        data-checkuptplmenuid="<?= $checkuptplmenuid ?>">
                    <i class="fa"></i> 保存
                </button>
            </div>
        </section>
    </div>
    <div class="clear"></div>
</div>
<?php
$footerScript = <<<STYLE
$(function() {
    $('#menus').sortable();
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
