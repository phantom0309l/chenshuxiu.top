<?php
$pagetitle = "答卷列表 XAnswerSheet";
$cssFiles = [
    "{$img_uri}/v3/scale.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
        .scales {
            margin-top: 10px;
        }

        .btnShell {
            text-align: center;
            padding: 10px 0px;
        }

        .scales-details {
            color: #428bca;
            cursor: pointer;
        }

        .scales-details:hover {
            text-decoration: underline;
            cursor: pointer;
        }

        /*sheet*/
        .sheet-container {
            font-size: 16px;
            padding: 10px;
        }

        .sheet-input {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px;
            line-height: 120%;
            font-size: 14px;
        }

        .sheet-question-num {
            width: 25%;
        }

        .sheet-question-unit {
            color: #777;
        }

        .sheet-question-star {
            color: #f00;
        }

        .sheet-question-text {
            width: 95%;
        }

        .sheet-question-textarea {
            width: 95%;
            height: 100px;
        }

        .sheet-question-radioBox {
            float: left;
            margin: 0px 10px 5px 0px;
        }

        .sheetSubmitBtn {
            display: inline-block;
            height: 35px;
            line-height: 35px;
            padding: 0px 20px;
            border: 1px solid #09f;
            background: #09f;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            color: #fff;
        }

        .wh35 {
            width: 35%;
        }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <span>患者姓名：<?= $thepatient->name ?></span>
            <span>所属医生：<?= $thepatient->doctor->name; ?></span>
        </div>
        <input type="hidden" value="<?= $xquestionsheetid ?>" id="scale_type"/>
        <div class="searchBar">
            <?php if ($thepatient instanceof Patient) { ?>
                <a href="/xanswersheetmgr/list?patientid=<?= $thepatient->id ?>">全部列表</a>
                <?php
                foreach ($thepatient->getXQuestionSheetSumOfPatient() as $row) {
                    if ($row['xquestionsheetid'] != $xquestionsheetid) {
                        ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="qsheet_nav_one">
                <a href="/xanswersheetmgr/list2?xquestionsheetid=<?= $row['xquestionsheetid'] ?>&patientid=<?= $thepatient->id ?>"><?= $row['title'] ?>
                    (<?= $row['cnt'] ?>)</a>
            </span>
                        <?php
                    } else {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp; {$row['title']}({$row['cnt']})";
                    }
                }
            }
            ?>
        </div>
        <div class="scales">
            <?php
            foreach ($list as $aSheet) {
                $_obj = $aSheet->obj;
                $_paper = null;
                if ($_obj instanceof Paper) {
                    $_paper = $_obj;
                }
                ?>
                <div class='scales-item p10 border1 mt10'>
                    <div class='pb10'>
                    <span class="scales-details "> <?= $aSheet->createtime ?>
                        <?php
                        if ($aSheet->obj instanceof Paper) {
                            echo "<span class='blue'>{$aSheet->obj->papertpl->title }</span>";
                        } elseif ($aSheet->obj instanceof LessonUserRef) {
                            echo "<span class='blue'>{$aSheet->obj->lesson->title }</span>";
                        } else {
                            echo "<span class='blue'>{$aSheet->objtype} {$aSheet->objid}</span>";
                        }
                        ?>
                    </span>
                    </div>

                    <?php
                    if ($_paper instanceof Paper) {
                        if (in_array($_paper->ename, array(
                            'medicine_parent',
                            'adhd_iv'))) {
                            include $tpl . "/xanswersheetmgr/_{$_paper->ename}.php";
                        }
                    }
                    ?>
                    <div class='answersheet none' style="width: 750px;">
                        <div class="panel panel-primary">
                            <div class="panel-heading">患者的答卷</div>
                            <div class='details' class="panel-body">
                                <div class='sheet-container'>
                                    <?php

                                    foreach ($aSheet->getAnswers() as $xanswer) {
                                        // print_r($xanswer->getQuestionCtr());exit;
                                        if ($thepatient->notShowAdhd_ivOf26() && ($xanswer->xquestion->ename == "section_3" || $xanswer->xquestion->ename == "section_title3")) {
                                            continue;
                                        }

                                        $defaultHide = '';
                                        if ($xanswer->isDefaultHide()) {
                                            $defaultHide = 'style="display:none;"';
                                        }

                                        echo "<div class='scale-shell {$xanswer->xquestion->ename}' {$defaultHide}>";

                                        if ($_paper instanceof Paper && in_array($_paper->ename,
                                                array(
                                                    'adhd_iv',
                                                    'sideeffect',
                                                    'conners'))
                                        ) {
                                            echo $xanswer->getQuestionCtr()->getScaleHtml();
                                        } else {
                                            echo $xanswer->getQuestionCtr()->getQaHtml4paper();
                                        }

                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        $(".options").each(function () {
            $(this).find(".option").first().removeClass('option-center').addClass('option-left');
            $(this).find(".option").last().removeClass('option-center').addClass('option-right');
        });
        $(document).on('click', ".scales-details", function () {
            var answersheet = $(this).parents(".scales-item").find(".answersheet");
            if (answersheet.is(":visible")) {
                answersheet.hide();
                return false;
            }
            $(".answersheet").hide();
            answersheet.show();
            answersheet.find(".closeBtn").on('click', function () {
                answersheet.hide();
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
