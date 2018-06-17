<?php
$pagetitle = "[问卷:{$xquestionsheet->id}] {$xquestionsheet->title} (编辑模式) XQuestions";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <div class="contentShell">
        <section class="col-md-12">
            <div class="searchBar">
                <a href="/xquestionsheetmgr/one?xquestionsheetid=<?= $xquestionsheet->id ?>">[ 预览模式 ]</a>
                <a target="_blank"
                   href="/xquestionsheetmgr/modify?xquestionsheetid=<?= $xquestionsheet->id ?>">[<?= $xquestionsheet->getIshideposDesc() ?>
                    序号,修改]</a>
                <a href="#add">[ 添加问题 ]</a>
            </div>
            <form action="/xquestionmgr/posmodifypost" method="post">
                <input type="hidden" name="xquestionsheetid" value="<?= $xquestionsheet->id ?>"/>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td width=40>序号</td>
                        <td width=100>类型</td>
                        <td>ename</td>
                        <td width="60%">content</td>
                        <td>units</td>
                        <td>精简?</td>
                        <td>子问题?</td>
                        <td>必填?</td>
                        <td>操作</td>
                    </tr>
                    </thead>
                    <?php
                    foreach ($list as $a) {
                        ?>
                        <tr>
                            <td>
                                <input type="text" class="width40" name="pos[<?= $a->id ?>]" value="<?= $a->pos ?>"/>
                            </td>
                            <td><?= $a->getTypeDesc() ?></td>
                            <td><?= $a->ename ?></td>
                            <td><?= $a->content ?>
                                <?php if ($a->isSingleChoice()) { ?>
                                    <br/>
                                    <br/> 备选项: <?= HtmlCtr::getRadioCtrImp($a->getOptionArrayHasScore4HtmlCtr(), 'rightoptionid', $a->rightoptionid, ' '); ?>
                                <?php } elseif ($a->isMultChoice()) { ?>
                                    <br/>
                                    <br/> 备选项: <?= HtmlCtr::getCheckboxCtrImp($a->getOptionArray4HtmlCtr(), 'rightoptionid', array(), ' '); ?>
                                <?php } ?>
                                <div class="tipbox"><?= $a->tip ?></div>
                                父问题:<?= $a->_getParentQuestionIdEname() ?>

                            </td>
                            <td><?= $a->units ?></td>
                            <td><?= $a->getIsSimpleDesc() ?></td>
                            <td><?= $a->getIsSubDesc() ?></td>
                            <td><?= $a->getIsMustDesc() ?></td>
                            <td>
                                <a target="_blank" href="/xquestionmgr/modify?xquestionid=<?= $a->id ?>">修改</a>
                                &nbsp;
                                <a target="_blank" href="/xquestionmgr/preview?xquestionid=<?= $a->id ?>">预览</a>
                                &nbsp;
                                <a class="delete" data-xquestionid="<?= $a->id ?>">删除(<?= $a->getCntOfXanswer(); ?>
                                    个答案)</a>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan=20>
                            <input type="submit" class="btn btn-success" value="保存序号修改"/>
                            提交序号修改后,会先按序号调整顺序,然后生成新的序号.
                        </td>
                    </tr>
                </table>
                </div>
            </form>

            <?php include $tpl . "/xquestionmgr/_add.php"; ?>

        </section>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        $(".delete").on("click", function () {
            var me = $(this);
            var xquestionid = me.data("xquestionid");

            var tr = me.parents("tr");
            $.ajax({
                "type": "get",
                "data": {
                    xquestionid: xquestionid
                },
                "dataType": "html",
                "url": "/xquestionmgr/deleteJson",
                "success": function (data) {
                    if (data == "success") {
                        tr.remove();
                        alert("删除成功");
                    } else if (data == "fail") {
                        alert("不能删除有答案的问题");
                    }
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
