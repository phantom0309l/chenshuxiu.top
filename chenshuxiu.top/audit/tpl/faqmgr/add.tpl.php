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
        <form action="/faqmgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td>问题</td>
                    <td>
                        <textarea rows="5" cols="120" name="title"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>答案</td>
                    <td>
                        <textarea rows="10" cols="120" name="content"></textarea>
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
