<?php
$patientremark = $a->obj;
?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
		<table class="table table-bordered">
		<thead>
			<tr>
				<td>记录的日期</td>
				<td>类型</td>
				<td>名称</td>
				<td>具体内容</td>
			</tr>
		</thead>
		<tr>
			<td>
				<?= $patientremark->thedate ?>
			</td>
			<td>
				<?= $patientremark->typestr ?>
			</td>
			<td>
				<?= $patientremark->name ?>
			</td>
			<td>
				<?= $patientremark->content ?>
			</td>
		</tr>
</table>
	</div>
</div>
