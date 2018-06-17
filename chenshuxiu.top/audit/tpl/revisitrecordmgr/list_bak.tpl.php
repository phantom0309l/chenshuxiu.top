<?php
$pagetitle = "门诊记录列表 RevisitRecords";
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
			<div class=searchBar>
                <form action="/revisitrecordmgr/list" method="get" class="pr">
                    <div style="display: inline-block;">
                        <label for="">医生：</label>
                        <div class="col-xs-2">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>
                	</div>
                    <div class="mt10">
                        <label for="">按患者姓名：</label>
                        <input type="text" name="patient_name" value="<?= $patient_name ?>" />
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
                        <th>复诊日期</th>
                        <th>加号单id</th>
                        <th>用药套餐id</th>
                        <th>内容</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($revisitrecords as $a) {
                        ?>
                            <tr>
                        <td>
                            <?php
                                if ($a->patient instanceof Patient) {
                                    echo $a->patient->getMaskName();
                                }
                            ?>
                        </td>
                        <td><?= $a->doctor->name ?></td>
                        <td><?= substr($a->createtime, 0, 10); ?></td>
                        <td><?= $a->thedate ?></td>
                        <td>
                            <a target="_blank" href="/revisittktmgr/list?word=<?= $a->patient->name ?>"><?= $a->revisittktid ?></a>
                        </td>
                        <td>
                            <a target="_blank" href="/patientmedicinepkgmgr/list?patient_name=<?= $a->patient->name ?>"><?= $a->patientmedicinepkgid ?></a>
                        </td>
                        <td><?= $a->content ?></td>
                        <td>
                            <a target="_blank" href="/revisitrecordmgr/modify?revisitrecordid=<?= $a->id ?>">修改</a>
                            <a class="delete" data-revisitrecordid="<?= $a->id ?>">删除</a>
                        </td>
                    </tr>
                        <?php
                    }
                    ?>
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
    	$(document).on("click",".delete",function(){
    		var revisitrecordid = $(this).data('revisitrecordid');

    		var tr = $(this).parents("tr");
    		if(false == confirm("确定删除吗？")){
    			return false;
    		}
    		$.ajax({
    			"type" : "get",
    			"data" : {
    				revisitrecordid : revisitrecordid
    			},
    			"dataType" : "html",
    			"url" : "/revisitrecordmgr/deleteJson",
    			"success" : function(data) {
    				if (data == 'success') {
    					alert("删除成功");
    					tr.remove();
    				}else {
    					alert("未知错误");
    				}
    			}
    		});
    	});
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>