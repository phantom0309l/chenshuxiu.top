<?php
$pagetitle = "课程列表 Courses";
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
            <a class="btn btn-success" href="/coursemgr/add">课程新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>序号</td>
                <td>课程所属组</td>
                <td>创建日期</td>
                <td>所针对用户/患者</td>
                <td>标题</td>
                <td>子标题</td>
                <td>关联疾病</td>
                <td>用户数</td>
                <td>课文数</td>
                <td>绑定量表</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php

            $inum = 0;
            $changecolor = true;
            $group_temp = "article";
            foreach ($courses as $a) {
                if ($group_temp == $a->groupstr) {
                    if ($changecolor) {
                        $changecolor = false;
                    } else {
                        $changecolor = true;
                    }
                }
                if ($changecolor) {
                    $changecolor = false;
                    ?>
                    <tr class="<?php echo $group_temp ?>" bgcolor="white">
                    <?php
                } else {
                    $changecolor = true;
                    ?>

                    <tr class="<?php echo $group_temp ?>" bgcolor="#d3d3d3">
                    <?php
                }
                $group_temp = $a->groupstr;
                $inum++;
                ?>
                <td><?= $inum ?></td>
                <td><?= $a->groupstr ?></td>
                <td><?= $a->getCreateDay() ?></td>
                <td><?= $a->tag->name ?></td>
                <td><?= $a->title ?></td>
                <td><?= $a->subtitle ?></td>
                <td>
                    <?php
                    $diseasecourserefs = DiseaseCourseRefDao::getListByCourseid($a->id);
                    foreach ($diseasecourserefs as $diseasecourseref) {
                        echo "{$diseasecourseref->disease->name} \n";
                    }
                    ?>
                </td>
                <td><?= $a->getUserCnt() ?></td>
                <td>
                    <a href="/lessonmgr/listofcourse?courseid=<?= $a->id ?>">课文(<?= $a->getLessonCnt() ?>)</a>
                </td>
                <td>
                    <?php if ($a->papertplid) { ?>
                        <a href="/papertplmgr/list?papertplid=<?= $a->papertplid ?>"> 修改量表</a>
                    <?php } else { ?>
                        <a href="/papertplmgr/add?courseid=<?= $a->id ?>" style="color: #009933">绑定量表</a>
                    <?php } ?>
                </td>
                <td>
                    <a href="/coursemgr/modify?courseid=<?= $a->id ?>">修改</a>
                </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
