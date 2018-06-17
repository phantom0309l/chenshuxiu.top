<?php
$pagetitle = "工作日历模板列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <a class="btn btn-success" href="/doctor_workcalendartplmgr/add?doctorid=<?= $doctor->id ?>">新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                <tr>
                    <th class="tc" style="width: 50px">ID</th>
                    <th>创建时间</th>
                    <th class="tc">医生</th>
                    <th class="tc">疾病</th>
                    <th class="tc">CODE</th>
                    <th class="tc">标题</th>
                    <th class="tc">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($workcalendartpls as $workcalendartpl) {
                    ?>
                    <tr>
                        <td class="tc"><?= $workcalendartpl->id ?></td>
                        <td><?= $workcalendartpl->createtime ?></td>
                        <td class="tc"><?= $workcalendartpl->doctorid == 0 ? '全部医生' : $workcalendartpl->doctor->name ?></td>
                        <td class="tc"><?= $workcalendartpl->diseaseid == 0 ? '全部疾病' : $workcalendartpl->disease->name ?></td>
                        <td class="tc"><?= $workcalendartpl->code ?></td>
                        <td class="tc"><?= $workcalendartpl->title ?></td>
                        <td align="center">
                            <a class="cursor-pointer J_delete" data-workcalendartplid="<?= $workcalendartpl->id ?>">删除</a>
                            <a target="_blank" href="/doctor_workcalendartplmgr/modify?workcalendartplid=<?= $workcalendartpl->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php
$footerScript = <<<STYLE
$(function() {
    $('.J_delete').on('click', function() {
        if (!confirm('确定删除？')) {
            return false;
        }
        var workcalendartplid = $(this).data('workcalendartplid');
        window.location.href = '/doctor_workcalendartplmgr/deletePost?workcalendartplid=' + workcalendartplid;
    })
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
