<?php
$dbCodeArr = [
    '基本信息' => 'patientbaseinfo',
    '病历信息' => 'patientpcard',
    '病史信息' => 'diseasehistory'
];
$isDbBaseinfo = in_array($fitpagetpl->code, $dbCodeArr);
if ($isDbBaseinfo) {
    $pagetitle = "数据库基本信息配置";
} else {
    $pagetitle = "患者报到页配置";
}
$sideBarMini = true;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
$pageStyle = <<<STYLE
.block-options > li > a {
    opacity: 1;
}
li.active > a {
    color: #20a0ff !important;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
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
                    <a href="/doctorconfigmgr/fitpage?doctorid=<?=$doctor->id?>&diseaseid=<?=$a->id?>&code=<?=$fitpagetpl->code?>"><?=$a->name?></a>
                </li>
                <?php } ?>
            </ul>
            <div class="block-content tab-content">
                <div class="" id="btabs-animated-fade-profile">
                    <?php if ($isDbBaseinfo) {?>
                    <p class="">
                    <?php $i = 0; foreach ($dbCodeArr as $name => $code) { ?>
                    <a href="/doctorconfigmgr/fitpage?doctorid=<?=$doctor->id?>&diseaseid=<?=$diseaseid?>&code=<?=$code?>" class="btn <?php if($fitpagetpl->code == $code){?>btn-primary<?php }else{ ?>btn-default<?php }?> <?php if($i >0){ ?>push-10-l<?php }?>"><?=$name?></a>
                    <?php $i++; }?>
                    </p>
                    <?php }?>

                    <?php if($isdefault) { ?>
                <div class="alert alert-warning">
                <i class="fa fa-fw fa-warning"></i> 此配置是默认配置，如需修改请点击<a class="btn btn-default btn-xs push-20-l" href="/doctorconfigmgr/addfitpage?doctorid=<?=$doctor->id?>&diseaseid=<?=$diseaseid?>&code=<?=$fitpagetpl->code?>"><i class="fa fa-pencil"></i> 自定义</a>
                </div>
                    <?php } else { ?>
                    <form class="form" action="/doctorconfigmgr/configfitpagepost" method="post">
                    <p><input type="submit" class="btn btn-success btn-sm btn-minw" value="保存"></p>
                    <?php } ?>
                    <input type="hidden" id="fitpageid" name="fitpageid" value="<?=$fitpage->id ?>">
                    <input type="hidden" id="fitpagetplid" name="fitpagetplid" value="<?=$fitpagetpl->id ?>">
                    <input type="hidden" id="fitpageitemidstr" name="fitpageitemidstr" value="<?=$fitpageitemidstr ?>">
                    <input type="hidden" id="ismuststr" name="ismuststr" value="<?=$ismuststr ?>">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" >
                        <tbody>
                            <?php foreach ($fitpagetplitems as $a ){ ?>
                                <tr>
                                    <td width="70">
                                        <input <?php if($isdefault){ ?>disabled="true"<?php }?> type="text" class="form-control width50" name="pos[<?= $a->id ?>]" value="<?= $a->pos ?>" />
                                    </td>
                                    <td>
                                        <label class="css-input css-checkbox css-checkbox-success">
                                            <input <?php if($isdefault){ ?>disabled="true"<?php }?> id="checkbox-<?=$a->id?>" type="checkbox" name="tplid[]" value="<?= $a->id ?>"><span></span> <?=$a->name?>
                                        </label>
                                    </td>
                                    <td>
                                        <label class="css-input css-radio css-radio-success push-10-r">
                                            <input <?php if($isdefault){ ?>disabled="true"<?php }?> type="radio" name="ismust[<?= $a->id ?>]" id="inlineRadio1-<?=$a->id?>" value="1"><span></span> 必填
                                        </label>
                                        <label class="css-input css-radio css-radio-success">
                                            <input <?php if($isdefault){ ?>disabled="true"<?php }?> type="radio" name="ismust[<?= $a->id ?>]" id="inlineRadio2-<?=$a->id?>" value="0"><span></span> 不必填
                                        </label>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                        </div>
                    <?php if(!$isdefault) { ?>
                    <p><input type="submit" class="btn btn-success btn-sm btn-minw" value="保存"></p>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        init_checkbox();
        init_radio();
    });

    function init_checkbox() {
        var selectids = $("#fitpageitemidstr").val();
        if (selectids.length>0) {
            var idArr = selectids.split('|');

            $("input[type=checkbox][name='tplid[]']").each(function () {
                if ($.inArray($(this).val(), idArr) > -1) {
                    $(this).prop("checked",true);
                    $("#inlineRadio1-" + $(this).val()).prop("checked",true);
                }
            });
        }
    };

    function init_radio () {
        var selectids = $("#fitpageitemidstr").val();
        var ismusts = $("#ismuststr").val();
        if (selectids.length>0) {
            var idArr = selectids.split('|');
            var ismustArr = ismusts.split('|');
            var arr = [];
            for (var i = 0; i < ismustArr.length; i++) {
                var tmparr = ismustArr[i].split('-');
                arr["" + tmparr[0] + ""] = tmparr[1];
            }

            for (var i = 0; i < idArr.length; i++) {
                var ismust = arr[idArr[i]];
                if (ismust == 1) {
                    $("#inlineRadio1-" + idArr[i]).prop('checked',true);
                    $("#inlineRadio2-" + idArr[i]).prop('checked',false);
                } else if (ismust == 0) {
                    $("#inlineRadio2-" + idArr[i]).prop('checked',true);
                    $("#inlineRadio1-" + idArr[i]).prop('checked',false);
                }
            }
        }
    }
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
