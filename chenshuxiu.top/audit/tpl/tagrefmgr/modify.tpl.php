<!--没有对应的Action-->
<?php
$pagetitle = "TagRef修改";
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
            <form action="/tagrefmgr/modifypost" method="post">
                <input type="hidden" name="tagrefid" value="<?= $tagref->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width='100'>tagrefid</th>
                        <td><?= $tagref->id ?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $tagref->createtime ?></td>
                    </tr>
                    <tr>
                        <th>objtype</th>
                        <td><?= $tagref->objtype ?></td>
                    </tr>
                    <tr>
                        <th>目标(objid)</th>
                        <td><?= $tagref->objid ?></td>
                    </tr>
                    <tr>
                        <th>tagid</th>
                        <td><?= HtmlCtr::getSelectCtrImp(CtrHelper::getTagCtrArrayWithAll($typestr), "tagid",$tagref->tagid) ?></td>
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