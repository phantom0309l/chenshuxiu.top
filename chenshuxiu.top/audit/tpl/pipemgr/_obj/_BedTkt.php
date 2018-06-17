<?php $bedtkt = $a->obj ?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
		<table class="table  table-bordered">
		<tr>
			<th>患者姓名</th>
			<td><?= $bedtkt->patient->name ?></td>
		</tr>
		<tr>
			<th width=150>患者预约日期</th>
			<td><?= $bedtkt->want_date?></td>
		</tr>
		<tr>
			<th>患者下单日期</th>
			<td><?= substr($bedtkt->submit_time, 0, 10);?></td>
		</tr>
		<tr>
			<th>医保类型</th>
			<td>
				<?php
					if ($bedtkt->fee_type == 'beijing') {
						echo "北京";
					} else if ($bedtkt->fee_type == 'notbeijing') {
						echo "非北京";
					} else {
						echo "未知";
					}
				?>
			</td>
		</tr>
		<tr>
			<th>预约医生</th>
			<td><?= $bedtkt->doctor->name ?></td>
		</tr>
		<tr>
			<th>审核状态</th>
			<td>
				<?php
					$arr = BedTkt::TYPESTR_STATUS;
					echo $arr["{$bedtkt->status}"];
				?>
			</td>
		</tr>
	</table>
	</div>
</div>
