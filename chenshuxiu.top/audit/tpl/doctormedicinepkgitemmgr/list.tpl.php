<?php
$pagetitle = "方案明细 DoctorMedicinePkgItems ";
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
            <div class="searchBar">
                <a class="btn btn-success" href="/doctormedicinepkgitemmgr/add?doctormedicinepkgid=<?=$doctormedicinepkg->id ?>">套餐项新建</a>
            </div>

            <h3>用药方案 : <?=$doctormedicinepkg->name ?> of <?=$doctormedicinepkg->doctor->name ?></h3>

            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>药名</td>
                        <td>给药途径</td>
                        <td>用药时机</td>
                        <td>标准用法</td>
                        <td>药物剂量</td>
                        <td>用药频率</td>
                        <td>调药规则</td>
                        <th>药材成分</th>
                        <td>用药注意事项</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($doctormedicinepkgitems as $a) {
                        ?>
                            <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->medicine->name ?></td>
                        <td><?= $a->getDrug_way_arr() ?></td>
                        <td><?= $a->getDrug_timespan_arr() ?></td>
                        <td><?= $a->drug_std_dosage ?></td>
                        <td><?= $a->drug_dose ?></td>
                        <td><?= $a->drug_frequency ?></td>
                        <td><?= $a->drug_change ?></td>
                        <td><?= implode('<br/>',explode('|',$a->herbjson)) ?> </td>
                        <td><?= $a->getDoctorMedicineRef()->doctorremark ?></td>
                        <td>
                            <a href="/doctormedicinepkgitemmgr/modify?doctormedicinepkgid=<?=$a->doctormedicinepkg->id ?>&doctormedicinepkgitemid=<?= $a->id ?>">修改</a>
                            <a class="delete" data-id="<?=$a->id ?>">删除</a>
                        </td>
                    </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
	$(function(){
		$(".delete").on("click", function(){
			var id = $(this).data("id");

			var tr = $(this).parents("tr");
			$.ajax({
				"type" : "get",
				"data" : {
					doctormedicinepkgitemid : id
				},
				"dataType" : "html",
				"url" : "/doctormedicinepkgitemmgr/deleteJson",
				"success" : function(data) {
					if (data == 'success') {
						alert("删除成功");
						tr.remove();
					}else if(data == 'fail'){
						alert("删除失败");
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