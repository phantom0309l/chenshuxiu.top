<?php
$pagetitle = "运营任务检查列表 OptLogs";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form class="form-horizontal" action="/optlogmgr/list" method="get">
                <div class="form-group mt10">
                    <label class="control-label col-md-2">运营人员</label>
                    <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(), "auditorid", $auditorid, 'js-select2 form-control auditorid'); ?>
                    </div>
                    <label class="control-label col-md-2">运营操作日期</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="optask_done_date_range" name="optask_op_date_range" value="<?= $optask_op_date_range ?>" placeholder="日期范围">
                    </div>
                </div>
                <div class="form-group mt10">
                    <label class="control-label col-md-2">任务名称</label>
                    <div class="col-md-3">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getOptaskTplCtrArray(), "optasktplid", $optasktplid, 'js-select2 form-control optasktplid'); ?>
                    </div>
                    <label class="control-label col-md-2">任务时间</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="optask_plan_date_range" name="optask_plan_date_range" value="<?= $optask_plan_date_range ?>" placeholder="日期范围">
                    </div>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合筛选" />
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>患者</td>
                        <td width="180">操作时间</td>
                        <td width="220">操作人</td>
                        <td>任务类型</td>
                        <td>任务id</td>
                        <td width="150">任务计划时间</td>
                        <td width="130">任务关闭时间</td>
                        <td>操作内容</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($optlogs as $optlog) {
                        ?>
                        <tr>
                            <td><?=$optlog->id?></td>
                            <td><a target="_blank" href="/optaskmgr/listnew?patientid=<?=$optlog->optask->patientid?>&patient_name=<?=$optlog->optask->patient->name?>"><?=$optlog->optask->patient->name?></a></td>
                            <td><?=$optlog->createtime?></td>
                            <td><?=$optlog->auditor->name?></td>
                            <td><?=$optlog->optask->optasktpl->title?></td>
                            <td><?=$optlog->optaskid?></td>
                            <td><?=$optlog->optask->plantime?></td>
                            <td><?=$optlog->optask->donetime?></td>
                            <td>
                                <?php
                                    if (false !== strpos($optlog->content, '[状态变更]')) {
                                        $str = str_replace('0', '进行中', $optlog->content);
                                        $str = str_replace('1', '关闭', $str);
                                        $str = str_replace('2', '挂起', $str);

                                        echo $str;
                                    } else {
                                        echo $optlog->content;
                                    }
                                ?>
                            </td>
                            <td></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan=12>
                            <?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="clear"></div>

<?php
$footerScript = <<<XXX
$(function() {
    App.initHelper('select2');

    //日期范围选择
    laydate.render({ 
        elem: '#optask_done_date_range',
        range: '至' //或 range: '~' 来自定义分割字符
    });
    
        //日期范围选择
    laydate.render({ 
        elem: '#optask_plan_date_range',
        range: '至' //或 range: '~' 来自定义分割字符
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
