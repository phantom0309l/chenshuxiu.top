<?php
$pagetitle = "工作日历模板";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
    $img_uri . "/v5/page/audit/checkuptplmenumgr/edit.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/page/audit/checkuptplmenumgr/edit.js",
    $img_uri . "/v5/page/audit/checkuptplmenumgr/add/add.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT
    var data = {$configs};
    var subData = data.concat();
    data.unshift({
    id: 'parent_node',
    text: '父菜单'
    })

    var myData = [];
SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <div class="content-div">
        <section class="col-md-12">
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
                    <div class="mb20" style="background-color: #e4e8f1; padding-top: 20px;">
                        <div class="col-md-12 mb20">
                            <button id="create_parent_menu" class="btn btn-primary">创建父菜单</button>
                        </div>
                        <div class="clear"></div>
                        <ul id="menus" class="nav-main" style="padding-left: 15px;">
                            <li class="open">
                                <div class="menu-item menu-parent">
                                    <input type="text" class="form-control menu-parent-key" style="" placeholder="请输入父菜单名称" value="${text}"/>
                                    <input type="text" class="form-control menu-parent-name" style="" placeholder="请输入父菜单名称" value="${text}"/>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-default menu-remove" type="button">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                        <button class="btn btn-default menu-add" type="button" ${id != 'parent_node' ? 'disabled' : ''}>
                                        <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <ul></ul>
                            </li>
                            <li>
                                <input type="text" name="code" placeholder="请填写唯一code">
                                <input type="text" name="code" placeholder="请填写标题">
                                <div>
                                    <span>内容</span>
                                    <select>
                                        <option>选择类型</option>
                                        <option value="checkbox">复选</option>
                                        <option value="radio">单选</option>
                                        <option value="text">文本</option>
                                        <option value="textarea">文本域</option>
                                    </select>
                                    <select>
                                        <option>是否有</option>
                                        <option value="checkbox">复选</option>
                                        <option value="radio">单选</option>
                                        <option value="text">文本</option>
                                        <option value="textarea">文本域</option>
                                    </select>
                                    <span>
                                    <label class="css-input css-checkbox css-checkbox-primary">
                                        <input type="checkbox" checked=""><span></span> 默认选中
                                    </label>
                                </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- 菜单 End -->
            <div class="col-md-12 tc">
                <button class="btn btn-minw btn-success J_submit" type="submit" data-doctorid="<?= $doctor->id ?>">
                    <i class="fa"></i> 保存
                </button>
            </div>
        </section>
    </div>
    <div class="clear"></div>
</div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
