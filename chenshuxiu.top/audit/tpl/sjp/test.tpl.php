<?php
$pagetitle = "sjp测试";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
];
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = true;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<h3>王宫瑜需求 #5403, #5755</h3>
<div class="col-md-12">
    <form action="/sjp/test" method="get">
        <div class="col-xs-3">
            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
        </div>
        <div class="col-xs-9">
            <input type="submit" value="搜索" />
            ( 前500个患者 )
        </div>
    </form>
</div>
<br />
<br />
<div class="col-md-12">
    <div class="table-responsive m10">
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>姓名</th>
                <th>年龄</th>
                <th>性别</th>
                <th>死亡日期</th>
                <th>电话</th>
                <th width="140">最后一次联系</th>
                <th width="60%">运营备注</th>
            </tr>
            <?php
            foreach ($patients as $i => $patient) {
                ?>
            <tr>


            <tr>
                <td><?= $i+1 ?></td>
                <td><?= $patient->name ?></td>
                <td><?= $patient->getAgeStr() ?></td>
                <td><?= $patient->getSexStr() ?></td>
                <td><?= $patient->getDeadDate() ?></td>
                <td>
                <?php
                $linkmans = $patient->getLinkmans();
                foreach ($linkmans as $linkman) {
                    echo $linkman->mobile . ",";
                }
                ?>
                </td>
                <td>
                <?php
                $sql = "select createtime
                    from pipes
                    where patientid = {$patient->id} and objtype in ('WxPicMsg', 'WxTxtMsg', 'WxVoiceMsg')
                    order by createtime desc
                    limit 1 ";
                $maxcreatetime = Dao::queryValue($sql);
                echo substr($maxcreatetime, 0, 16);
                ?>
                </td>
                <td>
                <?php
                echo nl2br($patient->opsremark) . "\n";
                $patientrecords = PatientRecordDao::getListByPatientid($patient->id);
                if (count($patientrecords) > 0) {
                    echo "<div class='border-top-blue'>";
                    foreach ($patientrecords as $a) {
                        ?><div><?= PatientRecordHelper::getShortDesc($a) ?></div><?php
                    }
                    echo "</div>";
                }
                ?>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>
<div class="clear"></div>
<script type="text/javascript">
$('.js-select2').select2();
</script>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
