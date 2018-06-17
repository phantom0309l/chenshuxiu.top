<?php
$pagetitle = "音频文件列表 voices";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>voiceid</th>
                        <th>创建日期</th>
                        <th>图片</th>
                        <th>标题</th>
                        <th>简介</th>
                        <th>文件名</th>
                        <th>文件后缀</th>
                        <th>大小</th>
                        <th>来源</th>
                        <th>状态</th>
                        <th>编辑</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($voices as $a) {
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td class=''>
                            <?php
                            if($a->pictureid) {
                                $picWidth = 150;
                                $picHeight = 150;
                                $pictureInputName = "pictureid";
                                $isCut = false;
                                $picture = $a->picture;
                                require_once("$dtpl/picture.ctr.php");
                            }
                            ?>
                        </td>
                        <td><?= $a->title ?></td>
                        <td><?= $a->content ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->ext ?></td>
                        <td><?= $a->size . "k" ?></td>
                        <td><?= $a->type ?></td>
                        <td><?= $a->status == 1 ? "有效":"无效" ?></td>
                        <td>
                            <a class="btn btn-default item-button"
                               href="/voicemgr/modify?voiceid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                        <?php } ?>
                        <tr>
                        <td colspan=100 class="pagelink">
                        	<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
