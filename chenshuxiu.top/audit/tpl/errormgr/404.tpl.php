<?php
$pagetitle = "404";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .pagetail {
        color: #ccc;
        text-align: center;
        font-size: 13px;
        margin-bottom: 6px;
        margin-top: 24px;
    }

    .msg {
        text-align: center;
        margin-top: 24px;
        margin-bottom: 100px;
        color: #777
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <h3 id="pgtitle">404</h3>
            <hr class="pgtline">
            <div id="topcmt">
                <p class="msg">页面被病毒偷走了</p>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>