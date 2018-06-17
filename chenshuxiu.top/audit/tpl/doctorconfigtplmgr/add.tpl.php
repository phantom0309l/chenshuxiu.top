<?php
$pagetitle = "医生配置模板新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/doctorconfigtplmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th >配置标题</th>
                        <td>
                            <input name="title" />
                        </td>
                    </tr>
                    <tr>
                        <th>编码用的code</th>
                        <td>
                            <input name="code" />
                        </td>
                    </tr>
                    <tr>
                        <th>所属组标题</th>
                        <td>
                            <input name="groupstr" />
                        </td>
                    </tr>
                    <tr>
                        <th>简介</th>
                        <td>
                            <textarea name="brief" style="width: 80%; height: 100px;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
