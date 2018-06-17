<?php
$pagetitle = "员工组列表 AuditorGroup";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/auditorgroupmgr/add">员工组新建</a>
        </div>
        <div class="searchBar clearfix">
            <form action="/auditorgroupmgr/list" method="get" class="form-horizontal pr">
                <div class="form-group">
                    <label class="col-md-2 control-label">类型：</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(AuditorGroup::getTypeArr(true),"type", $type,'js-select2 form-control'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" class="btn btn-primary btn-block" value="筛选">
                    </div>
                </div>
            </form>

        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th rowspan="2">序号</th>
                    <th rowspan="2">id</th>
                    <th rowspan="2">创建日期</th>
                    <th rowspan="2">类型</th>
                    <th rowspan="2">ename</th>
                    <th rowspan="2">名字</th>
                    <th rowspan="2">组内人员</th>
                    <th rowspan="2">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($auditorGroups as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->type ?></td>
                        <td><?= $a->ename ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= implode(',', AuditorDao::getNamesByAuditorGroup($a)) ?></td>
                        <td>
                            <a target="_blank" class="btn btn-success" href="/auditorgroupmgr/modify?auditorgroupid=<?= $a->id ?>">修改</a>
                            <a target="_blank" class="btn btn-danger J_delete" data-auditorgroupid="<?= $a->id ?>">删除</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function () {
    App.initHelper('select2');

    $(".J_delete").on("click", function () {
        if (!confirm('确定删除吗？')) {
            return false;
        }

        var me = $(this);
        var auditorgroupid = me.data('auditorgroupid');

        $.ajax({
            type: "post",
            url: "/auditorgroupmgr/ajaxdeletepost",
            data:{
                "auditorgroupid" : auditorgroupid
            },
            dataType: "json",
            success : function(data){
                if(data.errno === '0'){
                    window.location.reload();
                }else{
                    alert(data.errmsg);
                }
            }
        });
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
