<?php
$pagetitle = "课文修改";
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
        <form action="/lessonmgr/modifypost" method="post">
            <input type="hidden" name="lessonid" value="<?= $lesson->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 80%;" value="<?= $lesson->title ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>简介</th>
                    <td>
                        <textarea id="brief" name="brief"
                                  style="width: 80%; height: 150px;"><?= $lesson->brief ?></textarea>
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
                        <textarea id="keypoints" name="keypoints"
                                  style="width: 80%; height: 250px;"><?= $lesson->keypoints ?></textarea>
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
                        $picture = $lesson->picture;
                        require_once("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                </tr>
                <?php if ($lesson->voiceid > 0) { ?>
                    <tr>
                        <th>音频内容</th>
                        <td>
                            <!--                            <audio controls="controls" src="-->
                            <? //= ROOT_TOP_PATH . "/wwwroot/voice/doctorlesson/".$lesson->voice->name.".".$lesson->voice->ext?><!--">-->
                            <!--                                浏览器不支持audio标签，抱歉私密达！</audio>-->
                            <audio id="musicfx" loop="loop" autoplay="autoplay" controls="controls">
                                <source src="<?= $lesson->voice->getUrl() ?>" type="audio/mpeg">
                            </audio>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>内容</th>
                    <td>
                        <textarea id="content" name="content"
                                  style="width: 80%; height: 300px;"><?= $lesson->content ?></textarea>
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
                        <a href="<?= $wx_uri ?>/lesson/justforshow?lessonid=<?= $lesson->id ?>&openid=<?= $myauditor->user->createwxuser->openid ?>"
                           target="_blank">微信端预览地址</a>
                    </td>
                </tr>
                <tr>
                    <th>作业提示</th>
                    <td>
                        <input id="hwktip" type="text" name="hwktip" style="width: 80%;"
                               value="<?= $lesson->hwktip ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>课程时长(现用于分组课程)</th>
                    <td>
                        <input id="hwktip" type="text" name="open_duration" style="width: 80%;"
                               value="<?= $lesson->open_duration ?>"/>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="修改课"/>
                    </td>
                </tr>
            </table>
            </div>
        </form>

        <?php

        $pagetitle = "内容展示预览";
        include $tpl . "/_pagetitle.php";
        ?>
        <div class="border1 p10">
            <p style="font-size: 14px; color: #777; margin-top: 30px; width: 93%; line-height: 16px; margin-left: 5%; letter-spacing: 1px">
                <?= $lesson->getContentNl2br() ?>
            </p>
        </div>

        <?php

        $pagetitle = "所属课程 (todo: 这个功能需要迁移到单独页面)";
        include $tpl . "/_pagetitle.php";
        ?>
        <div class="border1 p10">
            <?php foreach ($courselessonrefs as $a) { ?>
                <p><?= $a->course->title ?><?= $a->course->subtitle ?>
                    <a href="/lessonmgr/modifydelrefpost?courselessonrefid=<?= $a->id ?>">删除</a>
                </p>
            <?php } ?>
            <form action="/lessonmgr/modifyaddrefpost" method="post">
                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toCourseCtrArray($courses), 'courseid', 0); ?>
                <input type="hidden" name="lessonid" value="<?= $lesson->id ?>"/>
                <input type="submit" value="添加"/>
            </form>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        App.initHelper('select2');
        var editor = new wangEditor('content');
        editor.config.hideLinkImg = true;
        editor.config.uploadImgFileName = 'imgurl'
        editor.config.uploadImgUrl = '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial&fromWeditor=1';

        editor.create();
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
