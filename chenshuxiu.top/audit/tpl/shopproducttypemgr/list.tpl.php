<?php
$pagetitle = "商品类别 ShopProductTypes";
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
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                <tr>
                    <td width="100">id</td>
                    <td width="100">序号</td>
                    <td>疾病组</td>
                    <td>名称</td>
                    <td width="100">操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($shopProductTypes as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->pos ?></td>
                        <td><?= $a->diseasegroup ? $a->diseasegroup->name : '不分组' ?></td>
                        <td><?= $a->name ?></td>
                        <td>
                            <a href="/shopproducttypemgr/modify?shopproducttypeid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <form action="/shopproducttypemgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="100">新增类别:</th>
                        <td>
                            <input type="text" name="name" value=""/>
                            <div style="width: 154px; display: inline-block;"><?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(false), "diseasegroupid", 0, "form-control"); ?></div>
                            <input type="submit" class="btn btn-success" value="提交"/>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
