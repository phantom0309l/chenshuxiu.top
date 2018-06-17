<?php
$pagetitle = '测试微信号管理';
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
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>wxuserid</td>
                        <td>userid</td>
                        <td>patientid</td>
                        <td>微信号</td>
                        <td>微信昵称</td>
                        <td>危险操作</td>
                        <td>危险操作</td>
                        <td>是否报到</td>
                        <td>所属医生</td>
                        <td>扫码进入</td>
                        <td>国家,省,市</td>
                        <td>关注时间</td>
                        <td>退订</td>
                    </tr>
                </thead>
<?php
foreach ($wxusers as $a) {
    ?>
                    <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->userid ?></td>
                    <td><?= $a->user->patientid ?></td>
                    <td><?= $a->wxshop->shortname ?></td>
                    <td><?= $a->nickname ?></td>
                    <td>
                        <?php if($a->user->patient->name != ''){ ?>
                        <a href="/wxusermgr/ResetPatientIdPost?nickname=<?= $a->nickname ?>">取消报到!</a>
                            <?php }?>
                            </td>
                    <td>
                        <a href="/wxusermgr/deletewxuserPost?nickname=<?= $a->nickname ?>">删除关注:wxuser,user!</a>
                    </td>
                    <td><?=($a->user->patient->name != '') ? 'yes' : 'no'?></td>
                    <td><?= $a->user->patient->doctor->name ?></td>
                    <td><?= $a->wx_ref_code?'Yes':'No'; ?></td>
                    <td><?= $a->country ?>,<?= $a->province ?>,<?= $a->city ?></td>
                    <td><?= $a->subscribe_time ?></td>
                    <td><?= $a->subscribe?'':'退订' ?> <?= $a->subscribe?'':$a->unsubscribe_time; ?></td>
                </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
