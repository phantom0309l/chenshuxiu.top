<?php
$pagetitle = "汇款单列表";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/doctorwithdrawordermgr/list" class="form-horizontal shopOrderForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">医生：</label>
                <div class="col-sm-2">
                    <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                </div>
                <div class="col-md-2">
                    <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                </div>
            </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>id</td>
                    <td>医生</td>
                    <td>金额</td>
                    <td>通过时间</td>
                    <td>备注</td>
                    <td>运营</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($doctorWithdrawOrders as $i => $a) {
                    ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->doctor->name ?></td>
                    <td><?= $a->getAmount_yuan() ?></td>
                    <td><?= $a->donetime ?></td>
                    <td>
                        <input type="text" value="<?= $a->remark ?>" class="remark"/>
                    </td>
                    <td><?= $a->auditor->name ?></td>
                    <td>
                        <?php if($a->status){ ?>
                            <span class="green">已汇款</span>
                        <?php }else{ ?>
                            <span class="btn btn-default passBtn" data-doctorwithdraworderid="<?= $a->id ?>">汇款</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<?php
$footerScript = <<<STYLE
$(function(){
    $(".passBtn").on("click", function(){
        var me = $(this);
        var remark = $.trim( me.parents("tr").find(".remark").val() );
        var doctorwithdraworderid = me.data("doctorwithdraworderid");
		$.ajax({
			"type" : "post",
			"data" : {
				remark : remark,
                doctorwithdraworderid : doctorwithdraworderid
			},
			"dataType" : "text",
			"url" : "/doctorwithdrawordermgr/passJson",
			"success" : function(data) {
                window.location.href = window.location.href;
			}
		});
    })
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
