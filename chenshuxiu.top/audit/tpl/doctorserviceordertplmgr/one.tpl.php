<?php
$pagetitle = "修改";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/doctorserviceordertplmgr/modifypost" method="post">
            <input type="hidden" value="<?= $doctorServiceOrderTpl->id ?>" name="doctorserviceordertplid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td>ename</td>
                    <td>
                        <span><?= $doctorServiceOrderTpl->ename ?></span>
                    </td>
                </tr>
                <tr>
                    <td>title</td>
                    <td>
                        <span><?= $doctorServiceOrderTpl->title ?></span>
                    </td>
                </tr>
                <tr>
                    <td>content</td>
                    <td>
                        <p><?= $doctorServiceOrderTpl->content ?></p>
                    </td>
                </tr>
                <tr>
                    <td>price</td>
                    <td>
                        <span><?= $doctorServiceOrderTpl->price ?></span>
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
