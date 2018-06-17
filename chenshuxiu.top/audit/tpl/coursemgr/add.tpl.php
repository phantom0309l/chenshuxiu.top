<?php
$pagetitle = "课程新建";
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
        <form action="/coursemgr/addpost" method="post">
            <input type="hidden" id="doctorid" name="doctorid" value="<?= $doctorid ?>"/>
            <input type="hidden" id="addtype" name="addtype" value="<?= $addtype ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>课程主标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 50%;"/>
                    </td>
                </tr>
                <tr>
                    <th>课程副标题</th>
                    <td>
                        <input id="subtitle" type="text" name="subtitle" style="width: 50%;"/>
                    </td>
                </tr>
                <tr>
                    <th>课程所属分组</th>
                    <td>
                        <input id="groupstr" type="text" name="groupstr" style="width: 20%;"/>
                        (必填)
                    </td>
                </tr>
                <tr>
                    <th>段落标题一</th>
                    <td>
                        <input id="title1" type="text" name="title1" style="width: 80%;"/>
                    </td>
                </tr>
                <tr>
                    <th>段落标题二</th>
                    <td>
                        <input id="title2" type="text" name="title2" style="width: 80%;"/>
                    </td>
                </tr>
                <tr>
                    <th>段落标题三</th>
                    <td>
                        <input id="title3" type="text" name="title3" style="width: 80%;"/>
                    </td>
                </tr>
                <tr>
                    <th>课程简介</th>
                    <td>
                        <textarea id="brief" name="brief" cols="100" rows="10"></textarea>
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
                        <input type="submit" value="创建课程"/>
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
