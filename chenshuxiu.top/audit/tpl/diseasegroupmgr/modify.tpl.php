<?php
$pagetitle = "疾病分组修改";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/diseasegroupmgr/modifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <input type="hidden" name="diseasegroupid" value="<?= $diseasegroup->id ?>" />
                <tr>
                    <th width=200>疾病分组名</th>
                    <td>
                        <input type="text" name="name" value="<?= $diseasegroup->name?>" />
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
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
