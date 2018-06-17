<?php
$pagetitle = "定时任务新建";
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
            <form action="/crontabmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>脚本类名:</th>
                        <td>
                            <input type="text" style="width:50%;" name="process_name" />
                        </td>
                    </tr>
                    <tr>
                        <th>类型:</th>
                        <td>
                            <?= HtmlCtr::getRadioCtrImp(CronTab::getTypeDescArr(), 'type', '' ,'')?>
                        </td>
                    </tr>
                    <tr>
                        <th>执行时机:</th>
                        <td>
                            <input type="text" name="when" />
                        </td>
                    </tr>
                    <tr>
                        <th>脚本中文名:</th>
                        <td>
                            <input type="text" name="title" />
                        </td>
                    </tr>
                    <tr>
                        <th>脚本说明:</th>
                        <td>
                            <textarea name="content" cols="100" rows="10"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>脚本文件路径:</th>
                        <td>
                            <input type="text" style="width:100%;"name="filepath" />
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
