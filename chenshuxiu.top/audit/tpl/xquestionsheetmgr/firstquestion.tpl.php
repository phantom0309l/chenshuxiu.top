<?php
$pagetitle = "第一题 of {$xquestionsheet->title}";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-10">
            <form action="/xanswersheetmgr/firstanswerpost" method="post">
                <div>
<?php
echo $xquestion->getHtml();
?>
                </div>
                <div>
                    <input type="submit" class="sheet-question-subit" value="提交答卷" />
                </div>
                <br />
                <br />
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>