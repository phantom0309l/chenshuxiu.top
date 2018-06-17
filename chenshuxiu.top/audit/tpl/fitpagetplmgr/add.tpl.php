<?php
$pagetitle = "模板添加 ";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
                <form method="post" action="/fitpagetplmgr/addpost">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <tr>
                            <th width="90">code</th>
                            <td>
                                <input name="code">
                            </td>
                        </tr>
                        <tr>
                            <th>name</th>
                            <td>
                                <input name="name">
                            </td>
                        </tr>
                        <tr>
                            <th>remark</th>
                            <td>
                                <textarea name="remark" rows="10" cols="50"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="submit" value="提交">
                            </td>
                        </tr>
                    </table>
                    </div>
                </form>
    </section>
</div>
<div class="clear"></div>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
