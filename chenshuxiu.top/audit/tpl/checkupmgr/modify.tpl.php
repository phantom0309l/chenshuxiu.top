<?php
$pagetitle = '运营系统首页';
$pagetitle = "检查修改";
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
            <form action="/checkupmgr/modifypost" method="post">
                <input type="hidden" name="checkupid" value="<?= $checkup->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>检查id</th>
                        <td>
                            <?= $checkup->id?>
                        </td>
                    </tr>
                    <tr>
                        <th>患者</th>
                        <td>
                            <?= $checkup->patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td>
                            <?= $checkup->patient->doctor->name?> of <?= $checkup->patient->doctor->hospital->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>检查日期</th>
                        <td>
                            <input type="text" class="calendar" name="check_date" value="<?= $checkup->check_date ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>检查医院</th>
                        <td>
                            <input type="text" name="hospitalstr" value="<?= $checkup->hospitalstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>检查结果</th>
                        <td>
                            <textarea name="content" cols=60 rows=4><?= $checkup->content ?></textarea>
                            检查医生的医嘱
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
                            <a href="/picturerefmgr/deletepicrefpost?picturerefid=<?= $a->id ?>&objtype=Checkup&objid=<?= $checkup->id ?>">删除</a>
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
                            $pictureref = PictureRefDao::getByObjPictureid($checkup, $picture->id);
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
                            <a href="/picturerefmgr/addpicrefpost?objid=<?= $checkup->id ?>&objtype=Checkup&pictureid=<?= $a->picture->id ?>">添加</a>
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
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
