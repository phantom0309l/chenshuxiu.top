<?php
$pagetitle = "问卷列表 XQuestionSheet";
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
        <div class="searchBar">
            <a class="btn btn-success" href="/xquestionsheetmgr/add">问卷新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td width=100>id</td>
                <td width=120>sn</td>
                <td>title</td>
                <td width=50>objtype</td>
                <td width=100>objid</td>
                <td width=50>objcode</td>
                <td width=70>操作</td>
                <td>问题列表/问卷预览</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($list as $a) {
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->sn ?></td>
                    <td><?= $a->title ?></td>
                    <td><?= $a->objtype ?></td>
                    <td><?= $a->objid ?></td>
                    <td><?= $a->objcode ?></td>
                    <td>
                        <a href="/xquestionsheetmgr/modify?xquestionsheetid=<?= $a->id ?>">修改</a>
                        <!-- <a style="color:red" class="delete" data-xquestionsheetid="<?= $a->id ?>">删除</a> -->
                    </td>
                    <td>
                        <a target="_blank"
                           href="/xquestionmgr/list?xquestionsheetid=<?= $a->id ?>"><?= $a->getQuestionCnt(); ?>个问题</a>
                        <a target="_blank" href="/xquestionsheetmgr/one?xquestionsheetid=<?= $a->id ?>">预览</a>
                        <a target="_blank"
                           href="/xquestionsheetmgr/firstquestion?xquestionsheetid=<?= $a->id ?>">第一题</a>
                        <a target="_blank"
                           href="/xanswersheetmgr/list?xquestionsheetid=<?= $a->id ?>"><?= $a->getAnswerSheetCnt(); ?>
                            份答卷</a>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan=23>
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        $(".delete").on("click", function () {
            var me = $(this);
            var xquestionsheetid = me.data("xquestionsheetid");

            if (!confirm("确认删除该问卷吗?")) {
                reutrn;
            }

            var tr = me.parents("tr");
            $.ajax({
                "type": "get",
                "data": {
                    xquestionsheetid: xquestionsheetid
                },
                "dataType": "html",
                "url": "/xquestionsheetmgr/deleteJson",
                "success": function (data) {
                    if (data == "success") {
                        tr.remove();
                        alert("删除成功");
                    } else if (data == "fail") {
                        alert("该问卷还有答卷，必须先删除答卷才能删除问卷");
                    }
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
