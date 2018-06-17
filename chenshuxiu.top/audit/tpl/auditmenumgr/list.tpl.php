<?php
$pagetitle = "后台菜单列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/auditmenumgr/add">菜单新建</a>
                <a class="btn btn-primary" href="/auditmenumgr/tree">树形展示</a>
            </div>
            <div class="searchBar">
                <form action="/auditmenumgr/list" method="get" class="pr">
                    <?php
                    $auditroledescarr = AuditRole::getDescArr();
                    echo HtmlCtr::getCheckboxCtrImp($auditroledescarr, 'auditroleids[]', $auditroleidarr, '');
                    ?>
                    <br />
                    <input class='btn btn-success' type="submit" value="提交" />
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>id</td>
                        <td>父级目录</td>
                        <td>页面名称/url</td>
                        <td>所关联的resource权限</td>
                        <td>被指向的resources</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <form action="/auditmenumgr/posmodifypost" method="post">
                        <input type="hidden" name="parentmenuid" value="<?= $parentmenuid ?>">
<?php
foreach ($auditmenus as $a) {
    ?>
                    <tr>
                            <td>
                                <input class="form-control" style="width: 60px" type="number" name="pos[<?=$a->id?>]" value="<?= $a->pos ?>" />
                            </td>
                            <td>
                            <?= $a->id; ?>
                        </td>
                            <td>
                                <a href="/auditmenumgr/list?parentmenuid=<?=$a->parentmenuid?>"><?= $a->parentmenuid ? $a->parentmenu->title : '----'; ?></a>
                            </td>
                            <td>
<?php
    if ($a->parentmenuid < 1) {
        ?>
<a href="/auditmenumgr/list?parentmenuid=<?=$a->id?>"><?= $a->title ?></a>
<?php
    } else {
        echo $a->title;
    }
    ?>
                                <br />
                                <a target="_blank" href="<?= $a->url; ?>">
                                    <span class="gray f12"><?= $a->url; ?></span>
                                </a>
                            </td>
                            <td>
                                        <?php if( $a->auditresource instanceof AuditResource ) {?>
                                            <span class="blue"><?= $a->auditresource->title;?></span>
                                <br />
                                <span class="gray f12">
                                            <?= $a->auditresource->getAuditRolesStr();?>
                                            </span>
                                        <?php }else{?>
                                            <p style="color: red;">需要绑定</p>
                                        <?php }?>
                        </td>
                            <td>
                                        <?php
    $auditresources = $a->getAuditResourceList();
    if (count($auditresources)) {
        ?>
                                            <?php foreach( $auditresources as $auditresource ){?>
                                                <?= $auditresource->title?>
                                                <br />
                                            <?php }?>
                                        <?php
    } elseif ($a->parentmenuid < 1) {
        echo '----';
    } else {
        ?>
                                            <a href="/auditmenumgr/deletepost?auditmenuid=<?=$a->id?>" class="btn btn-danger">可删除</a>
                                        <?php
    }
    ?>
                                    </td>
                            <td>
                                <a target="_blank" class="btn btn-default btn-xs" href="/auditmenumgr/modify?auditmenuid=<?= $a->id ?>"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                        <?php }  ?>
                    <tr>
                            <td colspan=20>
                                <input class="btn btn-success" type="submit" value="保存序号修改">
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
