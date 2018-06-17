<?php
$pagetitle = "量表模板新建";
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
        <form action="/papertplmgr/addpost" method="post">
            <input id="courseid" type="hidden" name="courseid" value="<?= $courseid ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width="140">标题:</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 60%;"/>
                    </td>
                </tr>
                <tr>
                    <th>groupstr:</th>
                    <td>
                        <input id="groupstr" type="text" name="groupstr"/>
                        (用来识别某一类量表)
                    </td>
                </tr>
                <tr>
                    <th>ename:</th>
                    <td>
                        <input id="ename" type="text" name="ename"/>
                    </td>
                </tr>
                <tr>
                    <th>量表疾病关系设置:</th>
                    <td>
                        <h5>疾病：</h5>
                        <div>
                        <?php foreach (CtrHelper::getDiseaseCtrArray(false) as $id => $name) { ?>
                            <label class="css-input css-checkbox css-checkbox-success">
                                <input type="checkbox" name="diseaseids[]" value="<?= $id ?>" />
                                <span></span><?= $name ?>
                            </label>
                        <?php } ?>
                        </div>
                        <h5 class="mt10 mb10">显示：</h5>
                        <div>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin">
                                <input type="checkbox" name="show_in_wx"><span></span>患者可见
                            </label>
                            <label class="css-input css-checkbox css-checkbox-primary remove-margin">
                                <input type="checkbox" name="show_in_audit" checked ><span></span>运营可见
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>简介:</th>
                    <td>
                        <textarea id="brief" name="brief" style="width: 60%; height: 150px;"></textarea>
                    </td>
                </tr>
                <tr>
                    <th>内容:</th>
                    <td>
                        <textarea id="content" name="content" style="width: 60%; height: 300px;"></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <input type="submit" value="添加量表"/>
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
