<?php
$pagetitle = "重复患者列表(不包括孤岛患者) Patients （{$patient_cnt}）";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="mergealready">特殊患者合并</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>min_createtime</td>
                        <td>max_createtime</td>
                        <td>patientid</td>
                        <td>doctorid</td>
                        <td>生日</td>
                        <td>患者名</td>
                        <td>数量</td>
                        <td>合并/信息比对</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (empty($repetition_patients)) {
                    echo "没有重复的";
                }
                foreach ($repetition_patients as $i => $a) {
                    $doctorids = $a['doctorids'];
                    $sql = "select id, name from doctors where id in ($doctorids) ";
                    $rows = Dao::queryRows($sql);
                    ?>
                            <tr>
                        <td><?= $i+1; ?></td>
                        <td><?= substr($a['min_createtime'],0,10); ?></td>
                        <td><?= substr($a['max_createtime'],0,10); ?></td>
                        <td><?= str_replace(',', '<br/>', $a['ids']); ?></td>
                        <td><?php
                    foreach ($rows as $row) {
                        echo $row['id'] . " " . $row['name'] . "<br/>";
                    }
                    ?></td>
                        <td><?= str_replace(',', '<br/>',$a['birthdays']); ?></td>
                        <td><?= $a['name'] ?></td>
                        <td><?= $a['cnt'] ?></td>
                        <td>
                            <a target="_blank" class="btn btn-success" href="/patientmgr/list4bind?patientid=<?=min(explode(",", $a['ids']))?>">患者合并</a>
                            <a target="_blank" class="btn btn-success" href="/patientmgr/mergealready?patientids=<?=$a['ids']?>">比对患者信息</a>
                        </td>
                    </tr>
                        <?php
                }
                ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
