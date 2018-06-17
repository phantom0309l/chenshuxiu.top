<div class="optaskOneShell">
    <?php $patient = $optask->patient; ?>
    <?php if($patient instanceof Patient){ ?>
        <?php
        $patientname = $patient->name;
        $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
        include $tpl . "/_pagetitle.php"; ?>
        <?php
        $patientpgroupref = $optask->obj->patientpgroupref;
        $course = $patientpgroupref->pgroup->course;
        $lesson = $optask->obj->obj;

        $courselessonref = CourseLessonRefDao::getByCourseAndLesson($course->id, $lesson->id);
        ?>
        <div>课文名：<?=$lesson->title?>（第<?=$courselessonref->pos?>课）</div>
        <div style="border: 1px dashed #777;" class="optaskContent"><?= $optask->content ?></div>
        <a target="_blank" class="btn-fixed-bottom" href='/optaskmgr/listforshow?optasktplid=<?= $optask->optasktplid ?>'>查看全部课程交流</a>
    <?php } ?>
</div>
