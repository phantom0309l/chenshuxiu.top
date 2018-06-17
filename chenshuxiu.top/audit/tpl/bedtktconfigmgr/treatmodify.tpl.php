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
        <form class="form form-horizontal" action="/bedtktconfigmgr/modifypost" method="post">
            <input type='hidden' name='bedtktconfigid' value="<?= $bedtktconfig->id ?>">
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">总开关</h3>
                </div>
                <div class="block-content">
                    <?php
                    $typearr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'is_allow_bedtkt', $bedtktconfig->is_allow_bedtkt, 'total_switch css-radio-warning'); ?>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">自动审核通过</h3>
                </div>
                <div class="block-content">
                    <?php
                    $typearr = ['0' => '关闭','1' => '启用'];
                    echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_auto_audit_open]', $content['is_auto_audit_open'], 'css-radio-warning'); ?>
                </div>
            </div>
            <div class="block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">住院预约须知</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table js-table-checktable table1">
                    <tr>
                    <td class="td1" width=143> 开关 </td>
                    <td>
                        <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_notice_open]',$content['is_notice_open'], 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                        <textarea name="content[notice_content]" class="children_select form-control" rows="8" cols="80"><?=$content['notice_content']?></textarea>
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
                        <!--<table class="table js-table-checktable table1 patient-upload">-->
                    <ul class="nav-main-bedtkt" id="sortable"> 
                    <?php
                    $temparr = ['0' => '选填','1' => '必填'];
                    foreach ($content as $itemname => $itemvalue) { 
                        $headstr = strstr($itemname, '_show', true);
                        if (!$headstr) {
                            continue;
                        }
                        $muststr = $headstr . '_must';
                    ?>
                    <li class="dash-border">
                        <label class="css-input css-checkbox css-checkbox-primary remove-margin-t remove-margin-b">
                        <input type="checkbox" class="patient-upload-checkbox" <?php if($itemvalue == 1){?>checked=""<?php }?>><span></span>&nbsp;&nbsp;&nbsp;<?=$nameconfig[$itemname]?>
                        <input type="hidden" name="content[<?=$itemname?>]" value="<?=$itemvalue?>">
                        </label>
                        <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($temparr,'content['.$muststr.']', $content[$muststr], 'children_select css-radio-warning'); ?>
                    </li>
                    <?php } ?>
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
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_yuyuenotice_open]', $content['is_yuyuenotice_open'], 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                        <textarea name="content[yuyuenotice_content]" class="children_select form-control" rows="8" cols="80"> <?=$content['yuyuenotice_content']?> </textarea>
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
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_auditpass_notice_open]', $content['is_auditpass_notice_open'], 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                        <textarea name="content[auditpass_notice_content]" class="children_select form-control" rows="8" cols="80"><?=$content['auditpass_notice_content']?></textarea>
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
                        echo HtmlCtr::getRadioCtrImp4OneUi($typearr,'content[is_auditrefuse_notice_open]',$content['is_auditrefuse_notice_open'], 'children_select css-radio-warning');?>
                    </td>
                    <tr>
                    <td class="td1">内容</td> 
                    <td>
                        <div class="col-md-8 remove-padding">
                        <textarea name="content[auditrefuse_notice_content]" class="children_select form-control" rows="8" cols="80"><?=$content['auditrefuse_notice_content']?></textarea>
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
        initTotalSwitch({$bedtktconfig->is_allow_bedtkt});
        initNotice({$content['is_notice_open']});
        initYuYueNotice({$content['is_yuyuenotice_open']});
        initAuditPassNotice({$content['is_auditpass_notice_open']});
        initAuditRefuseNotice({$content['is_auditrefuse_notice_open']});
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
            status = typeof status == 'undefined' ? 1 : status;
            if (status == 1) {
                $('input[name="content[is_notice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_notice_open]"]').parents('tr').siblings('tr').hide();
            }
    }
    function initYuYueNotice(status) {
            status = typeof status == 'undefined' ? 1 : status;
            if (status == 1) {
                $('input[name="content[is_yuyuenotice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_yuyuenotice_open]"]').parents('tr').siblings('tr').hide();
            }
    }
    function initAuditPassNotice(status) {
            status = typeof status == 'undefined' ? 1 : status;
            if (status == 1) {
                $('input[name="content[is_auditpass_notice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_auditpass_notice_open]"]').parents('tr').siblings('tr').hide();
            }
    }
    function initAuditRefuseNotice(status) {
            status = typeof status == 'undefined' ? 1 : status;
            if (status == 1) {
                $('input[name="content[is_auditrefuse_notice_open]"]').parents('tr').siblings('tr').show();
            } else {
                $('input[name="content[is_auditrefuse_notice_open]"]').parents('tr').siblings('tr').hide();
            }
    }

    $(function(){
        init();
        $('#sortable').sortable();
        //$( "#sortable" ).disableSelection();

        $(document).on('click', '.patient-upload-checkbox', function() {
            var input = $(this).siblings('input');
            if ($(this).is(':checked')) {
                input.val(1);
            } else {
                input.val(0);
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
