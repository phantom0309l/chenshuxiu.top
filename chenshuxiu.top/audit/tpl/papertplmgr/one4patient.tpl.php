<?php
$pagetitle = "量表填写";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v3/js/xwenda.js",
]; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar blue">
            患者: <?= $patient->name ?>
        </div>
        <div class="searchBar blue">
            量表: <?= $papertpl->title ?>
        </div>
        <form id="form" action="/papermgr/addpost" method="post">
            <input type="hidden" name="patientid" value="<?= $patient->id ?>"/>
            <input type="hidden" name="papertplid" value="<?= $papertpl->id ?>"/>
            <?php
            foreach ($xquestionsheet->getQuestions() as $a) {
                $defaultHide = '';
                if ($a->isDefaultHide()) {
                    $defaultHide = 'style="display:none;"';
                }
                ?>
                <div class='questionDiv <?= $a->ename ?>' <?= $defaultHide ?>>
                    <?php echo $a->getHtml(); ?>
                </div>
                <div style="clear: both"></div>
                <?php
            }
            ?>
            <div>
                <input type="button" class="sheetSubmitBtn" value="提交答卷"/>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        xwenda.subClick("havesub");

        function resetHideInputs() {
            $("input:hidden").each(function () {
                var me = $(this);
                if (me.attr("type") !== "hidden") {
                    if (me.attr("type") == "radio") {
                        me.attr("checked", "");
                    } else {
                        me.val("");
                    }
                }
            });

            $("textarea:hidden").each(function () {
                $(this).val("");
            });

            $("checkbox:hidden").each(function () {
                $(this).attr("checked", "");
            });
        }

        $(".sheetSubmitBtn").on("click", function () {
            resetHideInputs();
            $("#form").submit();
        });

        laydate.render({
            elem: ".date-ymd"
        });

        laydate.render({
            elem: ".date-ym"
        });

        laydate.render({
            elem: ".date-y"
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
