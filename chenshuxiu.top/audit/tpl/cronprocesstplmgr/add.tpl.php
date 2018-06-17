<?php
$pagetitle = "添加定时任务模板";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-10">
            <div class="contentBoxTitle">
                <h3>定时任务模板基本信息</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <form action="/cronprocesstplmgr/addpost">
                    <tr>
                        <th width=140>定时任务类名</th>
                        <td>
                            <input type="text" name="tasktype" />
                        </td>
                    </tr>
                    <tr>
                        <th>标题</th>
                        <td>
                            <input type="text" name="title" />
                        </td>
                    </tr>
                    <tr>
                        <th>分组</th>
                        <td>
                            <input type="text" name="groupstr" />
                        </td>
                    </tr>
                    <tr>
                        <th>定时任务描述</th>
                        <td>
                            <textarea name="content" cols="60" rows="5"></textarea>
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