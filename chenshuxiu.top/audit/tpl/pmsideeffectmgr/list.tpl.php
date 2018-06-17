<?php
$pagetitle = "副反应报告列表 PmSideEffect";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
        $('#patient-listcond-word').autoComplete({
            type: 'patient',
            partner: '#patientid',
            change: function (event, ui) {
            },
            select: function (event, ui) {
                // $('#patientid').val(ui.item.id);
            },
            close: function (event, ui) {
            }
        });
    })
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/pmsideeffectmgr/list" method="get">
                <div class="mt10">
                    <label class="col-md-1" for="" style="margin-top: 8px;padding-right: 0px;width: 65px;">医生：</label>
                    <div class="col-md-3">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                </div>
                <div class="mt10">
                    <label class="col-md-1" for="" style="margin-top: 8px;padding-right: 0px;width: 65px;">患者：</label>
                    <div class="col-md-3">
                        <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                    </div>
                </div>
                <div class="mt10">
                    <label for="">反馈的检查日期:</label>
                    从
                    <input type="text" class="calendar" style="width: 100px; height: 27px;" name="fromdate" value="<?= $fromdate ?>" />
                    到
                    <input type="text" class="calendar" style="width: 100px" name="todate" value="<?= $todate ?>" />
                    (含起止日期) &nbsp;
                    <button class="btn_style4" id="cleardate">清空日期</button>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="组合筛选">
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>pmsideeffectid</th>
                        <th>创建日期</th>
                        <th>所属医生</th>
                        <th>患者</th>
                        <th>药品</th>
                        <th>计划提醒日期</th>
                        <th>反馈的检查日期</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($pmsideeffects as $a) {
                        $pcard = null;
                        if ($a->patient instanceof Patient) {
                            $pcard = $a->patient->getMasterPcard();
                        }

                        // 关联任务
                        $optask = OpTaskDao::getOneByObj($a);
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDayHi(); ?></td>
                        <td>
                            <a href="/pmsideeffectmgr/list?doctorid=<?= $pcard->doctorid ?>"><?= $pcard->doctor->name ?></a>
                        </td>
                        <td style="text-align: left">
                            <a href="/pmsideeffectmgr/list?patientid=<?= $a->patientid ?>">
                        <?php
                        if ($a->patient instanceof Patient) {
                            echo "{$a->patientid} ";
                            echo $a->patient->getMaskName();
                        }
                        ?>
                            </a>
                        </td>
                        <td style="text-align: left">
                            <a href="/pmsideeffectmgr/list?medicineid=<?= $a->medicineid ?>">
                                <?= $a->medicine->id; ?> <?= $a->medicine->name; ?>
                            </a>
                        </td>
                        <td><?= substr($optask->plantime, 0, 10); ?></td>
                        <td><?= $a->thedate; ?></td>
                        <td>
                            <a target="_blank" href="/pmsideeffectmgr/modify?pmsideeffectid=<?= $a->id ?>">标记(修改)结果</a>
                        </td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
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
        $("#cleardate").on("click",function(){
            $(".calendar").val('');
            return false;
        });
	});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
