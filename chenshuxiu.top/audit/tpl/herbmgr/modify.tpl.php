<?php
$pagetitle = "修改中药药材";
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
            <form action="/herbmgr/modifypost" method="post">
                <input type="hidden" name="herbid" value="<?= $herb->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>id</th>
                        <td><?= $herb->id ?></td>
                    </tr>
                    <tr>
                        <th>药材名字</th>
                        <td>
                            <input type="text" name="name" value="<?= $herb->name ?>" style="width: 300px" />
                        </td>
                    </tr>
                    <tr>
                        <th>拼音</th>
                        <td>
                            <input type="text" name="pinyin" value="<?= $herb->pinyin ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>拼音首字母</th>
                        <td>
                            <input type="text" name="py" value="<?= $herb->py ?>" />
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