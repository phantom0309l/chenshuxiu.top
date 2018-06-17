<?php
$pagetitle = "[问卷:{$xquestionsheet->id }] {$xquestionsheet->title } (预览模式)";
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
            <div class="searchBar">
                <a href="/xquestionmgr/list?xquestionsheetid=<?= $xquestionsheet->id ?>">[编辑模式]</a>
                <a target="_blank"
                   href="/xquestionsheetmgr/modify?xquestionsheetid=<?= $xquestionsheet->id ?>">[<?= $xquestionsheet->getIshideposDesc() ?>
                    序号,修改]</a>
                <a href="#add">[添加问题]</a>
            </div>
            <form action="/xanswersheetmgr/answerspost" method="post">
                <input type="hidden" id="xquestionsheet-title" value="<?= $xquestionsheet->title ?>">
                <?php
                foreach ($xquestionsheet->getQuestions($issimple) as $a) {
                    $defaultHide = '';
                    if ($a->isDefaultHide()) {
                        $defaultHide = 'style="display:none;"';
                    }
                    ?>
                    <div class='questionDiv <?= $a->ename ?>' <?= $defaultHide ?>>
                        <div style="float: right; padding: 10px;">
                            <a target="_blank" href="/xquestionmgr/modify?xquestionid=<?= $a->id ?>">编辑</a>
                        </div>
                        <?php echo $a->getHtml(); ?>
                    </div>
                    <div style="clear: both"></div>
                    <?php
                }
                ?>
                <div>
                    <input type="submit" class="sheet-question-subit" value="提交答卷"/>
                </div>
            </form>
            <p style="margin-top:20px;"><a class="btn btn-primary"
                                           href="/xquestionmgr/quickcreate?xquestionsheetid=<?= $xquestionsheet->id ?>">快速创建选项一致的单选问题问卷</a>
            </p>
            <?php include $tpl . "/xquestionmgr/_add.php"; ?>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    var showHideOptionJsonStr = {$xquestionsheet->getShowHideOptionJsonStr()};

    $(function () {
        cym.init('.date-ym', 1940, 2020);
        xwenda.provinceCity.init();

        $(".sheetSubmitBtn").on("click", function () {
            xwenda.resetHideInputs();
            $("#form").submit();
        });

        var title = $("#xquestionsheet-title").val();
        if (title == 'NRS2002营养筛查表') {
            var radio_bmi;

            var inputs = [];
            var count = 0;
            $(".sheet-question-content").each(function (index, el) {
                // BMI < 20.5 的单选框设置不可选
                if (index == 2 || $(this).text() == 'BMI < 20.5? ') {
                    $(this).parent().children(".sheet-question-radio").prop("disabled", true);

                    radio_bmi = $(this).parent();
                }

                // 保存，体重，身高的input
                if (index == 0 || index == 1) {
                    var name = $(this).parent().children('div').children('div:first').children('input').attr('name');
                    inputs[count++] = name;
                }
            });

            $(".sheet-input").on("change", function () {
                var name = $(this).attr('name');
                if ($.inArray(name, inputs) == -1) {
                    return false;
                }

                var count = 0;
                var arr = [];
                $(".sheet-input").each(function () {
                    var v = $(this).val();
                    if (v != '') {
                        arr[count] = v;

                        count++;
                    }
                });

                if (count == 2) {
                    if (arr[0] > 0 && arr[1] > 0) {
                        var height = arr[0];
                        var weight = arr[1];

                        var bmi = weight / (height * height);
                        var vv = Math.pow(10, 1);
                        var bmi = Math.round(bmi * vv) / vv;

                        var count = 0;
                        var radios = [];

                        radio_bmi.children(".sheet-question-radio").each(function () {
                            radios[count] = $(this).attr("id");
                            count++;
                        });

                        if (bmi < 20.5) {
                            $("#" + radios[0]).prop("checked", true);
                        } else {
                            $("#" + radios[1]).prop("checked", true);
                        }
                    }
                }
            });
        }
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
