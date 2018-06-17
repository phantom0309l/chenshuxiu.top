<?php
$pagetitle = "检查报告模板修改";
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

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<div class="col-md-12">
        <section class="col-md-12">
        <form action="/checkuptplmgr/modifypost" method="post">
                <input type="hidden" name="checkuptplid" value="<?=$checkuptpl->id?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width=140>医生:</th>
                        <td>
                            <?php $doctorid = $checkuptpl->doctorid; ?>
                            <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>检查分组:</th>
                        <td>
                            <input name="groupstr" value="<?=$checkuptpl->groupstr ?>">
                        </td>
                    </tr>
                    <tr>
                        <th width=140>ename:</th>
                        <td>
                            <input name="ename" value="<?=$checkuptpl->ename ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>title:</th>
                        <td>
                            <input name="title" value="<?=$checkuptpl->title ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>是否显示
                            在约复诊中:</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(array(0=>'不显示',1=>'显示'), 'is_in_tkt', $checkuptpl->is_in_tkt,'') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否有问卷:</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(array(0=>'没有',1=>'有'), 'is_in_admin', $checkuptpl->is_in_admin,'') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否在约复诊中默认被选中:</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(array(0=>'不是',1=>'是'), 'is_selected', $checkuptpl->is_selected,'') ?>
                        </td>
                    </tr>
                    <tr>
                        <th>摘要:</th>
                        <td>
                            <textarea rows="4" cols="40" name="brief"><?=$checkuptpl->brief ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>内容:</th>
                        <td>
                            <textarea name="content" rows="4" cols="40"><?=$checkuptpl->content ?></textarea>
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