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
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form class="form-horizontal" action="/auditoroplogmgr/list" method="get">
                <input type="hidden" id="patientid" name="patientid" value="<?=$patientid?>">
                <div class="form-group mt10">
                    <label class="control-label col-md-1">运营</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(), "auditorid", $auditorid, 'js-select2 form-control levelBox'); ?>
                    </div>
                    <label class="control-label col-md-1">类型</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(AuditorOpLog::getCodes(), "code", $code, 'js-select2 form-control levelBox'); ?>
                    </div>
                    <label class="control-label col-md-2">日期范围(左闭右开)</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="date_range" name="date_range"
                               value="<?= $date_range ?>" placeholder="日期范围">
                    </div>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合筛选"/>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td width="100">id</td>
                    <td width="160">操作时间</td>
                    <td width="80">操作人</td>
                    <td width="100">患者</td>
                    <td width="140">类型</td>
                    <td>内容</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditorOpLogs as $i => $a) { ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->createtime ?></td>
                        <td><span class="label label-success"><?= $a->auditor->name ?></span></td>
                        <td><span class="label label-danger"><?= $a->patient->name ?></span></td>
                        <td><span class="label label-primary"><?= $a->getCodeStr() ?></span></td>
                        <td><?= $a->content ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        App.initHelper('select2');

        //日期范围选择
        laydate.render({
            elem: '#date_range',
            range: '至' //或 range: '~' 来自定义分割字符
        });
    });
</script>

<?php
$footerScript = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
