<?php
$pagetitle = "列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
    .searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <a class="btn btn-success" href="/patientrecordtplmgr/add">新建</a>
        </div>
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/patientrecordtplmgr/list" class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 control-label">疾病组:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$diseasegroupid,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
        <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>疾病组</td>
                    <td>title</td>
                    <td>ename</td>
                    <td>content</td>
                    <td>pos</td>
                    <td>is_show</td>
                    <td>style_class</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patientRecordTpls as $i => $a) { ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->diseasegroup->name ?></td>
                    <td><?= $a->title ?></td>
                    <td><?= $a->ename ?></td>
                    <td><?= $a->content ?></td>
                    <td><?= $a->pos ?></td>
                    <td><?= $a->is_show ?></td>
                    <td><?= $a->style_class ?></td>

                    <td align="center">
                        <a target="_blank" href="/patientrecordtplmgr/modify?patientrecordtplid=<?=$a->id ?>">修改</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
