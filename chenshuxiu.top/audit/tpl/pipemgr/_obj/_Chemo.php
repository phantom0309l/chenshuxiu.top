<?php
$chemo = $a->obj;
?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
		<table class="table  table-bordered">
	<tr>
		<th>类型</th>
		<td><?= $chemo->type ?></td>
	</tr>
	<tr>
		<th>疗程</th>
		<td><?= $chemo->stage?></td>
	</tr>
	<tr>
		<th width=150>化疗开始日期</th>
		<td><?= substr($chemo->startdate, 0, 10);?></td>
	</tr>
	<tr>
		<th>化疗方案名称</th>
		<td><?= $chemo->pkg_name ?>
		</td>
	</tr>
	<tr>
		<th>疗效评价</th>
		<td><?= $chemo->effect_content ?></td>
	</tr>
	<tr>
		<th>化疗医院</th>
		<td><?= $chemo->hospital ?></td>
	</tr>
</table>
	</div>
</div>
