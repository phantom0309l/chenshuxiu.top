<?php
$pagetitle = "医生后台助理列表";
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

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form class="form-horizontal" action="/assistantmgr/list" method="get" class="pr">
                    <div class="form-group mt10">
                        <label class="control-label col-md-1" style="width: 60px;">医生</label>
                        <div class="col-md-3">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选" />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table  table-bordered">
                <thead>
                    <tr>
                        <td width=140>ID</td>
                        <td width=140>创建日期</td>
                        <td width=140>用户id</td>
                        <td width=140>登录名</td>
                        <td width=140>姓名</td>
                        <td width=140>所属医生</td>
                        <td width=140>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($assistants as $a) {
                            ?>
                            <tr>
                                <td><?= $a->id ?></td>
                                <td><?= $a->createtime ?></td>
                                <td><?= $a->userid ?></td>
                                <td><?= $a->user->username ?></td>
                                <td><?= $a->name ?></td>
                                <td><?= $a->doctor->name ?></td>
                                <td><a href="javascript:" xhref="/assistantmgr/privilegelist?assistantid=<?=$a->id?>">查看权限</a></td>
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
$(function() {
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
