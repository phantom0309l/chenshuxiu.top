<?php
$checkup = $a->obj;
?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
		<table class="table table-bordered">
		<thead>
			<tr>
				<th>标题</th>
				<th>检查日期</th>
				<th>医院</th>
				<th>检查答卷</th>
			</tr>
		</thead>
		<tr>
			<td>
				<?php if($checkup->checkuptplid > 0){?>
					<?= $checkup->checkuptpl->title?>
				<?php }else{ ?>
					<?= $checkup->title?>
				<?php }?>
			</td>
			<td><?= $checkup->check_date; ?></td>
			<td><?= $checkup->hospitalstr; ?></td>
			<td>
			<?php
			if ($checkup->xanswersheetid > 0) {
				?>
				<a target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?= $checkup->xanswersheetid ?>">查看答卷</a>
			<?php
			}
			?>
			</td>
		</tr>
</table>
	</div>
</div>
