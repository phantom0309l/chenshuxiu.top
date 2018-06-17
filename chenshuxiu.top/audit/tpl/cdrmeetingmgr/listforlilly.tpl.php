<?php
$pagetitle = "库存列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
$pageScript = <<<SCRIPT
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/cdrmeetingmgr/listforlilly" class="form-horizontal shopOrderForm">
                <div class="form-group">
                    <label class="control-label col-md-3" style="width:120px">按时间筛选：</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="left_date" value="<?= $left_date ?>" placeholder="起始时间" />
                    </div>
                    <label class="control-label col-md-1">到</label>
                    <div class="col-md-2">
                        <input type="text" class="calendar form-control" name="right_date" value="<?= $right_date ?>" placeholder="截止时间" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">分类:</label>
                    <div class="col-sm-10">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getCdrMeetingStatusCtrArray(),'type', $type, 'css-radio-success')?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
            <div>
                <?php if($patientid > 0){ ?>
                    <a href="/cdrmeetingmgr/listforlilly">全部列表</a>
                <?php } ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <thead>
                <tr>
                    <td>序号</td>
                    <td>生成日期</td>
                    <td>呼叫类型</td>
                    <td>坐席</td>
                    <td>通话时长</td>
                    <td>播放</td>
                    <td>患者</td>
                    <td>AE/PC数</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($cdrMeetings as $i => $a) {
                    ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $a->createtime ?></td>
                    <td><?= $a->isPatientCallIn() ? '患者呼入' : '运营呼出' ?></td>
                    <td><?= $a->auditor->name ?></td>
                    <td><?= $a->formatDuration() ?></td>
                    <td>
                        <?php if($a->isCallOk()){ ?>
                            <?php if( $a->needDownloadVoiceFile() ){ ?>
                                <div class="btn btn-success download-cdr" data-cdrmeetingid="<?=$a->id?>">下载录音</div>
                            <?php }else{ ?>
                                <div>
                                    <audio src="<?= $a->getVoiceUrl() ?>" controls="controls" preload="none"></audio>
                                </div>
                            <?php } ?>
                        <?php }else{ ?>
                            <p class="text-danger"><?= $a->getCallResultDesc() ?></p>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="/cdrmeetingmgr/listforlilly?patientid=<?= $a->patientid ?>">
                            <?= $a->patient->getMaskName() ?>
                        </a>
                    </td>
                    <td>
                        <a target="_blank" href="/aepcmgr/list?patientid=<?= $a->patientid ?>">
                            <?= PaperDao::getAEPCCntByPatient($a->patient) ?>
                        </a>
                    </td>
                    <td align="center">
                        <a target="_blank" href="/patientmgr/list?keyword=<?=$a->patientid ?>">详情</a>
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
<?php
$footerScript = <<<STYLE
    $(function(){
        $(document).on("click", ".download-cdr", function() {
            alert('正在下载，请2分钟后刷新页面');
			var me = $(this);
			var cdrmeetingid = me.data("cdrmeetingid");
			$.ajax({
				"type" : "post",
				"data" : {
					"cdrmeetingid" : cdrmeetingid
				},
				"url" : "/pipemgr/downloadvoiceJson",
				"success" : function(data) {
					$(".download-cdr").hide();
				}
			});
		});
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
