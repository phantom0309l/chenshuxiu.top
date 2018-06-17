<?php
$pagetitle = "paitent状态日志 Patient_Status_Log";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/static/js/jquery-1.11.1.min.js"
]; //填写完整地址
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
                <form action="/patient_status_logmgr/list" method="get">
                    <div class="mt10">
                        <label>patientid:</label>
                        <input name="patientid" value="<?= $patientid == 0 ? '' : $patientid;?>" />
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value='筛选' />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td>patientid</td>
                    <td>createtime</td>
                    <td>name</td>
                    <td>doctor</td>
                    <td>操作</td>
                    <td>操作人</td>
                    <td>修改后</td>
                    <td>修改前</td>
                    <td>关注数</td>
                </tr>
                <?php
                    foreach ($patient_status_logs as $a) {
                        list($type,$auditorname) = explode(":", $a->content);
                    ?>
                        <tr>
                            <td><?= $a->patientid ?></td>
                            <td><?= $a->createtime ?></td>
                            <td><?= $a->patient->name ?></td>
                            <td><?= $a->patient->doctor->name ?></td>
                            <td><?= $type ?></td>
                            <td><?= $auditorname ?></td>
                            <td>
                                <?php
                                    $arr = json_decode($a->patient_status_json,true);
                                    foreach ($arr as $k => $v) {
                                        echo "{$k}:{$v}" . "<br>";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $arr = json_decode($a->patient_status_old_json,true);
                                    foreach ($arr as $k => $v) {
                                        echo "{$k}:{$v}" . "<br>";
                                    }
                                ?>
                            </td>
                            <td><?= $a->patient->subscribe_cnt ?></td>
                        </tr>
                    <?php
                    }
                ?>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>