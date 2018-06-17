<?php
if (!$course instanceof Course) {
    $pagetitle = "音频课文新建";
} else {
    $pagetitle = "音频课文新建  of 课程:{$course->title}";
}
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
        <form action="/lessonmgr/addpost" method="post">
            <input type="hidden" id="doctorid" name="doctorid" value="<?= $doctorid ?>"/>
            <input type="hidden" id="addtype" name="addtype" value="voice"/>
            <input type="hidden" name="courseid" value="<?= $course->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 60%;"/>
                    </td>
                </tr>
                <tr>
                    <th>简介</th>
                    <td>
                        <textarea id="brief" name="brief" cols=80 rows=8></textarea>
                    </td>
                </tr>
                <tr>
                    <th>课文封面</th>
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
                    <th>音频内容</th>
                    <td>
                        <?php
                        $voiceInputName = "voiceid";
                        require_once("$dtpl/voice.ctr.php");
                        ?>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="添加课"/>
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
