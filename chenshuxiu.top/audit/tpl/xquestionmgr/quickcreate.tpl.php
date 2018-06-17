<?php
$pagetitle = "快速创建问卷, 支持选项相同的单选问题";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .width40 {
        width: 40px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <div class="contentShell">
            <section class="col-md-12">
                <form action="/xquestionmgr/quickaddpost" method="post">
                    <input type="hidden" name="xquestionsheetid" value="<?= $xquestionsheetid ?>" />
                    <p>放置问题以回车隔开,如果是section标题，前面请加#</p>
                    <p>
                        <textarea name="titles" rows="70" cols="120"></textarea>
                    </p>
                    <p>放置选项以『|』隔开。如：（未发生|没有|轻度|中度|重度|极重）</p>
                    <p>
                        <textarea name="optionstrs" rows="5" cols="120"></textarea>
                    </p>
                    <p>放置分数以『|』隔开。如：（1|2|3|4|5|6）</p>
                    <p>
                        <textarea name="scorestrs" rows="5" cols="120"></textarea>
                    </p>
                    <p>
                        <input type="submit" class="btn btn-success" value="提交" />
                    </p>
                </form>
            </section>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>