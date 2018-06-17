<?php
$pagetitle = "新建";
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
        <form action="/optaskcheckmgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td>optaskchecktplid</td>
                    <td>
                        <input type="text" name="optaskchecktplid" value="" />
                    </td>
                </tr>
                <tr>
                    <td>xanswersheetid</td>
                    <td>
                        <input type="text" name="xanswersheetid" value="" />
                    </td>
                </tr>
                <tr>
                    <td>auditor_id</td>
                    <td>
                        <input type="text" name="auditor_id" value="" />
                    </td>
                </tr>
                <tr>
                    <td>optask_id</td>
                    <td>
                        <input type="text" name="optask_id" value="" />
                    </td>
                </tr>
                <tr>
                    <td>checked_auditor_id</td>
                    <td>
                        <input type="text" name="checked_auditor_id" value="" />
                    </td>
                </tr>
                <tr>
                    <td>checked_time</td>
                    <td>
                        <input type="text" name="checked_time" value="" />
                    </td>
                </tr>
                <tr>
                    <td>status</td>
                    <td>
                        <input type="text" name="status" value="" />
                    </td>
                </tr>
                <tr>
                    <td>woy</td>
                    <td>
                        <input type="text" name="woy" value="" />
                    </td>
                </tr>
                <tr>
                    <td>remark</td>
                    <td>
                        <input type="text" name="remark" value="" />
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
