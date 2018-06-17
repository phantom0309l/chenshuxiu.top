<?php
$pagetitle = "{$xanswersheet->patient->name} [得分 {$xanswersheet->score}]";
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
        <?php
        $i = 0;
        foreach ($xanswersheet->getAnswers() as $a) {
            $i++;

            $defaultHide = '';
            if ($a->isDefaultHide()) {
                $defaultHide = 'style="display:none;"';
            }

            $xansweroptionref = XAnswerOptionRef::getOneByXAnswer($a);
            if (false == $xansweroptionref instanceof XAnswerOptionRef) {
                ?>
                <div class='questionDiv sheet-question-box <?= $a->xquestion->ename ?> delete-<?= $a->id; ?>' <?= $defaultHide ?>>
                    <?php echo $a->getHtml(); ?>
                </div>
                <?php
            } else {
                ?>
                <div class='questionDiv sheet-question-box <?= $a->xquestion->ename ?> delete-<?= $a->id; ?>' <?= $defaultHide ?>>
                    <?php echo $a->xquestion->content; ?><br>
                    <?php echo $xansweroptionref->xoption->content; ?>
                </div>
                <?php
            }
        }
        ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
