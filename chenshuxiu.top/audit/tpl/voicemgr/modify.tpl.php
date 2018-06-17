<?php
$pagetitle = "音频资源修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/voicemgr/modifypost" method="post">
                <input type="hidden" name="voiceid" value="<?= $voice->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>voiceid</th>
                        <td><?= $voice->id ?></td>
                    </tr>
                    <tr>
                        <th>图片</th>
                        <td>
                            <?php
                            $picWidth = 150;
                            $picHeight = 150;
                            $pictureInputName = "pictureid";
                            $isCut = false;
                            $picture = $voice->picture;
                            require_once("$dtpl/picture.ctr.php");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>标题</th>
                        <td><input type="text" name="title" value="<?= $voice->title ?>" /></td>
                    </tr>
                    <tr>
                        <th>简介</th>
                        <td><textarea type="text" rows="10" cols="40" name="content" value="<?= $voice->content ?>"></textarea></td>
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
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
