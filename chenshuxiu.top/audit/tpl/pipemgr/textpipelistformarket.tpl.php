<?php
$pagetitle = "报表统计首页";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<table class="table-bordered">
    <tr>
        <td>患者名</td>
        <td>对话</td>
    </tr>

    <?php
    foreach ($patients as $patient) {
        $cond = "and patientid=:patientid and ( ( objtype = 'WxTxtMsg') or (objtype= 'PushMsg' and objcode='byAuditor')) order by createtime ";
        $bind = [];
        $bind[':patientid'] = $patient->id;
        $pipes = Dao::getEntityListByCond('Pipe', $cond, $bind);
        ?>
    <tr>
        <td><?= $patient->name ?></td>
        <td>
        <?php
        foreach ($pipes as $pipe) {
            if ($pipe->objtype == 'WxTxtMsg') {
                echo "患者: " . $pipe->obj->content;
            } else {
                echo "运营: " . $pipe->obj->content;
            }
            ?>
            <br />
        <?php }?>
        </td>
    </tr>
    <?php } ?>
</table>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>