<?php
$pagetitle = "运营审核患者列表 ";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
            <tr>
                <td>创建时间</td>
                <td>患者</td>
                <td>医生</td>
                <td>疾病</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($list as $a) {
                    ?>
                    <tr>
                        <td><?= $a->patient->createtime ?></td>
                        <td><?= $a->patient->name ?></td>
                        <td><?= $a->patient->doctor->name ?></td>
                        <td><?= $a->patient->disease->name ?></td>
                        <td>
                        	<a target="_blank" class="btn btn-success" href="/patientmgr/list4bind?patientid=<?= $a->patientid?>">去审核</a>
                        	<button class="closeOptask btn btn-success" data-optaskid="<?=$a->id?>" data-auditorid="<?=$auditor->id?>">关闭该任务</button>
                        </td>
                    </tr>
                    <?php
                }
            ?>
            <tr>
                <td colspan=100 class="pagelink">
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
$footerScript = <<<STYLE
$(function(){
    $(".closeOptask").on("click", function(){
        var optaskid = $(this).data('optaskid');
        var auditorid = $(this).data('auditorid');

        $.ajax({
            type: "post",
            url: "/optaskmgr/closeoptaskjson",
            data: {
                "optaskid": optaskid,
                "auditorid": auditorid
            },
            dataType: "text",
            success: function (data){
				if (data == 'ok') {
                    alert("关闭任务成功");
                    window.location.href = window.location.href;
				}
            }
        });
    });
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
