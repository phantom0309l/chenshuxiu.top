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
$(function(){
    var shoporder_service_node = $(".searchBar").find("select").find("[value=shoporder_service]");
    var shoporder_service_node2 = $(".searchBar").find("select").find("[value=shoporder_service2]");
    shoporder_service_node.hide();
    shoporder_service_node2.hide();

    var location_search = location.search;
    if(location_search == "?fuwu=1"){
        shoporder_service_node.show();
        shoporder_service_node2.show();
    }
})
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/export_jobmgr/list" class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 control-label">类型:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(Export_Job::getTypeDescArr(),'type', $type,'js-select2 form-control') ?>
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
                    <td>任务名称</td>
                    <td>创建时间</td>
                    <td>完成时间</td>
                    <td>任务进度</td>
                    <td>任务状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($export_Jobs as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getTypeDesc() ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->getCompleteTime() ?></td>
                    <td><?= $a->progress ?> %</td>
                    <td><?= $a->getStatusDesc() ?></td>
                    <td align="center">
                        <?php if($a->isComplete()){ ?>
                        <a target="_blank" href="<?= $a->getDownloadUrl() ?>?_code_=<?= $_myuserid_ ?>">下载</a>
                        <?php }else{ ?>
                            <a href="/export_jobmgr/list">刷新页面</a>
                        <?php } ?>
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
