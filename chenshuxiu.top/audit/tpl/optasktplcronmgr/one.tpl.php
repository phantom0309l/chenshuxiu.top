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
        <form action="/optasktplcronmgr/modifypost" method="post">
            <input type="hidden" value="<?= $opTaskTplCron->id ?>" name="optasktplcronid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td>optasktplid</td>
                    <td>
                        <input type="text" name="optasktplid" value="<?= $opTaskTplCron->optasktplid ?>" />
                    </td>
                </tr>
                <tr>
                    <td>step</td>
                    <td>
                        <input type="text" name="step" value="<?= $opTaskTplCron->step ?>" />
                    </td>
                </tr>
                <tr>
                    <td>send_content</td>
                    <td>
                        <input type="text" name="send_content" value="<?= $opTaskTplCron->send_content ?>" />
                    </td>
                </tr>
                <tr>
                    <td>dealwith_type</td>
                    <td>
                        <input type="text" name="dealwith_type" value="<?= $opTaskTplCron->dealwith_type ?>" />
                    </td>
                </tr>
                <tr>
                    <td>follow_daycnt</td>
                    <td>
                        <input type="text" name="follow_daycnt" value="<?= $opTaskTplCron->follow_daycnt ?>" />
                    </td>
                </tr>
                
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" class="btn btn-success" value="提交" />
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
