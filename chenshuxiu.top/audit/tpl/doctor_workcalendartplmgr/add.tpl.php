<?php
$pagetitle = "新建工作日历模板";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/doctor_workcalendartplmgr/addpost" method="post">
            <input type="hidden" value="<?= $doctorid ?>" name="doctorid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="width: 120px;">医生</td>
                        <td>
                            <div class="col-md-2 col-sm-1">
                                <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>疾病</td>
                        <td>
                            <div class="col-md-2 col-sm-1">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($diseases, true), 'diseaseid', '', 'form-control') ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>CODE</td>
                        <td>
                            <div class="col-md-2 col-sm-1">
                                <input class="form-control" type="text" name="code" placeholder="请填写CODE" value=""/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>标题</td>
                        <td>
                            <div class="col-md-2 col-sm-1">
                                <input class="form-control" type="text" name="title" placeholder="请填写标题" value=""/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            模板配置
                            <br>
                            <br>
                            <a target="_blank" href="https://www.bejson.com/">go BeJson</a>
                        </td>
                        <td>
                            <div class="col-md-4 col-sm-2">
                                <textarea class="form-control" name="content" rows="6" placeholder="请编辑模板内容"></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <div class="col-md-2 col-sm-1">
                                <input type="submit" class="btn btn-success" value="提交"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function() {
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
