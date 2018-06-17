<?php
$pagetitle = "常用词列表 CommonWords";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a target="_blank" class="btn btn-success" href="/commonwordmgr/add">常用词新建</a>
                <a target="_blank" class="btn btn-success" href="/commonwordmgr/multiadd">批量新增常用词</a>
            </div>
            <form action="/commonwordmgr/list" method="get" class="pr">
                <div class="searchBar">
                    <div class="mt10">
                        <label>医生: </label>
                        <?php echo HtmlCtr::getSelectCtrImp( CtrHelper::getDoctorCtrArray($mydisease->id),"doctorid",$doctorid,"f18"); ?>
            	    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value=" 组合筛选 ">
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>所属医生</th>
                        <th>内容分类</th>
                        <th>类型用途</th>
                        <th>分组</th>
                        <th>内容</th>
                        <th>权重</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($commonwords as $a) {
                        ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td>
                                <?= $a->getDoctorDesc()?>
                        </td>
                        <td>
                            <?php if( $a->ownertype == 'PatientRemarkTpl'){?>
                                <?= $a->owner->name?>
                            <?php }?>
                        </td>
                        <td>
                            <?= $a->getTypestrDesc()?>
                        </td>
                        <td>
                            <?= $a->groupstr?>
                        </td>
                        <td><?= $a->content; ?></td>
                        <td><?= $a->weight; ?></td>
                        <td>
                            <a target="_blank" class="btn btn-xs btn-primary" href="/commonwordmgr/modify?commonwordid=<?=$a->id?>">修改</a>
                            <a class="btn btn-xs btn-danger" href="/commonwordmgr/deletepost?commonwordid=<?=$a->id?>">删除</a>
                        </td>
                    </tr>
                        <?php } ?>
                    <tr>
                        <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
