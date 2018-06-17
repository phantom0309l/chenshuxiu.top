<?php
$pagetitle = "医生操作日志列表";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
    .label-width {
        width: 100px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form class="form-horizontal pr" action="/doctordboplogmgr/list" method="get">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <label class="col-xs-1 control-label label-width" for="">医生</label>
                        <div class="col-xs-2">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                        <label class="col-xs-1 control-label label-width" for="word">模糊查找</label>
                        <div class="col-xs-2">
                            <input class="form-control" type="text" id="word" name="word" value="<?=$word?>" placeholder="患者id或姓名">
                        </div>
                        <div class="col-xs-2">
                            <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table  table-bordered">
                <thead>
                    <tr>
                        <td width=140>ID</td>
                        <td width=140>创建日期</td>
                        <td width=140>患者姓名</td>
                        <td width=140>医生</td>
                        <td>修改日志</td>
                        <td width=140>操作人</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($doctordboplogs as $a) {
                            ?>
                            <tr>
                                <td><?= $a->id ?></td>
                                <td><?= $a->createtime ?></td>
                                <td><?= $a->patient->name ?></td>
                                <td><?= $a->doctorid ?> <?= $a->doctor->name ?></td>
                                <td><?= $a->content ?></td>
                                <td><?= $a->userid ?> <?= $a->username ?></td>
                            </tr>
                            <?php
                        }
                    ?>
                    <tr>
                        <td colspan=100 class="pagelink">
                            <?php include $dtpl."/pagelink.ctr.php";  ?>
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
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
