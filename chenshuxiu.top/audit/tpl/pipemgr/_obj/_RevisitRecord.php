<?php $revisitrecord = $a->obj; ?>
<div class="TriggerBox">
    <div class="grayBgColorBox">
        <button class="TriggerBtn btn btn-default btn-sm">展开</button>
    </div>
    <div class="TriggerContent colorBox none">
		<table class="table table-bordered">
		<thead>
			<tr>
				<td>类型</td>
				<td>内容</td>
				<td>加号单</td>
				<!-- <td>用药处方</td> -->
			</tr>
		</thead>
		<tr>
			<td>
				<?= $revisitrecord->typestr ?>
			</td>
			<td>
				<?= $revisitrecord->content ?>
			</td>
			<td>
				<?php if($revisitrecord->revisittktid){ ?>
					<a target="_blank" href="/revisittktmgr/modify?revisitrecordid=<?= $revisitrecord->revisittktid ?>">加号单</a>
				<?php }else{ ?>
					无
				<?php } ?>
			</td>
			<!-- <td>
				<?php if($revisitrecord->patientmedicinepkgid){ ?>
					<a target="_blank" href="/patientmedicinepkgmgr/one?revisitrecordid=<?= $revisitrecord->patientmedicinepkgid ?>">用药处方</a>
				<?php }else{ ?>
					无
				<?php } ?>
			</td> -->
		</tr>
</table>
	</div>
</div>
