<?php
$pagetitle = "[{$optasktplcron->optasktpl->title}]任务模版 修改定时事件";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/optasktplcronmgr/modifypost" method="post">
            <input type="hidden" id="optasktplcronid" name="optasktplcronid" value="<?=$optasktplcron->id?>">
            <input type="hidden" id="optasktplid" name="optasktplid" value="<?=$optasktplcron->optasktplid?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td width="200px">任务模版</td>
                        <td>
                            <div class="col-md-2">
                                <?=$optasktplcron->optasktpl->title?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>步骤step</td>
                        <td>
                            <div class="col-md-1">
                                <?php echo HtmlCtr::getSelectCtrImp(OpTaskTplCron::getSteps(), 'step', $optasktplcron->step, 'form-control'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>话术</td>
                        <td>
                            <div class="col-md-4">
                                <textarea class="form-control" id="send_content" name="send_content" rows="4" placeholder="话术"><?=$optasktplcron->send_content?></textarea>
                            </div>
                            <div class="col-md-4">
                                pp (小写) : 患者姓名<br>
                                dd (小写) : 医生姓名<br>
                                DD (大写) : 疾病名
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>结束处理方式</td>
                        <td>
                            <div class="col-md-2">
                                <?php echo HtmlCtr::getSelectCtrImp(OpTaskTplCron::getDealwith_types(), 'dealwith_type', $optasktplcron->dealwith_type, 'form-control dealwith_type'); ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                        if ($optasktplcron->dealwith_type != 'appoint_follow') {
                            $stylestr = "display: none";
                        }
                    ?>
                    <tr id="is_appoint_follow" style="<?=$stylestr?>">
                        <td>约定跟进天数</td>
                        <td>
                            <div class="col-md-1">
                                <?php echo HtmlCtr::getSelectCtrImp(OpTaskTplCron::getFollow_daycnts(), 'follow_daycnt', $optasktplcron->follow_daycnt, 'form-control'); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>备注</td>
                        <td>
                            <div class="col-md-4">
                                <textarea class="form-control" id="remark" name="remark" rows="4" placeholder="备注"><?=$optasktplcron->remark?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交"/>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $(".dealwith_type").on('change', function(){
            var dealwith_type = $("select[name='dealwith_type']").val();

            if (dealwith_type == 'appoint_follow') {
                $("#is_appoint_follow").show();
            } else {
                $("#is_appoint_follow").hide();
            }
        });
    });
</script>

<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
