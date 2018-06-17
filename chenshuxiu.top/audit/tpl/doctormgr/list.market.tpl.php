<?php
$pagetitle = "医生列表 Doctors";
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
        <?php if( 10020 == $myauditor->id){ ?>
            <div class="searchBar">
                <form action="/doctormgr/list" method="get" class="pr">
                    <div class="mt10">
                        <label>按医院筛选：</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toHospitalCtrArray($hospitals,true),"hospitalid",$hospitalid,'f18'); ?>
                    </div>
                    <div class="mt10">
                        <label>按市场负责人筛选：</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),"auditorid_market",$auditorid_market,'f18');?>
                    </div>
                    <div class="mt10">
                        <label>状态：</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorStatusCtrArray(true),"status",$status,'f18');?>
                    </div>
                    <div class="mt10">
                        <label for="">按医生名模糊查找：</label>
                        <input type="text" name="doctor_name" value="<?= $doctor_name ?>" />
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选" />
                    </div>
                </form>
            </div>
        <?php }?>

            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>创建日期</td>
                        <td>所属医院</td>
                        <td>疾病</td>
                        <td>姓名</td>
                        <td>市场责任人</td>
                        <td>code</td>
                        <td>user:username</td>
                        <td>状态</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($doctors as $a) {
                ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->hospital->shortname ?></td>
                        <td width="180">
                            <?= $a->getDiseaseNamesStr()?>
                        </td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->marketauditor->name ?> </td>
                        <td><?= $a->code ?></td>
                        <td><?= $a->user->username ?></td>
                        <td><?= $a->status ? '开通' : '未开通' ?></td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=10>
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
    $("select").on("change", function () {
      var val = parseInt($(this).val());
      var url = val == 0 ? location.pathname : location.pathname + '?'+$(this).attr("name")+'=' + val;
      window.location.href = url;
    });
    $("select#status").on("change", function () {
      var val = parseInt($(this).val());
      var url = val == -1 ? location.pathname : location.pathname + '?status=' + val;
      window.location.href = url;
    });
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
