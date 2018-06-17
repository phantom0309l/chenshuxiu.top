<?php
$pagetitle = "服务号列表 WxShops";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/wxshopmgr/add">服务号新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
                <tr>
                    <th width=100>id</th>
                    <th>名称</th>
                    <th>短名称</th>
                    <th>主关联疾病</th>
                    <th>gh</th>
                    <th>wx_email</th>
                    <th>下次认证</th>
                    <th>管理员</th>
                    <th>运营者</th>
                    <th>开放平台</th>
                    <th>修改菜单</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($wxshops as $a) {
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->name ?></td>
                    <td><?= $a->shortname ?></td>
                    <td><?= $a->disease->name ?></td>
                    <td><?= $a->gh ?></td>
                    <td><?= $a->wx_email ?></td>
                    <td><?= $a->getNext_cert_dateFix() ?></td>
                    <td><?= $a->admin_name ?></td>
                    <td><?= $a->oper_names ?></td>
                    <td><?= $a->open_email ?></td>
                    <td>
                        <a href="/wxshopmgr/wxmenu?wxshopid=<?=$a->id ?>">菜单</a>
                    </td>
                    <td>
                        <a href="/wxshopmgr/modify?wxshopid=<?=$a->id ?>">修改</a>
                        <a href="/doctorwxshoprefmgr/list?wxshopid=<?=$a->id ?>">医生</a>
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
