<?php
$pagetitle = "复诊预约配置";
$sideBarMini = true;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.table1 td {
    border:none !important;
}
.table1 .td1{
    line-height: 2.4 !important;
}
.table1 tr.dash-border {
    border-bottom: 1px dashed #e9e9e9;
}
STYLE;

?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php";?>
    <div class="content-div">
        <section class="col-md-12">
        <div class="block" style="border:1px solid #e9e9e9;">
            <ul class="nav nav-tabs">
                <?php foreach ($diseases as $a) { ?>
                <li <?php if($a->id == $diseaseid) {?>class="active"<?php }?>>
                    <a href="/revisittktconfigmgr/one?doctorid=<?=$doctor->id?>&diseaseid=<?=$a->id?>"><?=$a->name?></a>
                </li>
                <?php } ?>
            </ul>
            <div class="block-content tab-content">
        <form class="form form-horizontal" action="/revisittktconfigmgr/modifypost" method="post">
            <input type="hidden" name="revisittktconfigid" value="<?= $revisittktconfig->id ?>"  />
            <input type="hidden" name="copy2otherdisease" value=""  />
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">总开关</h3>
                </div>
                <div class="block-content">
                    <?php
                    $temparr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'status',$revisittktconfig->status, 'parent_select css-radio-warning'); ?>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">患者预约上传设置</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">

                    <tr class="dash-border">
                        <td class="td1" width=30>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                <input type="checkbox" class="isuse_out_case_no_checkbox" <?php if($revisittktconfig->isuse_out_case_no == 1) {?>checked=""<?php } ?>><span></span>
                                <input type="hidden" name="isuse_out_case_no" value="<?=$revisittktconfig->isuse_out_case_no?>">
                            </label>
                        </td>
                        <td class="td1" width=100>院内病历号</td>
                        <td>
                            <?php
                            $temparr = ['0' => '选填','1' => '必填'];
                            echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'ismust_out_case_no', $revisittktconfig->ismust_out_case_no, 'children_select css-radio-warning'); ?>
                        </td>
                    </tr>

                    <tr class="dash-border">
                        <td class="td1" width=30>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                <input type="checkbox" class="isuse_patientcardno_checkbox" <?php if($revisittktconfig->isuse_patientcardno == 1) {?>checked=""<?php } ?>><span></span>
                                <input type="hidden" name="isuse_patientcardno" value="<?=$revisittktconfig->isuse_patientcardno?>">
                            </label>
                        </td>
                        <td class="td1" width=100>院内就诊卡号</td>
                        <td>
                            <?php
                            $temparr = ['0' => '选填','1' => '必填'];
                            echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'ismust_patientcardno', $revisittktconfig->ismust_patientcardno, 'children_select css-radio-warning'); ?>
                        </td>
                    </tr>

                    <tr class="dash-border">
                        <td class="td1" width=30>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                <input type="checkbox" class="isuse_patientcard_id_checkbox" <?php if($revisittktconfig->isuse_patientcard_id == 1) {?>checked=""<?php } ?>><span></span>
                                <input type="hidden" name="isuse_patientcard_id" value="<?=$revisittktconfig->isuse_patientcard_id?>">
                            </label>
                        </td>
                        <td class="td1" width=100>院内患者ID</td>
                        <td>
                            <?php
                            $temparr = ['0' => '选填','1' => '必填'];
                            echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'ismust_patientcard_id', $revisittktconfig->ismust_patientcard_id, 'children_select css-radio-warning'); ?>
                        </td>
                    </tr>

                    <tr class="dash-border">
                        <td class="td1" width=30>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                <input type="checkbox" class="isuse_bingan_no_checkbox" <?php if($revisittktconfig->isuse_bingan_no == 1) {?>checked=""<?php } ?>><span></span>
                                <input type="hidden" name="isuse_bingan_no" value="<?=$revisittktconfig->isuse_bingan_no; ?>">
                            </label>
                        </td>
                        <td class="td1" width=100>院内病案号</td>
                        <td>
                            <?php
                            $temparr = ['0' => '选填','1' => '必填'];
                            echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'ismust_bingan_no', $revisittktconfig->ismust_bingan_no, 'children_select css-radio-warning'); ?>
                        </td>
                    </tr>

                    <tr class="dash-border">
                        <td class="td1" width=30>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                <input type="checkbox" class="isuse_treat_stage_checkbox" <?php if($revisittktconfig->isuse_treat_stage == 1) {?>checked=""<?php } ?>><span></span>
                                <input type="hidden" name="isuse_treat_stage" value="<?=$revisittktconfig->isuse_treat_stage; ?>">
                            </label>
                        </td>
                        <td class="td1" width=100>手术</td>
                        <td>
                            <?php
                            $temparr = ['0' => '选填','1' => '必填'];
                            echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'ismust_treat_stage', $revisittktconfig->ismust_treat_stage, 'children_select css-radio-warning'); ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="td1" width=30>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                <input type="checkbox" class="isuse-patient-content" <?php if($revisittktconfig->isuse_patient_content == 1) {?>checked=""<?php } ?>><span></span>
                                <input type="hidden" name="isuse_patient_content" value="<?=$revisittktconfig->isuse_patient_content?>">
                            </label>
                        </td>
                        <td class="td1">就诊目的</td>
                        <td>
                            <?php
                            $temparr = ['0' => '选填','1' => '必填'];
                            echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'ismust_patient_content',$revisittktconfig->ismust_patient_content, 'children_select css-radio-warning'); ?>
                        </td>
                    </tr>
                </table>
                    </div>
                </div>
            </div>

            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">未如约复诊跟进任务设置</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                <tr>
                <td class="td1" width=143>
                    是否创建任务
                </td>
                <td>
                        <?php
                        $temparr = ['0' => '关闭','1' => '启用'];
                        echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'create_optask_not_ontime_status',$revisittktconfig->create_optask_not_ontime_status, 'children_select css-radio-warning'); ?>
                </td>
                </tr>
                </table>
                    </div>
                </div>
            </div>

            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">复诊提醒</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                <tr>
                <td class="td1" width=143> 开关 </td>
                <td>
                    <?php
                    $temparr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'remind_status',$revisittktconfig->remind_status, 'children_select css-radio-warning'); ?>
                </td>
                <tr>
                <td class="td1">提前通知天数</td>
                <td>
                    <div class="col-md-8 remove-padding">
                    <input type="number" class="children_select form-control" name="remind_pre_day_cnt" value="<?= $revisittktconfig->remind_pre_day_cnt ?>"/>
                    </div>
                </td>
                </tr>
                <tr>
                <td class="td1">提醒内容</td>
                <td>
                    <div class="col-md-8 remove-padding">
                        <textarea name="remind_notice" class="children_select form-control" rows="8" cols="80"><?= $revisittktconfig->remind_notice ?></textarea>
                        <div class="alert alert-warning alert-dismissable push-10-t">
                        #patient_name# 患者名
                        <br/>#thedate# 为预约的日期 如： 2017-04-23
                        <br/>#doctor_name# 为医生名
                        <br/>#begin_hour# 复诊时间 如: 上午 9：00
                        <br/>#address# 本次复诊的 位置如： 通港大厦708
                        <br/>#dow# 星期几 如 周三
                        </div>
                    </div>
                    <div class="col-md-6" style="">
                    </div>
                </td>
                </tr>
                <tr>
                <td class="td1">错过是否发送</td>
                <td>
                    <?php
                    $temparr = ['0' => '不发送','1' => '发送'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'remind_issend_miss',$revisittktconfig->remind_issend_miss, 'children_select css-radio-warning'); ?>
                </td>
                </tr>
                </table>
                    </div>
                </div>
            </div>

            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">复诊确认</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                <tr>
                <td class="td1" width=143> 开关 </td>
                <td>
                    <?php
                    $temparr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'confirm_status',$revisittktconfig->confirm_status, 'children_select css-radio-warning'); ?>
                </td>
                <tr>
                <td class="td1">提前通知天数</td>
                <td>
                    <div class="col-md-8 remove-padding">
                        <input type="number" class="children_select form-control" name="confirm_pre_day_cnt" value="<?= $revisittktconfig->confirm_pre_day_cnt ?>"/>
                    </div>
                </td>
                </tr>
                <tr>
                <td class="td1">提醒内容</td>
                <td>
                    <div class="col-md-8 remove-padding">
                        <textarea name="confirm_notice" class="children_select form-control" rows="8" cols="80"><?= $revisittktconfig->confirm_notice ?></textarea>
                        <div class="alert alert-warning alert-dismissable push-10-t">
                            #patient_name# 患者名
                            <br/>#thedate# 为预约的日期 如： 2017-04-23
                            <br/>#doctor_name# 为医生名
                            <br/>#begin_hour# 复诊时间 如: 上午 9：00
                            <br/>#address# 本次复诊的 位置如： 通港大厦708
                            <br/>#dow# 星期几 如 周三
                        </div>
                    </div>
                </td>
                </tr>
                <tr>
                    <td class="td1">确认通知</td>
                    <td>
                        <div class="col-md-8 remove-padding">
                            <textarea name="confirm_content_yes" class="children_select form-control" rows="8" cols="80"><?= $revisittktconfig->confirm_content_yes ?></textarea>
                            <div class="alert alert-warning alert-dismissable push-10-t">
                                #patient_name# 患者名
                                <br/>#thedate# 为预约的日期 如： 2017-04-23
                                <br/>#doctor_name# 为医生名
                                <br/>#begin_hour# 复诊时间 如: 上午 9：00
                                <br/>#address# 本次复诊的 位置如： 通港大厦708
                                <br/>#dow# 星期几 如 周三
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                <td class="td1">错过是否发送</td>
                <td>
                    <?php
                    $temparr = ['0' => '不发送','1' => '发送'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'confirm_issend_miss',$revisittktconfig->confirm_issend_miss, 'children_select css-radio-warning'); ?>
                </td>
                </tr>
                </table>
                    </div>
                </div>
            </div>

            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">标记加号</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                            <tr>
                                <td class="td1" width=143> 开关 </td>
                                <td>
                                    <?php
                                    $temparr = ['0' => '关闭','1' => '启用'];
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'is_mark_his_notice',$revisittktconfig->is_mark_his_notice, 'children_select css-radio-warning'); ?>
                                </td>
                            <tr>
                                <td class="td1">标记加号 推送内容</td>
                                <td>
                                    <div class="col-md-8 remove-padding">
                                        <textarea name="mark_his_notice" class="children_select form-control" rows="8" cols="80"><?= $revisittktconfig->mark_his_notice ?></textarea>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="td1">取消标记加号 推送内容</td>
                                <td>
                                    <div class="col-md-8 remove-padding">
                                        <textarea name="unmark_his_notice" class="children_select form-control" rows="8" cols="80"><?= $revisittktconfig->unmark_his_notice ?></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div >
                <p>
                    <button class="btn btn-primary btn-minw">保存</button>
                    <a href="javascript:" class="btn btn-warning btn-minw btn-copy push-20-l">保存并复制到其他疾病</a>
                </p>
            </div>
        </form>
        </div>
        </div>
        </section>
    </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    function init (status) {
        initTotalSwitch(status);
        initRemindSwitch(status);
        initConfirmSwitch(status);
        initMarkHisNoticeSwitch(status);
    }

    function initTotalSwitch(status) {
        status = status || {$revisittktconfig->status};
        if (status == 1) {
            var i = 0;
            $('form .block').show();
            //$(".children_select").removeClass('css-radio-default').addClass('css-radio-warning').find("input[type='radio']").prop('disabled', false);
            //$("input[type='checkbox']").prop('disabled', false);
        } else if (status == 0)  {
            var i = 0;
            $('form .block').each(function(){
                if (i > 0) {
                    $(this).hide();
                }
                i++;
            });
            //$(".children_select").removeClass('css-radio-warning').addClass('css-radio-default').find("input[type='radio']").prop('disabled', true);
            //$("input[type='checkbox']").prop('disabled', true);
        }
        //var s1 = $('input[name="remind_status"]').val();
        //initRemindSwitch(s1);
        //var s2 = $('input[name="confirm_status"]').val();
        //initConfirmSwitch(s2);
    }

    function initRemindSwitch(status) {
            status = status || {$revisittktconfig->remind_status};
            if (status == 1) {
                $('input[name="remind_status"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="remind_status"]').parents('tr').siblings('tr').hide();
            }
    }

    function initConfirmSwitch(status) {
            status = status || {$revisittktconfig->confirm_status};
            if (status == 1) {
                $('input[name="confirm_status"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="confirm_status"]').parents('tr').siblings('tr').hide();
            }
    }

    function initMarkHisNoticeSwitch(status) {
            status = status || {$revisittktconfig->is_mark_his_notice};
            if (status == 1) {
                $('input[name="is_mark_his_notice"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="is_mark_his_notice"]').parents('tr').siblings('tr').hide();
            }
    }

    $(function(){
        init();

        $(".parent_select").on('change', function(event) {
            var me = $(this).find('input');
            var status = me.val();
            initTotalSwitch(status);
        });

        $(document).on('click', '.isuse_out_case_no_checkbox', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='isuse_out_case_no']").val(1);
            } else {
                $("input[name='isuse_out_case_no']").val(0);
            }
        });

        $(document).on('click', '.isuse_patientcardno_checkbox', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='isuse_patientcardno']").val(1);
            } else {
                $("input[name='isuse_patientcardno']").val(0);
            }
        });

        $(document).on('click', '.isuse_patientcard_id_checkbox', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='isuse_patientcard_id']").val(1);
            } else {
                $("input[name='isuse_patientcard_id']").val(0);
            }
        });

        $(document).on('click', '.isuse_bingan_no_checkbox', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='isuse_bingan_no']").val(1);
            } else {
                $("input[name='isuse_bingan_no']").val(0);
            }
        });

        $(document).on('click', '.isuse_treat_stage_checkbox', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='isuse_treat_stage']").val(1);
            } else {
                $("input[name='isuse_treat_stage']").val(0);
            }
        });

        $(document).on('click', '.isuse-patient-content', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='isuse_patient_content']").val(1);
            } else {
                $("input[name='isuse_patient_content']").val(0);
            }
        });

        $(document).on('change', 'input[name="remind_status"]', function(e) {
            var status = $(this).val();
            initRemindSwitch(status);
        });
        $(document).on('change', 'input[name="confirm_status"]', function(e) {
            var status = $(this).val();
            initConfirmSwitch(status);
        });
        $(document).on('change', 'input[name="is_mark_his_notice"]', function(e) {
            var status = $(this).val();
            initMarkHisNoticeSwitch(status);
        });

        $(document).on('click', '.btn-copy', function() {
            if (!confirm("保存并复制到其他疾病会覆盖掉现有疾病的已有配置，你确定要这么做吗？")) {
                return false;
            }
           $('input[name="copy2otherdisease"]').val(1);
           $('form').submit();
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
