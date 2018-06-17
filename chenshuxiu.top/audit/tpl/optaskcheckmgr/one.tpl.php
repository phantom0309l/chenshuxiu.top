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
        <form action="/optaskcheckmgr/modifypost" method="post">
            <input type="hidden" value="<?= $opTaskCheck->id ?>" name="optaskcheckid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td>optaskchecktplid</td>
                    <td>
                        <input type="text" name="optaskchecktplid" value="<?= $opTaskCheck->optaskchecktplid ?>" />
                    </td>
                </tr>
                <tr>
                    <td>xanswersheetid</td>
                    <td>
                        <input type="text" name="xanswersheetid" value="<?= $opTaskCheck->xanswersheetid ?>" />
                    </td>
                </tr>
                <tr>
                    <td>auditor_id</td>
                    <td>
                        <input type="text" name="auditor_id" value="<?= $opTaskCheck->auditor_id ?>" />
                    </td>
                </tr>
                <tr>
                    <td>optask_id</td>
                    <td>
                        <input type="text" name="optask_id" value="<?= $opTaskCheck->optask_id ?>" />
                    </td>
                </tr>
                <tr>
                    <td>checked_auditor_id</td>
                    <td>
                        <input type="text" name="checked_auditor_id" value="<?= $opTaskCheck->checked_auditor_id ?>" />
                    </td>
                </tr>
                <tr>
                    <td>checked_time</td>
                    <td>
                        <input type="text" name="checked_time" value="<?= $opTaskCheck->checked_time ?>" />
                    </td>
                </tr>
                <tr>
                    <td>status</td>
                    <td>
                        <input type="text" name="status" value="<?= $opTaskCheck->status ?>" />
                    </td>
                </tr>
                <tr>
                    <td>woy</td>
                    <td>
                        <input type="text" name="woy" value="<?= $opTaskCheck->woy ?>" />
                    </td>
                </tr>
                <tr>
                    <td>remark</td>
                    <td>
                        <input type="text" name="remark" value="<?= $opTaskCheck->remark ?>" />
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
