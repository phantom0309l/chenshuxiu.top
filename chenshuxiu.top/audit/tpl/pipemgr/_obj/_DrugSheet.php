<?php
$drugsheet = $a->obj;
if($drugsheet->remark){ ?>
	<div class="drugsheetRemark">其他：<?= $drugsheet->remark ?></div>
<?php } ?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
		<table class="table table-bordered">
		<thead>
			<tr>
				<th>类型</th>
	            <th>行为日期</th>
	            <th>药名</th>
	            <th>剂量</th>
	            <th>漏服天数</th>
	            <th>备注</th>
			</tr>
		</thead>
	<?php
	$drugitems = $drugsheet->getDrugItems();
	foreach ($drugitems as $drugitem) {
	?>
		<tr>
			<td><?= $drugitem->getTypeDesc() ?></td>
            <td><?= $drugitem->record_date ?></td>
            <td><?= $drugitem->medicine->name ?></td>
            <td><?= $drugitem->value ?></td>
            <td><?= $drugitem->missdaycnt ?></td>
            <td><?= $drugitem->content ?></td>
		</tr>
	<?php } ?>
</table>
	</div>
</div>
