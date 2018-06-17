<?php
$pagetitle = "活动修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-10">\
        <form action="/wxtasktplmgr/modifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <input type="hidden" name="wxtasktplid" value="<?= $wxtasktpl->id ?>"/>
                <tr>
                    <th width=140>id</th>
                    <td><?= $wxtasktpl->id ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $wxtasktpl->createtime ?></td>
                </tr>
                <tr>
                    <th>修改时间</th>
                    <td><?= $wxtasktpl->updatetime ?></td>
                </tr>
                <tr>
                    <th>活动标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 80%;"
                               value="<?= $wxtasktpl->title ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>ename</th>
                    <td>
                        <input id="ename" type="text" name="ename" style="width: 80%;"
                               value="<?= $wxtasktpl->ename ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>简介</th>
                    <td>
                        <textarea id="brief" name="brief" cols="100" rows="10"><?= $wxtasktpl->brief ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td>
                        <textarea id="content" name="content" cols="100" rows="10"><?= $wxtasktpl->content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>配图</th>
                    <td>
                        <?php
                        $picWidth = 150;
                        $picHeight = 150;
                        $pictureInputName = "pictureid";
                        $isCut = false;
                        $picture = $wxtasktpl->picture;
                        require_once("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="修改活动"/>
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
