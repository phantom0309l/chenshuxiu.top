<?php
$pagetitle = "新建临床试验";
$cssFiles = [
    $img_uri . "/v5/plugin/weditor/css/wangEditor.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
    $img_uri . "/v5/page/wx/clinicaltest/base.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v5/plugin/weditor/js/wangEditor.min.js",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form class="form-horizontal" action="/clinicaltestmgr/addpost" method="post">
            <div class="form-group">
                <label class="col-md-1 col-sm-2 col-xs-3 control-label" for="list_title">列表标题</label>
                <div class="col-md-6 col-sm-8 col-xs-9">
                    <input class="form-control" type="text" id="list_title" name="list_title" placeholder="请输入列表标题">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 col-sm-2 col-xs-3 control-label" for="title">详情标题</label>
                <div class="col-md-6 col-sm-8 col-xs-9">
                    <input class="form-control" type="text" id="title" name="title" placeholder="请输入详情标题">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 col-sm-2 col-xs-3 control-label" for="brief">简介</label>
                <div class="col-md-6 col-sm-8 col-xs-9">
                    <textarea class="form-control" id="brief" name="brief" rows=4 placeholder="请输入简介"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 col-sm-2 col-xs-3 control-label" for="content">内容</label>
                <div class="col-md-6 col-sm-8 col-xs-9">
                    <textarea class="form-control" id="content" name="content" rows=20 placeholder="请输入内容"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 col-sm-2 col-xs-3 control-label" for="remark">运营备注</label>
                <div class="col-md-6 col-sm-8 col-xs-9">
                    <textarea class="form-control" id="remark" name="remark" rows=4 placeholder="请输入运营备注"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 col-sm-2 col-xs-3 control-label">状态</label>
                <div class="col-md-6 col-sm-8 col-xs-9">
                    <label class="css-input css-radio css-radio-success push-10-r">
                        <input type="radio" name="status" value="1" checked><span></span> 有效
                    </label>
                    <label class="css-input css-radio css-radio-danger push-10-r">
                        <input type="radio" name="status" value="0"><span></span> 无效
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-9 col-md-offset-1">
                    <button class="btn btn-sm btn-primary" type="submit">保存</button>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function(){
    App.initHelper('select2');
    
    var editor = new wangEditor('content');
    editor.config.hideLinkImg = true;
    editor.config.uploadImgFileName = 'imgurl'
    editor.config.uploadImgUrl = '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial&fromWeditor=1';

    editor.create();
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
