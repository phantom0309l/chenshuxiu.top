<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/5/28
 * Time: 17:40
 */

$pagetitle = "水滴-绿色通道申请列表";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                <tr>
                    <td width="90">id</td>
                    <td>疾病</td>
                    <td class="tc">城市</td>
                    <td class="tc" style="width: 190px;">期望就诊时段</td>
                    <td class="tc" style="width: 120px;">实际就诊日期</td>
                    <td>发送内容</td>
                    <td class="tc" style="width: 90px;">状态</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($drip_greenChannels as $drip_greenChannel) { ?>
                    <tr>
                        <td><?= $drip_greenChannel->id ?></td>
                        <td><?= $drip_greenChannel->diseasestr ?></td>
                        <td class="tc"><?= $drip_greenChannel->xcity->name ?></td>
                        <td class="tc">
                            <?= $drip_greenChannel->expecteddate ?>
                            至
                            <?= $drip_greenChannel->bounddate ?>
                        </td>
                        <td class="tc"><?= $drip_greenChannel->actualdate ?></td>
                        <td><?= $drip_greenChannel->content ?></td>
                        <td class="tc">
                            <?php
                            $status_class = "default";
                            switch ($drip_greenChannel->status) {
                                case 0:
                                    $status_class = "default";
                                    break;
                                case 1:
                                    $status_class = "primary";
                                    break;
                                case 2:
                                    $status_class = "success";
                                    break;
                                default:
                                    break;
                            } ?>
                            <span class="label label-<?= $status_class ?>"><?= $drip_greenChannel->getStatusStr() ?></span>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
