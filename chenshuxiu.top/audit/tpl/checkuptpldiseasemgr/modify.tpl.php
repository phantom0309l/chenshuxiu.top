<?php
$pagetitle = "检查报告模板修改";
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
        <form action="/checkuptpldiseasemgr/modifypost" method="post">
                <input type="hidden" name="checkuptplid" value="<?=$checkuptpl->id?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width=140>检查分组:</th>
                        <td>
                            <input name="groupstr" value="<?=$checkuptpl->groupstr ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>title:</th>
                        <td>
                            <input name="title" value="<?=$checkuptpl->title ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>ename:</th>
                        <td>
                            <input name="ename" value="<?=$checkuptpl->ename ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>摘要:</th>
                        <td>
                            <textarea rows="4" cols="40" name="brief"><?=$checkuptpl->brief ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>内容:</th>
                        <td>
                            <textarea name="content" rows="4" cols="40"><?=$checkuptpl->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
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