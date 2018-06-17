<?php
$pagetitle = "答卷修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/static/js/areadata.js",
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
            <div class="mt10">
                <div>
                    <a class='btn btn-success'
                       href="/xanswersheetmgr/print?xanswersheetid=<?= $xanswersheet->id ?>">精简打印格式</a>
                </div>
            </div>
            <form action="/xanswersheetmgr/modifypost" method="post">
                <input type="hidden" name="xanswersheetid" value="<?= $xanswersheet->id ?>"/>
                <?php
                foreach ($xanswersheet->getAnswers() as $a) {
                    $defaultHide = '';
                    if ($a->isDefaultHide()) {
                        $defaultHide = 'style="display:none;"';
                    }
                    ?>
                    <div class='questionDiv sheet-question-box <?= $a->xquestion->ename ?> delete-<?= $a->id; ?>' <?= $defaultHide ?>>
                        <?php echo $a->getHtml(); ?>
                        <a class="delete btn btn-success" data-xanswerid="<?= $a->id; ?>">删除</a>
                    </div>
                    <?php
                }
                ?>
                <div>
                    <input type="submit" class="sheet-question-subit" value="提交答卷"/>
                </div>
                <br/>
                <br/>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function () {
        cym.init('.date-ym', 1940, 2020);
        xwenda.subClick("havesub");
        xwenda.provinceCity.init();
        xwenda.provinceCity.show();

        $(".sheetSubmitBtn").on("click", function () {
            xwenda.resetHideInputs();
            $("#form").submit();
        });

        $(".delete").on("click", function () {
            var me = $(this);
            var xanswerid = me.data("xanswerid");

            if (!confirm("确认删除该答案吗？")) {
                return;
            }

            $.ajax({
                "type": "get",
                "data": {
                    xanswerid: xanswerid
                },
                "dataType": "html",
                "url": "/xanswersheetmgr/deleteXanswerJson",
                "success": function (data) {
                    if (data == "success") {
                        $(".delete-" + xanswerid).remove();
                        alert("删除成功");
                    } else {
                        alert("未知原因，删除失败");
                    }
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
