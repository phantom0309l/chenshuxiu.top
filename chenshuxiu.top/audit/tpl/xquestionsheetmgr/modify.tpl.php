<?php
$pagetitle = "问卷修改";
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
        <form action="/xquestionsheetmgr/modifypost" method="post">
            <input type="hidden" name="xquestionsheetid" value="<?= $xquestionsheet->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>id</th>
                    <td><?= $xquestionsheet->id ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $xquestionsheet->createtime ?></td>
                </tr>
                <tr>
                    <th>标题</th>
                    <td>
                        <input style="width: 400px" type="text" name="title" value="<?= $xquestionsheet->title ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>sn</th>
                    <td>
                        <input type="text" name="sn" value="<?= $xquestionsheet->sn ?>"/>
                        唯一码 sn, 如果由各模块创建,可以为空
                    </td>
                </tr>
                <tr>
                    <th>objtype</th>
                    <td><?= $xquestionsheet->objtype ?></td>
                </tr>
                <tr>
                    <th>objid</th>
                    <td><?= $xquestionsheet->objid ?></td>
                </tr>
                <tr>
                    <th>objcode</th>
                    <td><?= $xquestionsheet->objcode ?></td>
                </tr>
                <tr>
                    <th>序号</th>
                    <td><?= HtmlCtr::getRadioCtrImp(XQuestionSheet::getIshideposDescArray(), 'ishidepos', $xquestionsheet->ishidepos, ' '); ?></td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" class="btn btn-success blue" value="提交"/>
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
