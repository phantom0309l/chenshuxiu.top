<?php
$pagetitle = "门诊记录列表 RevisitRecords";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.trOnSeleted {
	background-color: #CCCCFF;
}

.trOnMouseOver {
	background-color: #CCCCFF;
}

.isclosed_objid {
	background-color: #e6e6e6
}

.isclosed {
	background-color: #eeeeee
}

.objid {
	background-color: #dff8df
}
STYLE;
$pageScript = <<<SCRIPT
    function over(tr){
        var className = $(tr).attr('id');
    	$(tr).removeClass(className).addClass('trOnMouseOver');
    }
    function out(tr){
    	var className = $(tr).attr('id');
    	$(tr).removeClass('trOnMouseOver').addClass(className);
    }

    $(function(){
    	$(".showRevisitRecordOneHtml").on("click",function(){
        	var className = $(this).parents('tr').attr('id');
    		$("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver").removeClass(className);
           	$(this).parents("tr").addClass("trOnSeleted");
       	});
    });
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

<div class="col-md-12" id="top">
        <section class="col-md-5 content-left">
        <div class=searchBar>
                <input type="hidden" name="revisitrecordid" value="<?= $revisitrecordid?>">
                <input type="hidden" name="isclick" value="<?= $isclick ?>">
                <form action="/revisitrecordmgr/list" method="get" class="pr">
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
                    <input type="submit" class="btn btn-success" value="组合刷选" />
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>患者</th>
                        <th>医生</th>
                        <th>创建日期</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($revisitrecords as $a ){ ?>
                <tr onmouseover="over(this)" onmouseout="out(this)">
                        <td>
                            <?php
                                if ($a->patient instanceof Patient) {
                                    echo $a->patient->getMaskName();
                                }
                            ?>
                        </td>
                        <td><?=$a->doctor->name ?></td>
                        <td><?=$a->thedate ?></td>
                        <td>
                            <a href="#top" data-revisitrecordid="<?= $a->id ?>" class="showRevisitRecordOneHtml revisitrecordid-<?=$a->id ?>">详情</a>
                        <?php
                if ($a->isEmpty()) {
                    ?>
                            <a class="delete" data-patient_name="<?= $a->patient->name ?>" data-revisitrecordid="<?= $a->id ?>">删除</a>
                        <?php
                }
                ?>
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
        <section class="col-md-7 content-right pb10">
            <div id="RevisitRecordHtmlShell" class="contentBoxBox"></div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
    	showpatientmedicinepkgitem();

        $(document).on("click", ".showRevisitRecordOneHtml", function() {
            $(".content-right").show();
            var me = $(this);
            var revisitrecordid = me.data("revisitrecordid");

            showclick(revisitrecordid);
        });

		function showpatientmedicinepkgitem(){
			var revisitrecordid = $("input[name=revisitrecordid]").val();
	        var isclick = $("input[name=isclick]").val();
	        if(isclick == 1){
	            $(".content-right").show();

	            showclick(revisitrecordid);
				$(".revisitrecordid-" + revisitrecordid).click();
	        }
		};

        function showclick(revisitrecordid){
        	$("#RevisitRecordHtmlShell").html('');

            $.ajax({
                "type" : "get",
                "data" : {
                    revisitrecordid : revisitrecordid
                },
                "dataType" : "html",
                "url" : "/revisitrecordmgr/oneHtml",
                "success" : function(data) {
                    $("#RevisitRecordHtmlShell").html(data);
                }
            });
        };

        $(document).on("click", ".delete", function() {
            var me = $(this);
            var revisitrecordid = me.data("revisitrecordid");
            var patient_name = me.data("patient_name");

            if(false == confirm("确认删除[" + patient_name + "]的门诊记录吗？")){
                return ;
            }

            var tr = $(this).parents("tr");
            $.ajax({
                "type" : "get",
                "data" : {
                    revisitrecordid : revisitrecordid
                },
                "dataType" : "html",
                "url" : "/revisitrecordmgr/deleteJson",
                "success" : function(data) {
                    if(data == 'success'){
                        tr.remove();
                        alert("删除成功");
                    }else{
                        alert("删除失败");
                    }
                }
            });
        });

    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
