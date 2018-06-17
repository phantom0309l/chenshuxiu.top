<?php
$pagetitle = "医生用药方案新建";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <form action="/doctormedicinepkgmgr/addpost" method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <tr>
                            <th width=140>医生:</th>
                            <td>
                                <div class="col-xs-2">
                                    <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>疾病:</th>
                            <td>
                                <?= $mydisease->name ?>
                            </td>
                        </tr>
                        <tr>
                            <th>方案名:</th>
                            <td>
                                <input name="name"/>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="submit" class="submit" value="提交"/>
                            </td>
                        </tr>
                    </table>
                    </div>
                </form>
    </section>
</div>
<div class="clear"></div>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
