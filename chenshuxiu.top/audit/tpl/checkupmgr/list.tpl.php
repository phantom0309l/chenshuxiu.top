<?php
$pagetitle = "检查报告列表 Checkups";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
    $img_uri . '/v5/common/search_patient.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE
    .label-width{
        width: 100px;
    }
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar" style="border:none">
                <form class="form-horizontal pr" action="/checkupmgr/list" method="get">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <label class="col-md-1 col-sm-1 control-label label-width" for="">医生</label>
                        <div class="col-md-2 col-sm-1">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>

                        <label class="col-md-1 col-sm-1 control-label label-width" for="patient_name">患者姓名</label>
                        <div class="col-md-2 col-sm-1">
                            <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                        </div>

                        <label class="col-md-1 col-sm-1 control-label label-width" for="patient_name">检查时间</label>
                        <div class="col-md-2 col-sm-1">
                            <input type="text" class="calendar form-control" name="fromdate" value="<?= $fromdate ?>" />
                        </div>
                        <div class="col-md-2 col-sm-1">
                            <input type="text" class="calendar form-control" name="todate" value="<?= $todate ?>" />
                        </div>

                        <div class="col-md-1 col-sm-1">
                            <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>检查id</th>
                        <th>所属医生</th>
                        <th>患者</th>
                        <th>标题</th>
                        <th>检查日期</th>
                        <th>创建日期</th>
                        <th>检查答卷</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($checkups as $a) {
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td>
                            <a href="/checkupmgr/list?doctorid=<?= $doctorid ?>"><?= $a->doctor->name ?></a>
                        </td>
                        <td>
                            <?php
                                if ($a->patient instanceof Patient) {
                                    echo $a->patient->getMaskName();
                                }
                            ?>
                        </td>
                        <td>
                            <?php if($a->checkuptplid > 0){?>
                                <?= $a->checkuptpl->title?>
                            <?php }?>
                        </td>
                        <td><?= $a->check_date; ?></td>
                        <td><?= $a->getCreateDayHi(); ?></td>
                        <td>
                    	<?php
                        if ($a->xanswersheetid > 0) {
                            ?>
                            <a target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?= $a->xanswersheetid ?>">查看答卷</a>
                        <?php
                        }
                        ?>
                        </td>
                        <td>
                            <?php if($a->checkuptplid > 0){?>
                                <a target="_blank" href="/checkuppicturemgr/list4bind?checkupid=<?= $a->id ?>">关联图片</a>
                            <?php }?>
                            <a class="delete" data-checkupid="<?=$a->id ?>">删除</a>
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
$footerScript = <<<XXX
	$(function(){
        $("#cleardate").on("click",function(){
            $(".calendar").val('');
            return false;
        });

		$(document).on("click",".delete",function(){
			var checkupid = $(this).data("checkupid");

			var tr = $(this).parents("tr");

			$.ajax({
				"type" : "get",
				"data" : {
					checkupid : checkupid
				},
				"dataType" : "html",
				"url" : "/checkupmgr/deleteJson",
				"success" : function(data) {
						alert("删除成功");
						tr.remove();
				}
			});
		});
	});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
