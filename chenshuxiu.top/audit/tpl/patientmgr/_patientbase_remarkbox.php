<!-- 运营备注 begin-->
<div class="remarkBox mt10">
    <textarea class="remarkBox-ta" rows="6"><?= $patient->opsremark ?></textarea>
    <div class="remarkBox-notice red none">已备注</div>
    <div class="clearfix">
        <a class="btn btn-danger remarkBox-btn pull-left">运营备注</a>
        <div class="pull-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#optaskBox">添加跟进任务</button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#thankBox">添加感谢留言</button>
        </div>
    </div>
</div>
<?php include_once($tpl . "/_thankbox.php"); ?>
<!-- 运营备注 end-->
