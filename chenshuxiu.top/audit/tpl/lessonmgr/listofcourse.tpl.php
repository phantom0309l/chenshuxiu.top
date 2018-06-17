<?php
$pagetitle = "课文列表 of 课程: {$course->title}";
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
        <div class="searchBar">
            <a class="btn btn-success" href="/lessonmgr/add?courseid=<?= $course->id ?>">添加课文( of <?= $course->title ?>
                )</a>
            注:一个课文可关联多个课程
        </div>
        <div class="searchBar">
            <form action="/lessonmgr/addrefpost" method="post">
                courselessonrefid
                <input type="text" name="courselessonrefid" value=""/>
                openid
                <input type="text" name="openid" value=""/>
                <input type="submit" value="生成权限"/>
                TODO by xuzhe : 一个后门功能
            </form>
        </div>
        <form action="/lessonmgr/posmodifypost" method="post">
            <input type="hidden" name="courseid" value="<?= $course->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td>ID</td>
                    <td>CourseLessonRefID</td>
                    <td>创建日期</td>
                    <td>课文</td>
                    <td>用户数</td>
                    <td style="width: 40px">序号</td>
                    <td>课长</td>
                    <td>巩固</td>
                    <td>作业</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($courselessonrefs as $a) {
                    ?>
                    <tr>
                        <td><?= $a->lessonid ?></td>
                        <td><?= $a->id ?></td>
                        <td><?= $a->lesson->createtime ?></td>
                        <td><?= $a->lesson->title ?></td>
                        <td>
                            <a href="/lessonuserrefmgr/list?lessonid=<?= $a->lessonid ?>"><?= $a->lesson->getUserCnt() ?></a>
                        </td>
                        <td>
                            <input type="text" name='pos[<?= $a->id ?>]' value="<?= $a->pos ?>" style="width: 40px"/>
                        </td>
                        <td><?= $a->lesson->open_duration ?></td>
                        <td>
                            <?php
                            if ($a->lesson->testxquestionsheetid > 0) {
                                $_url = "/xquestionmgr/list?xquestionsheetid={$a->lesson->testxquestionsheetid}";
                                $_txt = '要点巩固';
                            } else {
                                $temp = urlencode('要点巩固:' . $a->lesson->title);
                                $_url = "/xquestionsheetmgr/add?objtype=Lesson&objid={$a->lesson->id}&objcode=test&title={$temp}";
                                $_txt = '创建巩固问卷';
                            }
                            ?>
                            <a target="_blank" href="<?= $_url ?>"><?= $_txt ?></a>
                        </td>
                        <td>

                            <?php
                            if ($a->lesson->hwkxquestionsheetid > 0) {
                                $_url = "/xquestionmgr/list?xquestionsheetid={$a->lesson->hwkxquestionsheetid}";
                                $_txt = '课后作业';
                            } else {
                                $temp = urlencode('课后作业:' . $a->lesson->title);
                                $_url = "/xquestionsheetmgr/add?objtype=Lesson&objid={$a->lesson->id}&objcode=hwk&title={$temp}";
                                $_txt = '创建作业问卷';
                            }
                            ?>
                            <a target="_blank" href="<?= $_url ?>"><?= $_txt ?></a>
                        </td>
                        <td>
                            <a target="_blank" href="/lessonmgr/modify?lessonid=<?= $a->lesson->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan=20 align=right>
                        <input type="submit" value="保存序号修改"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                </tbody>
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
