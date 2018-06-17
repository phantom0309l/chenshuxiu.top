<?php
$pagetitle = "修改定时任务变量模板";
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
            <div class="table-responsive">
                <table class="table table-bordered">
                <form action="/cronprocesstplvarmgr/modifypost">
                    <input type="hidden" name="cronprocesstplid" value="<?= $cronprocesstplvar->cronprocesstplid ?>">
                    <tr>
                        <th width=140>定时任务变量id</th>
                        <td>
                            <input type="text" readonly name="cronprocesstplvarid" value="<?= $cronprocesstplvar->id ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>变量名</th>
                        <td>
                            <input type="text" readonly name="code" value="<?= $cronprocesstplvar->code ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>变量说明</th>
                        <td>
                            <input type="text" name="name" value="<?= $cronprocesstplvar->name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>单位</th>
                        <td>
                            <input type="text" name="unit" value="<?= $cronprocesstplvar->unit ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>相关说明</th>
                        <td>
                            <textarea name="remark" cols="60" rows="5"><?= $cronprocesstplvar->remark ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </form>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>