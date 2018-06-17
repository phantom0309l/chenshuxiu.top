<style>
.addmedicine {
	margin-bottom: 10px
}
</style>
<div class="mt20 " style="display: inline-block; width: 100%">
    <div class="addmedicine">
        <a class="btn btn-success" href="/patientmedicinepkgitemmgr/add?revisitrecordid=<?=$revisitrecord->id ?>&patientmedicinepkgid=<?=$revisitrecord->patientmedicinepkgid ?>">添加用药</a>
    </div>
    <div class="table-responsive">
        <table class="table tdcenter">
        <thead>
            <tr>
                <th>药名</th>
                <th>剂量</th>
                <th>频率</th>
                <th>调药方案</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
			<?php
foreach ($patientmedicinepkgitems as $a) {
    ?>
                	<tr>
                <td><?=$a->medicine->name ?></td>
                <td><?=$a->drug_dose ?></td>
                <td><?=$a->getDrug_frequencyStr(); ?></td>
                <td><?=$a->drug_change ?></td>
                <td>
                    <a href="/patientmedicinepkgitemmgr/modify?patientmedicinepkgitemid=<?=$a->id ?>">修改</a>
                    <a class="delete" data-patientmedicinepkgitemid="<?=$a->id ?>">删除</a>
                </td>
            </tr>
                <?php
}
?>
		</tbody>
    </table>
    </div>
</div>
<script>
	$(function(){
		$(document).on("click",".delete",function(){
			var patientmedicinepkgitemid = $(this).data("patientmedicinepkgitemid");

			var tr = $(this).parents("tr");
			$.ajax({
				"type" : "get",
				"data" : {
					patientmedicinepkgitemid : patientmedicinepkgitemid
				},
				"dataType" : "text" ,
				"url" : "/patientmedicinepkgitemmgr/deleteJson",
				"success" : function(data){
					if(data == "success"){
						tr.remove();
						alert("删除成功");
					}else if(data == "fail"){
						alert("删除失败");
					}
				}
			});
		});
	});
</script>
