<?php
$pagetitle = "医生用药方案  DoctorMedicinePkgs";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                <a class="btn btn-sm btn-primary" target="_blank" href="/doctormedicinepkgmgr/add?doctorid=<?= $doctorid ?>">
                    <i class="fa fa-plus push-5-r"></i>用药方案新建
                </a>
            </div>

            <div class="col-sm-11 col-xs-9">
                <div class="col-sm-3" style="float: right; padding-right: 0px;">
                    <form class="form-horizontal push-5-t" action="/doctormedicinepkgmgr/list" method="get">
                        <div class="form-group">
                            <div class="input-group">
                                <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                                <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                    <button type="submit" class="btn btn-primary">
                                        <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search">
                                        </span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clear">

            </div>
        </div>
<div class="col-md-12">
        <form action="/doctormedicinepkgmgr/posmodifypost" method="post">
            <input type="hidden" name="doctorid" value="<?= $doctorid ?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td>id</td>
                    <td>序号</td>
                    <td>医生</td>
                    <td>疾病</td>
                    <td>方案名</td>
                    <td>药品数</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($doctormedicinepkgs as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td>
                            <input type="text" class="width50" name="pos[<?= $a->id ?>]"
                                   value="<?= $a->pos ?>"/>
                        </td>
                        <td><?= $a->doctor->name ?></td>
                        <td><?= $a->disease->name ?></td>
                        <td><?= $a->name ?></td>
                        <td>
                            <a target="_blank"
                               href="/doctormedicinepkgitemmgr/list?doctormedicinepkgid=<?= $a->id ?>"><?= $a->getItemCnt() ?></a>
                        </td>
                        <td>
                            <a target="_blank"
                               href="/doctormedicinepkgmgr/modify?doctormedicinepkgid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan=20>
                        <input class="btn btn-success" type="submit" value="保存序号修改">
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </form>
</div>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<?php
$footerScript = <<<STYLE
$(function(){
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
