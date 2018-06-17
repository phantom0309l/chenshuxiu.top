<?php
$pagetitle = "周患者用药及入组率";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
<!--            <div class="searchBar">活跃率 = 总活跃报到人数 / 总报到人数</div>-->
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>日期－周一</td>
                        <td>日期－周末</td>
                        <td>
                            总报到
                            <br />
                            人数
                        </td>
                        <td>
                            总报到（扫码）
                            <br />
                            人数
                        </td>
                        <td>
                            总服药
                            <br />
                            人数
                        </td>
                        <td>
                            报到4天内服药
                            <br />
                            人数
                        </td>
                        <td>
                            报到4天内服药
                            <br />
                            与报到（扫码）总人数
                            <br />
                            比率
                        </td>
                        <td>
                            报到4天内服药有催用药
                            <br />
                            与报到4天内服药无催用药
                            <br />
                            比较
                        </td>
                        <td>
                            报到入组
                            <br />
                            人数
                        </td>
                        <td>
                            到现在为止报到入组
                            <br />
                            人数
                        </td>
                        <td>
                            报到4天内入组
                            <br />
                            人数
                        </td>
                        <td>
                            报到4天内入组总人数
                            <br />
                            与报到（扫码）总人数
                            <br />
                            比率
                        </td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($list as $a) {
                    ?>
                    <tr>
                        <td><?= $a->mondaydate ?></td>
                        <td><?= $a->sundaydate ?></td>
                        <td><?= $a->baodaocnt ?></td>
                        <td><?= $a->baodao_scan_cnt ?></td>
                        <td><?= $a->had_drug_cnt ?></td>
                        <td><?= $a->had_drug_cnt_limit4day ?></td>
                        <td><?= $a->had_drug_ratio.'%' ?></td>
                        <td><?= $a->had_drug_hadremind_cnt_limit4day.'/'.$a->had_drug_noremind_cnt_limit4day ?></td>
                        <td><?= $a->inpgroup_cnt ?></td>
                        <td><?= $a->getRealInpgroupcnt() ?></td>
                        <td><?= $a->inpgroup_cnt_limit4day ?></td>
                        <td><?= $a->ingroup_ratio.'%' ?></td>
                    </tr>
                <?php } ?>
                <?php
                foreach ($date as $k => $v) {
                    ?>
                    <tr>
                        <td><?= $v['mondaydate'] ?></td>
                        <td><?= $v['sundaydate'] ?></td>
                        <td><?= $v['baodaocnt'] ?></td>
                        <td><?= $v['baodao_scan_cnt'] ?></td>
                        <td><?= $v['had_drug_cnt'] ?></td>
                        <td><?= $v['had_drug_cnt_limit4day'] ?></td>
                        <td><?= $v['had_drug_ratio'].'%' ?></td>
                        <td><?= $v['had_drug_hadremind_cnt_limit4day'].'/'.$v['had_drug_noremind_cnt_limit4day'] ?></td>
                        <td><?= $v['inpgroup_cnt'] ?></td>
                        <td><?= $v['inpgroup_cnt'] ?></td>
                        <td><?= $v['inpgroup_cnt_limit4day'] ?></td>
                        <td><?= $v['ingroup_ratio'].'%' ?></td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=23>
                            <?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
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
