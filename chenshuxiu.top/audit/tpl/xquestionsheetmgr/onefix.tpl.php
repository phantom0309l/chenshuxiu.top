<?php
$pagetitle = "[问卷:{$xquestionsheet->id }] {$xquestionsheet->title } (预览模式)";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-10">
            <form action="/xanswersheetmgr/answerspostfix" method="post">
                userid
                <input type="text" name="userid" value="" />
                readtime
                <input type="text" name="readtime" value="" />
                xquestionsheetid
                <input type="text" name="xquestionsheetid" value="<?=$xquestionsheet->id ?>" />
<?php
foreach ($xquestionsheet->getQuestions() as $a) {
    ?>
                <div>

    <?php
    echo $a->getHtml();
    ?>
                </div>
                <div style="clear: both"></div>
<?php
}
?>
                <div>
                    <input type="submit" class="sheet-question-subit" value="提交答卷" />
                </div>
                <br />
                <br />
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
