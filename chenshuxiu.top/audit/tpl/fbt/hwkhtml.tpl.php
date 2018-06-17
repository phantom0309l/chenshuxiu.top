<div class="col-md-12">
    <h3><?= $courseuserref->patient->name ?> 作业详情</h3>
    写作业天数:<?= $writeHwkDays ?>
    加入课程天数:<?= $studydays ?>
    <br/>
    <br/>
    <div class="col-md-2 need" style="height: 500px; border: 1px solid #ccc;">
        <?php
        foreach ($hwklessonuserrefs as $date => $a) {
            ?>
            <p class="date-item" style="color: cadetblue;"><?= $date ?></p>
        <?php } ?>
    </div>
    <div class="col-md-10">
        <?php
        foreach ($comments as $a) {
            ?>
            <div class="content-firstshow">
                <p style="color: blue"><?= $a->title ?></p>
                <p><?= $a->content ?></p>
            </div>

            <?php
        }
        foreach ($hwklessonuserrefs as $date => $a) {
            ?>
            <div class="content-item-sub none">
                <p style="color: saddlebrown;"><?= $date ?></p>
                <?php
                $lesson_title = "";
                foreach ($a as $b) {
                    ?>
                    <?PHP

                    if ($lesson_title == "") {
                        $lesson_title = $b->lesson->title;
                        ?>
                        <p style="color: blue;"><?= $lesson_title ?></p>
                        <?php
                    } elseif ($lesson_title != $b->lesson->title) {
                        $lesson_title = $b->lesson->title;
                        ?>
                        <p style="color: blue;"><?= $lesson_title ?></p>
                        <?php
                    }
                    $i = 0;
                    foreach ($_answers = $b->getHwkAnswerSheet()->getAnswers() as $c) {
                        echo $c->getQuestionCtr()->getQaHtml4lesson();
                        if ($c->xquestion->isFbtHwkJinbuQuestion()) {
                            echo '<button style="color:red;" type="button" class="answer-one"
                                data-answerid="' . $c->id . '">加入感悟</button>';
                        }
                    }

                }
                ?>
            </div>
        <?php } ?>
    </div>
</div>
<style type="text/css">
    .need {
        overflow: auto;
    }
</style>
<script>
    $(document).ready(function () {
        var cTags = $(".date-item");
        var cItems = $(".content-item-sub");
        var cItemOneShows = $(".content-firstshow");

        $(".date-item").on("click", function () {
            var me = $(this);
            cTags.css("color", "cadetblue");
            me.css("color", "black");
            var index = me.index();
            cItems.hide();
            cItemOneShows.hide();
            cItems.eq(index).show();
        });
    });
</script>
