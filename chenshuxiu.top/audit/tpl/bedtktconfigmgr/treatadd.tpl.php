<?php
$pagetitle = "住院预约配置";
$sideBarMini = true;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.table1 td {
    border:none !important;
}
.nav-main-bedtkt {
    padding: 0;
}
.nav-main-bedtkt li {
    line-height: 2.4 !important;
    vertical-align: middle;
    padding: 10px 0;
    list-style: none;
}
.nav-main-bedtkt li label:first-child {
    width: 200px;
}
.nav-main-bedtkt li.dash-border {
    border-bottom: 1px dashed #e9e9e9;
}
.nav-main-bedtkt li.dash-border:last-child {
    border: none;
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
                <li <?php if($typestr == "treat") {?>class="active"<?php }?>>
                    <a href="/bedtktconfigmgr/one?doctorid=<?=$doctor->id?>&typestr=treat">治疗</a>
                </li>
                <li <?php if($typestr == "checkup") {?>class="active"<?php }?>>
                    <a href="/bedtktconfigmgr/one?doctorid=<?=$doctor->id?>&typestr=checkup">检查</a>
                </li>
            </ul>
            <div class="block-content tab-content">
        <form class="form form-horizontal" action="/bedtktconfigmgr/addpost" method="post">
            <input type='hidden' name='typestr' value="<?= $typestr ?>">
            <input type='hidden' name='doctorid' value="<?= $doctor->id ?>">
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">总开关</h3>
                </div>
                <div class="block-content">
                    <?php
                    $typearr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'is_allow_bedtkt', '0', 'total_switch css-radio-warning'); ?>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">自动审核通过</h3>
                </div>
                <div class="block-content">
                    <?php
                    $typearr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_auto_audit_open]', '0', 'css-radio-warning'); ?>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">住院预约须知</h3>
                </div>
                <div class="block-content">
                    <div class="">
                        <table class="table js-table-checktable table1">
                    <tr>
                    <td class="td1" width=143> 开关 </td>
                    <td>
                        <?php
                        $typearr = ['0' => '关闭','1' => '启用'];
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_notice_open]','1', 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                            <textarea name="content[notice_content]" class="children_select form-control" rows="8" cols="80">预约住院需要提交近3日血常规报告检查单照片和住院证正面</textarea>
                        </div>
                    </td>
                    </tr>
                    </table>
                    </div>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">患者预约上传设置</h3>
                </div>
                <div class="block-content">
                    <div class="">
                        <ul id="sortable" class="nav-main-bedtkt table js-table-checktable table1">
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-zhuyuan_photo_show" checked=""><span></span>&nbsp;&nbsp;&nbsp;住院证照片
                                        <input type="hidden" name="content[is_zhuyuan_photo_show]" value="1">
                                    </label>
                                    <?php
                                    $temparr = ['0' => '选填','1' => '必填'];
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_zhuyuan_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-feetype_show" checked=""><span></span>&nbsp;&nbsp;&nbsp;医保类型
                                        <input type="hidden" name="content[is_feetype_show]" value="1">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_feetype_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-xuechanggui_photo_show" checked=""><span></span>&nbsp;&nbsp;&nbsp;血常规照片
                                        <input type="hidden" name="content[is_xuechanggui_photo_show]" value="1">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_xuechanggui_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-gangongneng_photo_show" checked=""><span></span>&nbsp;&nbsp;&nbsp;肝功能照片
                                        <input type="hidden" name="content[is_gangongneng_photo_show]" value="1">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_gangongneng_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-plandate_show" checked=""><span></span>&nbsp;&nbsp;&nbsp;入住日期
                                    <input type="hidden" name="content[is_plandate_show]" value="1">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_plandate_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-idcard_show"><span></span>&nbsp;&nbsp;&nbsp;身份证号
                                    <input type="hidden" name="content[is_idcard_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_idcard_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-zhuyuanhao_show"><span></span>&nbsp;&nbsp;&nbsp;住院号
                                    <input type="hidden" name="content[is_zhuyuanhao_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_zhuyuanhao_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-bingshi_show" ><span></span>&nbsp;&nbsp;&nbsp;病史
                                    <input type="hidden" name="content[is_bingshi_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_bingshi_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-linchuangbiaoxian_show" ><span></span>&nbsp;&nbsp;&nbsp;临床表现
                                    <input type="hidden" name="content[is_linchuangbiaoxian_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_linchuangbiaoxian_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-otherdisease_show" ><span></span>&nbsp;&nbsp;&nbsp;其他疾病
                                    <input type="hidden" name="content[is_otherdisease_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_otherdisease_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-shoushuriqi_show" ><span></span>&nbsp;&nbsp;&nbsp;手术日期
                                    <input type="hidden" name="content[is_shoushuriqi_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_shoushuriqi_must]', 0, 'children_select css-radio-warning'); ?>
                            </li>
                            <li class="dash-border">
                                <td class="td1">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-xingongnengfenji_show" ><span></span>&nbsp;&nbsp;&nbsp;心功能分级
                                    <input type="hidden" name="content[is_xingongnengfenji_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_xingongnengfenji_must]', 0, 'children_select css-radio-warning'); ?>
                                </td>
                            </li>
                            <li class="dash-border">
                                <td class="td1">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-xindiantu_show" ><span></span>&nbsp;&nbsp;&nbsp;心电图
                                    <input type="hidden" name="content[is_xindiantu_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_xindiantu_must]', 0, 'children_select css-radio-warning'); ?>
                                </td>
                            </li>
                            <li class="dash-border">
                                <td class="td1">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-xueshuantanlitu_show" ><span></span>&nbsp;&nbsp;&nbsp;血栓弹力图
                                    <input type="hidden" name="content[is_xueshuantanlitu_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_xueshuantanlitu_must]', 0, 'children_select css-radio-warning'); ?>
                                </td>
                            </li>
                            <li class="dash-border">
                                <td class="td1">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-fengshimianyijiancha_show" ><span></span>&nbsp;&nbsp;&nbsp;风湿免疫检查
                                    <input type="hidden" name="content[is_fengshimianyijiancha_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_fengshimianyijiancha_must]', 0, 'children_select css-radio-warning'); ?>
                                </td>
                            </li>
                            <li class="dash-border">
                                <td class="td1">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                    <input type="checkbox" class="isuse-shuqianqitajiancha_show" ><span></span>&nbsp;&nbsp;&nbsp;术前其他检查
                                    <input type="hidden" name="content[is_shuqianqitajiancha_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_shuqianqitajiancha_must]', 0, 'children_select css-radio-warning'); ?>
                                </td>
                            </li>
                            <li class="dash-border">
                                <td class="td1">
                                    <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                                        <input type="checkbox" class="isuse-zhuyuangoal_show" ><span></span>&nbsp;&nbsp;&nbsp;本次住院目的
                                        <input type="hidden" name="content[is_zhuyuangoal_show]" value="0">
                                    </label>
                                    <?php
                                    echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content[is_zhuyuangoal_must]', 0, 'children_select css-radio-warning'); ?>
                                </td>
                            </li>
                        </ul>
                    </table>
                    </div>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">预约申请通知</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                    <tr>
                    <td class="td1" width=143> 开关 </td>
                    <td>
                        <?php
                        $typearr = ['0' => '关闭','1' => '启用'];
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_yuyuenotice_open]','1', 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                            <textarea name="content[yuyuenotice_content]" class="children_select form-control" rows="8" cols="80">您的住院申请已提交，正在审核中</textarea>
                        </div>
                    </td>
                    </tr>
                    </table>
                    </div>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">审核通过通知</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                    <tr>
                    <td class="td1" width=143> 开关 </td>
                    <td>
                        <?php
                        $typearr = ['0' => '关闭','1' => '启用'];
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_auditpass_notice_open]','1', 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                            <textarea name="content[auditpass_notice_content]" class="children_select form-control" rows="8" cols="80">您的住院申请已通过审核，请保持电话畅通会有医生与您联系</textarea>
                        </div>
                    </td>
                    </tr>
                    </table>
                    </div>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">审核拒绝通知</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                    <tr>
                    <td class="td1" width=143> 开关 </td>
                    <td>
                        <?php
                        $typearr = ['0' => '关闭','1' => '启用'];
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_auditrefuse_notice_open]','1', 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                            <textarea name="content[auditrefuse_notice_content]" class="children_select form-control" rows="8" cols="80">您的住院申请未通过，如有问题请与我们联系</textarea>
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
                    <!--<a href="javascript:" class="btn btn-warning btn-minw btn-copy push-20-l">保存并复制到其他</a>-->
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
    function init () {
        initTotalSwitch(0);
        initNotice(1);
        initYuYueNotice(1);
        initAuditPassNotice(1);
        initAuditRefuseNotice(1);
    }

    function initTotalSwitch(status) {
        status = typeof status == 'undefined' ? 1 : status;

        if (status == 1) {
            var i = 0;
            $('form .block').show();
        } else if (status == 0)  {
            var i = 0;
            $('form .block').each(function(){
                if (i > 0) {
                    $(this).hide();
                }
                i++;
            });
        }
    }

    function initNotice(status) {
            status = status || 1;
            if (status == 1) {
                $('input[name="content[is_notice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_notice_open]"]').parents('tr').siblings('tr').hide();
            }
    }
    function initYuYueNotice(status) {
            status = status || 1;
            if (status == 1) {
                $('input[name="content[is_yuyuenotice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_yuyuenotice_open]"]').parents('tr').siblings('tr').hide();
            }
    }
    function initAuditPassNotice(status) {
            status = status || 1;
            if (status == 1) {
                $('input[name="content[is_auditpass_notice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_auditpass_notice_open]"]').parents('tr').siblings('tr').hide();
            }
    }
    function initAuditRefuseNotice(status) {
            status = status || 1;
            if (status == 1) {
                $('input[name="content[is_auditrefuse_notice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_auditrefuse_notice_open]"]').parents('tr').siblings('tr').hide();
            }
    }

    $(function(){
        init();
        $('#sortable').sortable();
        //$('#sortable').disableSelection();

        $(document).on('click', '.isuse-zhuyuan_photo_show', function(e) {
            console.log($(this).is(':checked'));
            if ($(this).is(':checked')) {
                $("input[name='content[is_zhuyuan_photo_show]']").val(1);
            } else {
                $("input[name='content[is_zhuyuan_photo_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-feetype_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_feetype_show]']").val(1);
            } else {
                $("input[name='content[is_feetype_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-xuechanggui_photo_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_xuechanggui_photo_show]']").val(1);
            } else {
                $("input[name='content[is_xuechanggui_photo_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-gangongneng_photo_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_gangongneng_photo_show]']").val(1);
            } else {
                $("input[name='content[is_gangongneng_photo_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-plandate_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_plandate_show]']").val(1);
            } else {
                $("input[name='content[is_plandate_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-idcard_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_idcard_show]']").val(1);
            } else {
                $("input[name='content[is_idcard_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-zhuyuanhao_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_zhuyuanhao_show]']").val(1);
            } else {
                $("input[name='content[is_zhuyuanhao_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-bingshi_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_bingshi_show]']").val(1);
            } else {
                $("input[name='content[is_bingshi_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-linchuangbiaoxian_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_linchuangbiaoxian_show]']").val(1);
            } else {
                $("input[name='content[is_linchuangbiaoxian_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-otherdisease_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_otherdisease_show]']").val(1);
            } else {
                $("input[name='content[is_otherdisease_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-shoushuriqi_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_shoushuriqi_show]']").val(1);
            } else {
                $("input[name='content[is_shoushuriqi_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-xingongnengfenji_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_xingongnengfenji_show]']").val(1);
            } else {
                $("input[name='content[is_xingongnengfenji_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-xindiantu_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_xindiantu_show]']").val(1);
            } else {
                $("input[name='content[is_xindiantu_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-xueshuantanlitu_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_xueshuantanlitu_show]']").val(1);
            } else {
                $("input[name='content[is_xueshuantanlitu_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-fengshimianyijiancha_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_fengshimianyijiancha_show]']").val(1);
            } else {
                $("input[name='content[is_fengshimianyijiancha_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-shuqianqitajiancha_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_shuqianqitajiancha_show]']").val(1);
            } else {
                $("input[name='content[is_shuqianqitajiancha_show]']").val(0);
            }
        });
        $(document).on('click', '.isuse-zhuyuangoal_show', function(e) {
            if ($(this).is(':checked')) {
                $("input[name='content[is_zhuyuangoal_show]']").val(1);
            } else {
                $("input[name='content[is_zhuyuangoal_show]']").val(0);
            }
        });

        $(document).on('change', '.total_switch input', function(event) {
            var me = $(this);
            var status = me.val();
           initTotalSwitch(status);
        });
        $(document).on('change', 'input[name="content[is_notice_open]"]', function(e) {
            var status = $(this).val();
            initNotice(status);
        });
        $(document).on('change', 'input[name="content[is_yuyuenotice_open]"]', function(e) {
            var status = $(this).val();
            initYuYueNotice(status);
        });
        $(document).on('change', 'input[name="content[is_auditpass_notice_open]"]', function(e) {
            var status = $(this).val();
            initAuditPassNotice(status);
        });
        $(document).on('change', 'input[name="content[is_auditrefuse_notice_open]"]', function(e) {
            var status = $(this).val();
            initAuditRefuseNotice(status);
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
