<?php
$pagetitle = "住院预约草稿列表 Bedtkts";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
    $img_uri . '/v5/common/search_patient.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar" style="border:none">
                <form class="form-horizontal pr" action="/bedtktmgr/list" method="get">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <label class="col-md-1 col-sm-1 control-label label-width" for="">医生</label>
                        <div class="col-md-2 col-sm-1">
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </div>

                        <label class="col-md-1 col-sm-1 control-label label-width" for="patient_name">患者姓名</label>
                        <div class="col-md-2 col-sm-1">
                            <?php include_once $tpl . '/_search_patient.tpl.php'; ?>
                        </div>

                        <div class="col-md-1 col-sm-1">
                            <button class="btn btn-sm btn-success" type="submit">组合筛选</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>createtime</th>
                        <th>患者</th>
                        <!-- <th>图片上传情况</th> -->
                        <th>所属医生</th>
                        <th>希望入住时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bedtkts as $a) { ?>
                        <tr>
                            <td><?=$a->createtime?></td>
                            <td><?=$a->patient->name?></td>
                            <td><?=$a->doctor->name?></td>
                            <td><?=$a->want_date?></td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-xs btn-default" href="/bedtktmgr/modify?bedtktid=<?=$a->id?>">修改</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>

    <div class="clear"></div>

<?php
$footerScript = <<<XXX
	$(function(){
		// $(document).on("click",".delete",function(){
		// 	var checkupid = $(this).data("checkupid");
        //
		// 	var tr = $(this).parents("tr");
        //
		// 	$.ajax({
		// 		"type" : "get",
		// 		"data" : {
		// 			checkupid : checkupid
		// 		},
		// 		"dataType" : "html",
		// 		"url" : "/checkupmgr/deleteJson",
		// 		"success" : function(data) {
		// 			alert("删除成功");
		// 			tr.remove();
		// 		}
		// 	});
		// });
	});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
