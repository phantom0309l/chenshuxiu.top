<?php
$pagetitle = "标签关系列表 TagRefs";
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
            <div class="searchBar">

<?php

foreach (Tag::getTypeStrDefines(true) as $k => $v) {
    if ($k == $typestr) {
        $_class = 'btn btn-success';
    } else {
        $_class = 'tab-btn-highlight';
    }
    ?>
    		<a class="<?=$_class ?>" href="/tagrefmgr/list?objtype=<?=$objtype ?>&objid=<?=$objid ?>&typestr=<?= $k ?>"><?= $k ?>:<?= $v ?></a>
        <?php
}
?>

<?php
if ($objtype && $objid > 0) {
    ?>
               &nbsp;	| <a class="btn btn-success" href="/tagrefmgr/add?objtype=<?=$objtype ?>&objid=<?=$objid ?>&typestr=<?=$typestr ?>">新增标签关系</a>
            	   <?php

}
?>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <?php if($objtype == 'Patient'){ ?>
                        <td>患者</td>
                        <?php }else{ ?>
                        <td>id</td>
                        <td>objtype</td>
                        <td>objid</td>
                        <?php } ?>
                        <td>标签分组</td>
                        <td>标签</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
<?php
foreach ($tagrefs as $a) {
    ?>
                    <tr>
                        <?php
                            if($objtype == 'Patient'){
                                if ($a->obj instanceof Patient) {
                                    ?>
                                        <td><?= $a->obj->name ?></td>
                                    <?php
                                }
                            } else {
                                ?>
                                    <td><?= $a->id ?></td>
                                    <td><?= $a->objtype ?></td>
                                    <td><?= $a->objid ?></td>
                                <?php
                            }
                        ?>
                        <td><?= Tag::getTypeStrDefine($a->tag->typestr) ?></td>
                        <td><?= $a->tag->name ?></td>
                        <td>
                            <a href="/tagrefmgr/deletepost?tagrefid=<?=$a->id ?>&objtype=<?=$objtype ?>&objid=<?=$objid ?>&typestr=<?=$typestr ?>">删除</a>
                        </td>
                    </tr>
<?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
