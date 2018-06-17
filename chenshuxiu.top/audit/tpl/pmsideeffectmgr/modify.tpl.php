<?php
$pagetitle = "药物副反应检测报告修改";
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
            <form action="/pmsideeffectmgr/modifypost" method="post">
                <input type="hidden" name="pmsideeffectid" value="<?= $pmsideeffect->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th>患者</th>
                        <td>
                            <?= $pmsideeffect->patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td>
                            <?= $pmsideeffect->doctor->name?> of <?= $pmsideeffect->doctor->hospital->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>患者反馈检查日期</th>
                        <td>
                            <input type="text" class="calendar" name="thedate"
                                   value="<?= $pmsideeffect->thedate == '0000-00-00' ? '' : $pmsideeffect->thedate ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>所针对药物</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toMedicineCtrArrayForPmSideEffect(PmSideEffect::getPMMedicines()), 'medicineid', $pmsideeffect->medicineid ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>检查结果备注</th>
                        <td>
                            <textarea name="content" cols=60 rows=4><?= $pmsideeffect->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
            <section class="col-md-6">
                <div style="width: 100%; text-align: center;">
                    <?php
                    $pagetitle = "已关联图片";
                    include $tpl . "/_pagetitle.php";
                    ?>
                    <?php foreach( $picturerefs as $a){?>
                        <div class="fl border1-blue" style="display: inline-block; margin: 10px;">
                        <p class="red">
                                <?= $a->picture->createtime?>
                            </p>
                        <div>
                            <a data-gallery class="imgShell" target="_blank" href="<?= $a->picture->getSrc() ?>">
                                <img src="<?= $a->picture->getSrc(200,200,false); ?>">
                            </a>
                        </div>
                        <div>
                            <a href="/picturerefmgr/deletepicrefpost?picturerefid=<?= $a->id ?>&objtype=PmSideEffect&objid=<?= $pmsideeffect->id ?>">删除</a>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </section>
            <section class="col-md-6">
                <div style="width: 100%; text-align: center;">
                    <?php
                    $pagetitle = "该患者所有剩下图片";
                    include $tpl . "/_pagetitle.php";
                    ?>
                    <?php

foreach ($wxpicmsgs as $a) {
                        $picture = $a->picture;
                        if ($picture instanceof Picture) {
                            $pictureref = PictureRefDao::getByObjPictureid($pmsideeffect, $picture->id);
                            if (false == $pictureref instanceof PictureRef) {
                                ?>
                            <div class="fl border1-blue" style="display: inline-block; margin: 10px">
                        <p class="red">
                                    <?= $picture->createtime?>
                                </p>
                        <div>
                            <a data-gallery class="imgShell" target="_blank" href="<?= $picture->getSrc() ?>">
                                <img src="<?= $picture->getSrc(200,200,false); ?>">
                            </a>
                        </div>
                        <div>
                            <a href="/picturerefmgr/addpicrefpost?objid=<?= $pmsideeffect->id ?>&objtype=PmSideEffect&pictureid=<?= $a->picture->id ?>">添加</a>
                        </div>
                    </div>
                        <?php

}
                        }
                    }
                    ?>
                </div>
            </section>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>