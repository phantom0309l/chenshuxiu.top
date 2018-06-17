<?php
$pagetitle = "检查记录新增";
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
            <form action="/checkupmgr/addpost" method="post">
                <input type="hidden" name="patientid" value="<?=$patient->id ?>" />
                <div class="searchBar">手工录入检查记录</div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>患者</th>
                        <td>
                            <?=$patient->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>所属医生</th>
                        <td>
                            <?=$patient->doctor->name?>
                        </td>
                    </tr>
                    <tr>
                        <th>检查日期</th>
                        <td>
                            <input type="text" class="calendar" name="check_date" />
                        </td>
                    </tr>
                    <tr>
                        <th>检查医院</th>
                        <td>
                            <input type="text" name="hospitalstr" />
                        </td>
                    </tr>
                    <tr>
                        <th>检查结果</th>
                        <td>
                            <textarea name="content" cols=60 rows=6></textarea>
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
