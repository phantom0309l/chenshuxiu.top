<?php
$pagetitle = "检查报告修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .form-group .control-label {
        width: 90px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php"; ?>
        <div class="content-div">
            <section class="col-md-12 block-content">
                <form class="form-horizontal" action="/checkuptplmgr/modifyofdoctorpost" method="post">
                    <input type="hidden" name="checkuptplid" value="<?= $checkuptpl->id ?>">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="groupstr">检查分组</label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" id="groupstr" name="groupstr"
                                   value="<?= $checkuptpl->groupstr ?>"
                                   placeholder="请填写检查分组..">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="ename">ename</label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" id="ename" name="ename"
                                   value="<?= $checkuptpl->ename ?>"
                                   placeholder="请填写ename..">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="title">标题</label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" id="title" name="title"
                                   value="<?= $checkuptpl->title ?>"
                                   placeholder="请填写标题..">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="is_in_tkt">约复诊</label>
                        <div class="col-md-7">
                            <label class="css-input css-radio css-radio-primary push-10-r">
                                <input type="radio" name="is_in_tkt" value="1" <?= $checkuptpl->is_in_tkt ? 'checked' : '' ?>><span></span> 显示
                            </label>
                            <label class="css-input css-radio css-radio-primary">
                                <input type="radio" name="is_in_tkt" value="0" <?= $checkuptpl->is_in_tkt ? '' : 'checked' ?>><span></span> 不显示
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="is_selected">在约复诊默认选中</label>
                        <div class="col-md-7">
                            <label class="css-input css-radio css-radio-primary push-10-r">
                                <input type="radio" name="is_selected" value="1" <?= $checkuptpl->is_selected ? 'checked' : '' ?>><span></span> 选中
                            </label>
                            <label class="css-input css-radio css-radio-primary">
                                <input type="radio" name="is_selected" value="0" <?= $checkuptpl->is_selected ? '' : 'checked' ?>><span></span> 不选中
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="is_in_admin">问卷</label>
                        <div class="col-md-7">
                            <label class="css-input css-radio css-radio-primary push-10-r">
                                <input type="radio" name="is_in_admin" value="1" <?= $checkuptpl->is_in_admin ? 'checked' : '' ?>><span></span> 有&nbsp;&nbsp;&nbsp;&nbsp;
                            </label>
                            <label class="css-input css-radio css-radio-primary">
                                <input type="radio" name="is_in_admin" value="0" <?= $checkuptpl->is_in_admin ? '' : 'checked' ?>><span></span> 没有
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="brief">摘要</label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" id="brief"
                                   value="<?= $checkuptpl->brief ?>"
                                   name="brief" placeholder="请填写摘要..">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="content">内容</label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" id="content"
                                   value="<?= $checkuptpl->content ?>"
                                   name="content" placeholder="请填写内容..">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <button class="btn btn-minw btn-success" type="submit">保存</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>