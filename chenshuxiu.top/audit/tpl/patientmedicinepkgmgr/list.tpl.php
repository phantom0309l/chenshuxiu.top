<?php
$pagetitle = "医生处方  PatientMedicinePkgs";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });

        $('#patient-listcond-word').autoComplete({
            type: 'patient',
            partner: '#patientid'
        });
    });
</script>

    <div class="col-md-12 contentShell">
        <section class="col-md-5 content-left">
            <div class="searchBar">
                <form action="/patientmedicinepkgmgr/list" method="get" class="pr">
                    <div class="mt10">
                        <label for="">医生：</label>

                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                    <div class="mt10">
                        <label for="">患者：</label>
                        <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                    </div>
                    <input type="submit" class="btn btn-success" value="组合刷选" />
                </form>
            </div>
            <div class="table-responsive">
                <table class="table border-top-blue tdcenter patientList">
                <thead>
                    <tr>
                        <td>患者<?=$patientid ?></td>
                        <td>创建时间</td>
                        <td>医生</td>
                        <td>用药数</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($patientmedicinepkgarr as $a) {
                        ?>
                        	<tr>
                        <td><?=Patient::getById($a['patientid'])->getMaskName() ?></td>
                        <td><?=$a['createtime'] ?></td>
                        <td><?=Doctor::getById($a['doctorid'])->name ?></td>
                        <td><?=$a['cnt'] ?></td>
                        <td>
                            <a class="showPatientMedicineHtml btn btn-primary patientid=<?=$a['patientid'] ?>" data-patientmedicinepkgid="<?=$a['id'] ?>" data-patientid="<?=$a['patientid'] ?>">详情</a>
                        </td>
                    </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
        <section class="col-md-7 content-right border1 pb10">
            <div class="mt10" id="patientmedicinepkgdetail"></div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
	$(document).on("click",".showPatientMedicineHtml",function(){
		$("#patientmedicinepkgdetail").show();

		var me = $(this);
		var patientmedicinepkgid = me.data("patientmedicinepkgid");

		$("#patientmedicinepkgdetail").html('');
		$.ajax({
			"type" : "get",
			"data" : {
				patientmedicinepkgid : patientmedicinepkgid,
			},
			"dataType" : "html",
			"url" : "/patientmedicinepkgitemmgr/listHtml",
			"success" : function(data){
				$("#patientmedicinepkgdetail").html(data);
			}
		});
	});
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
