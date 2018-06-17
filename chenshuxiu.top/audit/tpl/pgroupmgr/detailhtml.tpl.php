<input type="hidden" id="pgroupid" value="<?= $pgroup->id ?>"/>
<div class="pageTitle">
    <div class="pageTitleIcon"></div>
    <div class="pageTitleStr"><?= $pgroup->name ?>设置区</div>
    <div class="clear"></div>
</div>
<div class="table-responsive">
    <table class="table table-bordered text-center">
    <tr>
        <td>作为首次推荐组</td>
        <td>
            <span data-value="1" class="level btn btn-default <?= $pgroup->level == 1 ? 'btn-primary' : ''?>">开启</span>
            <span data-value="0" class="level btn ml20 btn-default <?= $pgroup->level == 0 ? 'btn-primary' : ''?>">关闭</span>
        </td>
    </tr>
    <tr>
        <td class="col-md-6">患者可见</td>
        <td>
            <span data-value="1" class="showInWxBtn btn btn-default <?= $pgroup->showinwx == 1 ? 'btn-primary' : ''?>">开启</span>
            <span data-value="0" class="showInWxBtn btn ml20 btn-default <?= $pgroup->showinwx == 0 ? 'btn-primary' : ''?>">关闭</span>
        </td>
    </tr>
    <tr>
        <td class="col-md-6">运营可见</td>
        <td>
            <span data-value="1" class="showInAuditBtn btn btn-default <?= $pgroup->showinaudit == 1 ? 'btn-primary' : ''?>">开启</span>
            <span data-value="0" class="showInAuditBtn btn ml20 btn-default <?= $pgroup->showinaudit == 0 ? 'btn-primary' : ''?>">关闭</span>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="text-right">
            <a class="btn btn-success" href="/pgroupmgr/modify?pgroupid=<?= $pgroup->id ?>">去修改任务配置</a>
        </td>
    </tr>
</table>
</div>
