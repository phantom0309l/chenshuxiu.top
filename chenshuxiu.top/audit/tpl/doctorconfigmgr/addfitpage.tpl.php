<?php
$dbCodeArr = [
    '基本信息' => 'patientbaseinfo',
    '病历信息' => 'patientpcard',
    '病史信息' => 'diseasehistory'
];
$sideBarMini = true;
$isDbBaseinfo = in_array($fitpagetpl->code, $dbCodeArr);
if ($isDbBaseinfo) {
    $breadcrumbs = [
        "/doctorconfigmgr/fitpage?doctorid={$doctor->id}&diseaseid={$disease->id}&code={$fitpagetpl->code}" => "数据库基本信息",
    ];
    $pagetitle = "自定义数据库基本信息";
} else {
    $breadcrumbs = [
        "/doctorconfigmgr/fitpage?doctorid={$doctor->id}&diseaseid={$disease->id}" => "报到",
    ];
    $pagetitle = "自定义报到页面";
}
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
$pageStyle = <<<STYLE
.block-options > li > a {
    opacity: 1;
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
        <form class="form-horizontal" method="post" action="/doctorconfigmgr/addfitpagepost">
            <input type="hidden" name="doctorid" value="<?=$doctor->id ?>">
            <input type="hidden" name="diseaseid" value="<?=$disease->id ?>">
            <input type="hidden" name="code" value="<?=$fitpagetpl->code ?>">

            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td width="90">code</td>
                    <td class="">
                        <?=$fitpagetpl->code?>
                    </td>
                </tr>
                <tr>
                    <td width="90">名称</td>
                    <td class="">
                        <?=$fitpagetpl->name?>
                    </td>
                </tr>
                <tr>
                    <td>疾病</td>
                    <td>
                    <div class="col-md-6 col-xs-12 remove-padding">
                        <div class="form-control-static"><?=$disease->name?></div>
                    </div>
            </td>
                </tr>
                <tr>
                    <td>备注</td>
                    <td>
                        <div class="col-md-6 col-xs-12 remove-padding">
                            <textarea class="form-control" name="remark" rows="10" cols="50"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input class="btn btn-success btn-minw" type="submit" value="提交">
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<SCRIPT
SCRIPT
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
