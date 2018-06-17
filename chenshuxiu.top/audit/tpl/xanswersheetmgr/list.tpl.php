<?php
$pagetitle = "答卷列表 XAnswerSheet";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
        .qsheet_nav_one {
            width: 200px;
            line-height: 200%
        }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <span>患者姓名：<?= $thepatient->name ?></span>
            <span>所属医生：
                <?php
                if ($thepatient instanceof Patient) {
                    echo $thepatient->doctor->name;
                }
                ?>
                </span>
        </div>
        <div class="searchBar">
            <?php
            if ($thepatient instanceof Patient) {

                echo '<span>全部列表</span>';
                foreach ($thepatient->getXQuestionSheetSumOfPatient() as $row) {

                    if ($row['xquestionsheetid'] != $xquestionsheetid) {
                        ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="qsheet_nav_one">
                    <a href="/xanswersheetmgr/list2?xquestionsheetid=<?= $row['xquestionsheetid'] ?>&patientid=<?= $thepatient->id ?>"><?= $row['title'] ?>
                        (<?= $row['cnt'] ?>)</a>
                </span>
                        <?php
                    } else {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp; {$row['title']}({$row['cnt']})";
                    }
                }
            }
            ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td width=100>答卷ID</td>
                <td width=150>创建时间</td>
                <td>标题</td>
                <td width=100>objtype</td>
                <td width=100>objid</td>
                <td width=70>患者</td>
                <td width=130>作者</td>
                <td width=80>答案数</td>
                <td width=80>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($list as $a) {
                $xquestionsheet = $a->xquestionsheet;
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getCreateDayHi() ?></td>
                    <td><?= $xquestionsheet->title ?></td>
                    <td><?= $a->objtype ?></td>
                    <td><?= $a->objid ?></td>
                    <td><?= $a->patient->name ?> </td>
                    <td><?= $a->user->shipstr ?> : <?= $a->user->name ?></td>
                    <td><?= $a->getAnswerCnt() ?> / <?= $a->getQuestionCnt() ?>
                    </td>
                    <td>
                        <a target="_blank" href="/xanswersheetmgr/modify?xanswersheetid=<?= $a->id ?>">查看</a>
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
            var xanswersheetid = me.data("xanswersheetid");

            if (!confirm("确认删除该答卷吗?")) {
                reutrn;
            }

            var tr = me.parents("tr");
            $.ajax({
                "type": "get",
                "data": {
                    xanswersheetid: xanswersheetid
                },
                "dataType": "html",
                "url": "/xanswersheetmgr/deleteJson",
                "success": function (data) {
                    if (data == "success") {
                        tr.remove();
                        alert("删除成功");
                    } else if (data == "fail") {
                        alert("未知原因，删除失败");
                    }
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
