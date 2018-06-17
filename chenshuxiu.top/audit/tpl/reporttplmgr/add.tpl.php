<?php
$pagetitle = "新建模板";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .form-group .control-label {
        width: 70px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12 block-content">
            <form class="form-horizontal" action="/reporttplmgr/addpost" method="post">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="title">标题</label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" id="title" name="title"
                               placeholder="请填写标题..">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="brief">简介</label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" id="brief"
                               name="brief" placeholder="请填写简介..">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">内容</label>
                    <div class="col-md-7">
                        <?php
                        foreach ($config_items as $item) { ?>
                            <label class="css-input css-checkbox css-checkbox-rounded css-checkbox-info">
                                <input type="checkbox" <?= $item['checked'] ? 'checked' : '' ?>
                                       name="items[<?= $item['key'] ?>]"><span></span> <?= $item['title'] ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <button class="btn btn-sm btn-primary" type="submit">创建模板</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>