<?php
$pagetitle = '方寸运营后台管理系统';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-6">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td>家长感悟 (Last 300)</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($comments as $a) {
                    ?>
                    <tr>
                        <td>
                            <span style="font-size: 12px; color: #999;"><?= $a->id ?></span>
                            <span style="font-size: 16px"><?= $a->title ?></span>
                            <span style="font-size: 12px; color: #999;">
                                <?php
                                if ($a->user instanceof User) {
                                    echo $a->user->getPatientNameOrShowName();
                                }
                                ?>
                            </span>
                            <span style="font-size: 12px; color: #999;"><?= $a->createtime ?></span>
                            <a href="/fbt/commentdeletepost?commentid=<?= $a->id ?>">删除</a>
                            <button type="button" class="comment-one" data-commentid="<?= $a->id ?>">编辑</button>
                            <div style="margin-top: 10px; padding: 10px; border: 1px solid #eee"><?= $a->content ?></div>
                        </td>
                    </tr>

                <?php } ?>
                <tr>
                    <td colspan=9>
                        <?php include $dtpl . "/pagelink.ctr.php"; ?>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </section>
        <section class="col-md-6 content-right">
            <?php foreach ($comments as $comment) { ?>
                <span class="ganwuffid">家长 <?= $comment->user->xcode ?>:</span>
                <div style="margin-left: 20px; margin-top: 5px; width: 90%;">
                    <span class="ganwudate"><?= $comment->getCreateDate() ?> <?= $comment->title ?> </span>
                    <p class="ganwucontent"><?= $comment->content ?></p>
                    <hr style="border: none; border-bottom: 1px solid #eeeeee; color: #eeeeee; margin-top: 20px; margin-bottom: 20px;"/>
                </div>
            <?php } ?>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    // 为echarts对象加载数据
    $(function () {
        $(document).on("click", '.comment-one', function () {
            var me = $(this);
            var commentid = me.data("commentid");
            $.ajax({
                "type": "get",
                "data": {commentid: commentid},
                "dataType": "html",
                "url": "/fbt/commentmodifyhtml",
                "success": function (data) {
                    $(".content-right").html(data);
                    $(".content-right").show();
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>