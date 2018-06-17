<?php
$pagetitle = "量表模板修改";
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
        <form action="/papertplmgr/modifypost" method="post">
            <input type="hidden" value="<?= $papertpl->id ?>" name="papertplid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>标题</th>
                    <td>
                        <input value="<?= $papertpl->title ?>" id="title" type="text" name="title" style="width: 60%;"/>
                    </td>
                </tr>
                <tr>
                    <th>groupstr</th>
                    <td>
                        <input value="<?= $papertpl->groupstr ?>" id="groupstr" type="text" name="groupstr"/>
                        (用来识别某一类量表)
                    </td>
                </tr>
                <tr>
                    <th>ename</th>
                    <td>
                        <input value="<?= $papertpl->ename ?>" id="ename" type="text" name="ename"/>
                    </td>
                </tr>
                <tr>
                    <th>简介</th>
                    <td>
                        <textarea id="brief" name="brief"
                                  style="width: 60%; height: 150px;"><?= $papertpl->brief ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td>
                        <textarea id="content" name="content"
                                  style="width: 60%; height: 300px;"><?= $papertpl->content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <input type="submit" value="修改量表"/>
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
