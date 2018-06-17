<?php
$pagetitle = "课文列表 Lessons";
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
            <form action="/lessonmgr/list" method="get" class="pr">
                按课程筛选：
                <select id="selectCourse">
                    <option value="0">请选择</option>
                    <?php
                    foreach ($courses as $a) {
                        ?>
                        <option value="<?= $a->id ?>"><?= $a->title . $a->subtitle ?> (<?= $a->getLessonCnt(); ?>)
                        </option>
                        <?php

                    }
                    ?>
                </select>
                (注:选择课程才能新建课文) &nbsp;&nbsp;&nbsp;
                <label for="">按课文名模糊查找：</label>
                <input type="text" name="lesson_name" value="<?= $lesson_name ?>"/>
                <input type="submit" value="搜索"/>
            </form>
        </div>
        <form action="/lessonmgr/posmodifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>创建日期</th>
                    <th>课文</th>
                    <th>课程</th>
                    <th>巩固</th>
                    <th>作业</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($lessons as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->title ?></td>
                        <td>
                            <?php
                            foreach ($a->getCourses() as $_course) {
                                ?>
                                <a href="/lessonmgr/listofcourse?courseid=<?= $_course->id ?>">
                                    <?php
                                    echo $_course->title;
                                    if ($_course->subtitle) {
                                        echo " [{$_course->subtitle}]";
                                    }
                                    ?>
                                </a>
                                <br/>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($a->testxquestionsheetid > 0) {
                                $_url = "/xquestionmgr/list?xquestionsheetid={$a->testxquestionsheetid}";
                                $_name = "要点巩固";
                            } else {
                                $temp = urlencode('要点巩固:' . $a->title);
                                $_url = "/xquestionsheetmgr/add?objtype=Lesson&objid={$a->id}&objcode=test&title={$temp}";
                                $_name = "创建巩固问卷";
                            }
                            ?>
                            <a target="_blank" href="<?= $_url ?>"><?= $_name ?></a>
                        </td>
                        <td>
                            <?php
                            if ($a->hwkxquestionsheetid > 0) {
                                $_url = "/xquestionmgr/list?xquestionsheetid={$a->hwkxquestionsheetid}";
                                $_name = "课后作业";
                            } else {
                                $temp = urlencode('课后作业:' . $a->title);
                                $_url = "/xquestionsheetmgr/add?objtype=Lesson&objid={$a->id}&objcode=hwk&title={$temp}";
                                $_name = "创建作业问卷";
                            }
                            ?>
                            <a target="_blank" href="<?= $_url ?>"><?= $_name ?></a>
                        </td>
                        <td>
                            <a href="/lessonmgr/modify?lessonid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
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
    $(document).on(
        "change",
        "#selectCourse",
        function () {
            var val = parseInt($(this).val());
            var url = '/lessonmgr/listofcourse?courseid=' + val;
            window.location.href = url;
        }
    );
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
