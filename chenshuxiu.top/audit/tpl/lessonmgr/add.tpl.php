<?php
if (!$course instanceof Course) {
    $pagetitle = "课文新建 todo: 强烈建议从课程列表中点击课文后添加相应课程的课文";
} else {
    $pagetitle = "课文新建  of 课程:{$course->title}";
}
$cssFiles = [
    "{$img_uri}/static/css/article.css",
    "{$img_uri}/v5/plugin/weditor/css/wangEditor.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v5/plugin/weditor/js/wangEditor.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <form action="/lessonmgr/addpost" method="post">
            <input type="hidden" id="addtype" name="addtype" value="content"/>
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
                    <th>医生</th>
                    <td>
                        <?php include_once $tpl . "/_select_doctor.tpl.php"; ?>
                    </td>
                </tr>
                <tr>
                    <th>课后作业要点</th>
                    <td>
                        <p>示例：中括号里的项以英文逗号分开；最后一项不要有逗号</p>
                        <p>
                            {
                            "keypoints" : [
                            "我是要点一",
                            "我是要点二",
                            "我是要点三"
                            ]
                            }
                        </p>
                        <textarea id="keypoints" name="keypoints" style="width: 80%; height: 250px;"></textarea>
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
                    <th>内容</th>
                    <td>
                        <textarea id="content" name="content" style="width: 80%; height: 300px;"></textarea>
                    </td>
                    <td style="width: 30%">
                        预订样式展示: 基本语法《span class="参数（参数之间有空格）"》《/span》（把书名号改为尖括号）
                        <br/>
                        正常:展示样式
                        <br/>
                        ab-red:
                        <span class="ab-red">展示样式</span>
                        <br/>
                        ab-blue:
                        <span class="ab-blue">展示样式</span>
                        <br/>
                        ab-orange:
                        <span class="ab-orange">展示样式</span>
                        <br/>
                        ab-grey:
                        <span class="ab-grey">展示样式</span>
                        <br/>
                        ab-bold:
                        <span class="ab-bold">展示样式</span>
                        <br/>
                        实际正文正常字号：16:
                        <span style="font-size: 16px">展示样式</span>
                        <br/>
                        ab-12: 12:
                        <span class="ab-12">展示样式</span>
                         <br/>
                        ab-14: 14:
                        <span class="ab-14">展示样式</span>
                        <br/>
                        ab-16: 16:
                        <span class="ab-16">展示样式</span>
                        <br/>
                        ab-18: 18:
                        <span class="ab-18">展示样式</span>
                        <br/>
                        ab-20: 20:
                        <span class="ab-20">展示样式</span>
                        <br/>
                        <a href="/lessonmgr/uploadimage" target="_blank">添加图片素材</a>
                    </td>
                </tr>
                <tr>
                    <th>作业提示</th>
                    <td>
                        <textarea id="hwktip" name="hwktip" cols=80 rows=30></textarea>
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
$(function(){
    var editor = new wangEditor('content');
    editor.config.hideLinkImg = true;
    editor.config.uploadImgFileName = 'imgurl'
    editor.config.uploadImgUrl = '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial&fromWeditor=1';

    editor.create();
})
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
