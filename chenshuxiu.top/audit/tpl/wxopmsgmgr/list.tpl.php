<?php
$pagetitle = "医生-医助交互 WxOpMsgs";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
	"{$img_uri}/v3/js/amr/amrnb.js",
	"{$img_uri}/v3/js/amr/amritem.js",
	"{$img_uri}/v5/plugin/echarts/echarts.js",
	"{$img_uri}/v3/audit_patientmgr_list.js?v=2018022201",
	"{$img_uri}/v5/common/dealwithtpl.js?v=2018050401",
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

    <div class="col-md-12 contentShell">
        <section class="col-md-6 content-left">
            <div class="searchBar">
                <form action="/wxopmsgmgr/list" method="get" class="form-horizontal">
					<div class="form-group">
						<label class="col-xs-12" for="">医生</label>
						<div class="col-sm-5">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
					</div>
                    <div class="form-group">
						<label class="col-xs-12" for="patient_name">按患者姓名</label>
						<div class="col-sm-5">
							<input class='form-control' type="text" id="patient_name" name="patient_name" value="<?= $patient_name ?>" />
                        </div>
                    </div>
					<div class="form-group">
                        <div class="col-sm-9">
                            <button class="btn btn-sm btn-primary" type="submit">查询</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table tdcenter">
                <thead>
                    <tr>
                        <td>患者</td>
                        <td>医生</td>
                        <td>汇报日期</td>
                        <td>答复日期</td>
                        <td>记录数量</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
	               	<?php
	                foreach ($wxopmsg_group as $a) {
	                    ?>
		               	    <tr class="trOnSeleted">
		                        <td><?=Patient::getById($a['patientid'])->getMaskName() ?></td>
		                        <td><?=Doctor::getById($a['doctorid'])->name ?></td>
								<?php
									if ($a['wxopmsgid']) {
				                        if (WxOpMsg::getById($a['wxopmsgid'])->auditorid > 0) { ?>
											<td><?=$a['createtime'] ?></td>
				                        	<td></td>
										<?php } else { ?>
										<td></td>
				                        <td><?=$a['createtime'] ?></td>
										<?php }
									} else { ?>
										<td></td>
				                        <td></td>
										<?php
									}
								?>
								<td><?=$a['cnt'] ?></td>
		                        <td>
		                            <a class="showWxOpMsgHtml patientid-<?=$a['patientid'] ?> cursor-pointer" data-doctorid="<?=$a['doctorid'] ?>" data-patientid="<?=$a['patientid'] ?>">查看</a>
		                        </td>
		                    </tr>
	           	    	<?php }
	            	?>
                	<tr>
                        <td colspan=10>
							<?php include $dtpl . "/pagelink.ctr.php"; ?>
                    	</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
        <section class="col-md-6 content-right border1 pb10">
            <div class="mt10" id="wxopmsgreply"></div>
            <div class="mt10" id="pipeWxOpMsgDetail"></div>
            <div class="mt10" id="wxopmsgshowMore" style="display: none">
                <a class="btn btn-default showMore">查看更多</a>
            </div>
        </section>
    </div>
    <div class="clear"></div>
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
        <div class="slides"></div>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>
    <div id='answersheet' class="col-md-4 pull-right none">
        <div class="panel panel-primary">
            <div class="panel-heading" id="answersheet-title"></div>
            <div id='details' class="panel-body"></div>
        </div>
        <span id="answersheet-close">x</span>
    </div>
    <div id="goTop" class="none">Top</div>

<?php
$footerScript = <<<XXX
$(function(){
	function over(tr){
    	$(tr).addClass('trOnMouseOver');
    }
    function out(tr){
    	$(tr).removeClass('trOnMouseOver');
    }
    $(function () {
        $(".showPatientOneHtml").on("click",function(){
           	$("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver");
           	$(this).parents("tr").addClass("trOnSeleted");
       	});
    });

	$(document).on("click",".showWxOpMsgHtml",function(){
		$("#wxopmsgshowMore").show();

		var me = $(this);
		var patientid = me.data("patientid");

		$("#wxopmsgreply").html('');
		$.ajax({
			"type" : "get",
			"data" : {
				patientid : patientid,
			},
			"dataType" : "html",
			"url" : "/wxopmsgmgr/reply",
			"success" : function(data){
				$("#wxopmsgreply").html(data);
			}
		});

		$("#pipeWxOpMsgDetail").html('');
		$.ajax({
			"type" : "get",
			"data" : {
				patientid : patientid,
				page_size : 10
			},
			"dataType" : "html",
			"url" : "/wxopmsgmgr/listHtml",
			"success" : function(data){
				$("#pipeWxOpMsgDetail").html(data);
			}
		});
	});
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
