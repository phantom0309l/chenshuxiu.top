<?php
$pagetitle = "图片归档模板新建";
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
        <form action="/picturedatasheettplmgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>所属疾病:</th>
                    <td>
                        <?= $mydisease->name ?>
                    </td>
                </tr>
                <tr>
                    <th>编码:</th>
                    <td>
                        <input type="text" name="ename"/>
                    </td>
                </tr>
                <tr>
                    <th>问题标题:</th>
                    <td>
                        <input type="text" name="title"/>
                    </td>
                </tr>
                <tr>
                    <th>问题标题列表:</th>
                    <td>
                        <textarea name="questiontitles" cols="50" rows="10"></textarea>
                        每行一个问题，回车区分
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="提交"/>
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
