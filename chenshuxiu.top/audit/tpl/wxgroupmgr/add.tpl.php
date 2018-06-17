<?php
$pagetitle = "新建 wxgroups";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/wxgroupmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td width=140>wxshopid</td>
                        <td>
                            <input id="wxshopid" type="text" name="wxshopid" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <td>groupid</td>
                        <td>
                            <input id="groupid" type="text" name="groupid" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <td>ename</td>
                        <td>
                            <input id="ename" type="text" name="ename" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>
                            <input id="name" type="text" name="name" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>content</th>
                        <td>
                            <textarea id="content" name="content" cols="100" rows="8"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" value="添加" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>