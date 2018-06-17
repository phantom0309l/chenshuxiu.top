<?php
$pagetitle = "用户感谢展示页 Letter";
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
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/lettermgr/list" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">医生 :</label>
                    <div class="col-sm-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否审核 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getIsauditCtrArray(),'audit_status', $audit_status, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否在医生端显示 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getIsshowCtrArray(),'show_in_doctor', $show_in_doctor, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否在运营端显示 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getIsshowCtrArray(),'show_in_auditor', $show_in_auditor, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否绑定方寸管理端 :</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getIsBindingCtrArray(),'binding_in_fc', $binding_in_fc, 'css-radio-success')?>
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
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>微信用户</td>
                <td width="80">患者名字</td>
                <td width="80">医生名字</td>
                <td width="100">当前疾病</td>
                <td width="100">创建时间</td>
                <td width="100">审核时间</td>
                <td>内容</td>
                <td width="180">是否在医生端显示</td>
                <td width="180">是否在运营端显示</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($letters as $a) { ?>
                <tr>
                    <td><?= $a->wxuser->nickname; ?></td>
                    <td>
                        <?php
                        if ($a->patient instanceof Patient) {
                            echo $a->patient->getMaskName();
                        }
                        ?>
                    </td>
                    <td><?= $a->doctor->name ?></td>
                    <td>
                    	<?php
                        if ($a->patient instanceof Patient) {
                            echo $a->patient->disease->name;
                        }
                        ?>
                    </td>
                    <td><?= $a->getCreateDay() ?> </td>
                    <td><?= substr($a->audit_time, 0, 10) ?> </td>
                    <td><?= $a->content ?> </td>
                    <td data-letterid="<?= $a->id ?>">
                        <span data-showindoctor="1"
                              class="showindoctorBtn btn btn-default <?= $a->audit_status == 1 && $a->show_in_doctor == 1 ? 'btn-primary' : '' ?>">显示</span>
                        <span data-showindoctor="0"
                              class="showindoctorBtn btn btn-default <?= $a->audit_status == 1 && $a->show_in_doctor == 0 ? 'btn-primary' : '' ?>">关闭</span>
                    </td>
                    <td data-letterid="<?= $a->id ?>">
                        <span data-showinauditor="1"
                              class="showinauditorBtn btn btn-default <?= $a->show_in_auditor == 1 ? 'btn-primary' : '' ?>">显示</span>
                        <span data-showinauditor="0"
                              class="showinauditorBtn btn btn-default <?= $a->show_in_auditor == 0 ? 'btn-primary' : '' ?>">关闭</span>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="9" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
            </tr>
            </tbody>
        </table>
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
        $(function () {
            $(".showindoctorBtn").on("click", function () {
                var me = $(this);
                var showindoctor = me.data("showindoctor");
                var letterid = me.parents("td").data("letterid");
                if(me.hasClass('btn-primary')){
                    alert("请勿重复点击。");
                }
                $.ajax({
                    url: '/lettermgr/modifyShowInDoctorJson',
                    type: 'post',
                    dataType: 'text',
                    data: {showindoctor: showindoctor, letterid: letterid}
                })
                    .done(function () {
                        me.addClass('btn-primary').siblings().removeClass('btn-primary');
                    })
                    .fail(function () {
                    })
                    .always(function () {
                    });

            });
            $(".showinauditorBtn").on("click", function () {
                var me = $(this);
                var showinauditor = me.data("showinauditor");
                var letterid = me.parents("td").data("letterid");
                if(me.hasClass('btn-primary')){
                    alert("请勿重复点击。");
                }
                $.ajax({
                    url: '/lettermgr/modifyShowInAuditorJson',
                    type: 'post',
                    dataType: 'text',
                    data: {showinauditor: showinauditor, letterid: letterid}
                })
                    .done(function () {
                        me.addClass('btn-primary').siblings().removeClass('btn-primary');
                    })
                    .fail(function () {
                    })
                    .always(function () {
                    });

            })
        })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
