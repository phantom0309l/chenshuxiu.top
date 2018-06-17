<?php
$pagetitle = "量表列表 of " . $paperTpl->title;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
<?php
$xquestionsheet = $paperTpl->xquestionsheet;
$questions = $xquestionsheet->getQuestions();
?>
<div class="col-md-12">
    <section class="col-md-12" style="overflow-x: scroll;">
        <div class="table-responsive">
            <table class="table table-bordered tdcenter">
            <thead>
            <tr>
                <td>患者</td>
                <td>填表日期</td>
                <?php
                foreach ($questions as $i => $q) {
                    if ($q->isSection()) {
                        continue;
                    }

                    if ($q->isCaption()) {
                        continue;
                    }

                    if ($q->isMultText()) {
                        foreach ($q->getMultTitles() as $t) {
                            echo "<td>{$q->content}-{$t}</td>";
                        }

                    } else {
                        echo "<td>{$q->content}</td>";
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($papers as $a) {
                $xanswersheet = $a->xanswersheet;
                ?>
                <tr>
                    <td><?= $a->patient->name ?></td>
                    <td><?= $a->getCreateDay() ?></td>

                    <?php
                    foreach ($questions as $i => $q) {
                        if ($q->isSection()) {
                            continue;
                        }

                        if ($q->isCaption()) {
                            continue;
                        }

                        $xanswer = $xanswersheet->getAnswer($q->id);
                        // 有答案
                        if ($xanswer instanceof XAnswer) {
                            foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                                echo "<td>{$t}</td>";
                            }
                        } else {
                            if ($q->isMultText()) {
                                foreach ($q->getMultTitles() as $t) {
                                    echo "<td></td>";
                                }

                            } else {
                                echo "<td></td>";
                            }
                        }
                    }
                    ?>
                </tr>
            <?php } ?>
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
