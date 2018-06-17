<?php
$pagetitle = "分享用户列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-6">
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <th>序号</th>
                <th>userid</th>
                <th>微信名</th>
                <th>患者名</th>
                <th>分享数</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 0;
            foreach ($shareuserarray as $a) {
                $user = User::getById($a["ref_objid"]);
                if (false == $user instanceof User) {
                    continue;
                }
                $wxuser = $user->getMasterWxUser(3);
                if (!$wxuser instanceof WxUser) {
                    $wxuser = $user->getMasterWxUser();
                }
                $index++;
                $patient = $user->patient;
                ?>
                <tr>
                    <td><?= $index ?></td>
                    <td><?= $user->id ?></td>
                    <td><?= $wxuser->nickname ?></td>
                    <?php
                    if ($patient instanceof Patient) {
                        ?>
                        <td><?= $patient->getMaskName() ?></td>
                        <?php
                    } else {
                        ?>
                        <td><?= "" ?></td>
                        <?php
                    }
                    ?>
                    <td><?= $a["cnt"] ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
    </section>
    <section class="col-md-6">
        <h4>完课及快完课用户名单</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <th>更新时间</th>
                <th>微信号</th>
                <th>患者名</th>
                <th>性别</th>
                <th>年龄</th>
                <th>进度</th>
            </tr>
            </thead>
            <tbody>

            <?php
            foreach ($courseuserrefs as $a) {
                $user = $a->user;
                if (false == $user instanceof User)
                    continue;
                $patient = $user->patient;
                ?>

                <tr>
                    <td><?= $a->updatetime ?></td>
                    <td><?= $a->wxuser->nickname ?></td>
                    <?php
                    if ($patient instanceof Patient) {
                        ?>
                        <td><?= $patient->getMaskName() ?></td>
                        <td><?= $patient->getSexStr() ?></td>
                        <td><?= $patient->getAgeStr() ?> 岁</td>
                        <?php
                    } else {
                        ?>
                        <td><?= $user->getShowName() ?></td>
                        <td><?= "" ?></td>
                        <td><?= "" ?></td>
                        <?php
                    }
                    ?>
                    <td><?= $a->pos ?></td>
                </tr>

            <?php } ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
