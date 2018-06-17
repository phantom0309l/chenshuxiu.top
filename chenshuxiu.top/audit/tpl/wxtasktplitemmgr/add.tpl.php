<?php
$pagetitle = "新建方寸课堂活动子任务";
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
        <form action="/wxtasktplitemmgr/addpost" method="post">
            <input type="hidden" name="wxtasktplid" value="<?= $wxtasktplid ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>子任务主题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 50%;"/>
                    </td>
                </tr>
                <tr>
                    <th width=140>pos</th>
                    <td>
                        <input id="pos" type="text" name="pos" style="width: 50%;"/>
                    </td>
                </tr>
                <tr>
                    <th width=140>ename</th>
                    <td>
                        <input id="ename" type="text" name="ename" style="width: 50%;"/>
                    </td>
                </tr>
                <tr>
                    <th>子任务简介</th>
                    <td>
                        <textarea id="brief" name="brief" cols="100" rows="10"></textarea>
                    </td>
                </tr>
                <tr>
                    <th>子任务内容</th>
                    <td>
                        <textarea id="content" name="content" cols="100" rows="10"></textarea>
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
                        $picture = null;
                        require_once("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="创建活动"/>
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
