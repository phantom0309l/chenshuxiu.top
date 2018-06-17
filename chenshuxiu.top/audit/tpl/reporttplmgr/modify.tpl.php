<?php
$pagetitle = "修改模板";
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
            <form class="form-horizontal" action="/reporttplmgr/modifypost" method="post">
                <input type="hidden" name="reporttplid" value="<?= $reporttpl->id ?>">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="title">标题</label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" id="title" name="title"
                               value="<?= $reporttpl->title ?>"
                               placeholder="请填写标题..">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="brief">简介</label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" id="brief"
                               value="<?= $reporttpl->brief ?>"
                               name="brief" placeholder="请填写简介..">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">内容</label>
                    <div class="col-md-7">
                        <?php
                        $items = json_decode($reporttpl->content, true);
                        foreach ($config_items as $item) { echo in_array($item, $items); ?>
                            <label class="css-input css-checkbox css-checkbox-rounded css-checkbox-info">
                                <input type="checkbox" <?= in_array($item['key'], $items) ? 'checked' : '' ?>
                                       name="items[<?= $item['key'] ?>]"><span></span> <?= in_array($item, $items) ?> <?= $item['title'] ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9">
                        <button class="btn btn-sm btn-primary" type="submit">修改模板</button>
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