<?php
$pagetitle = "电话商品 CallProducts";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <a class="btn btn-success" href="/callproductmgr/add">新建商品</a>
        <div class="p10 m10">
            筛选:
            <p>
                <a href="/callproductmgr/list?status=1">在线</a>
                <a href="/callproductmgr/list?status=0">下线</a>
            </p>
        </div>
        <form action="/callproductmgr/posmodifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered col-md-10">
                    <thead>
                    <tr>
                        <td width="40">序号</td>
                        <td width="90">id</td>
                        <td width="220">objtype objid</td>
                        <td>标题</td>
                        <td width="90">
                            单价
                            <br/>
                            元
                        </td>
                        <td width="90">
                            市场价
                            <br/>
                            元
                        </td>
                        <td width="40">包装单位</td>
                        <td width="60">状态</td>
                        <td width="100">操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($callProducts as $a) { ?>
                        <tr>
                            <td>
                                <input class="form-control" style="width: 60px" type="number" name="pos[<?= $a->id ?>]"
                                       value="<?= $a->pos ?>"/>
                            </td>
                            <td><?= $a->id ?></td>
                            <td><?= $a->objtype ?> <?= $a->objid ?></td>
                            <td><?= $a->title ?></td>
                            <td align="right"><?= $a->getPrice_yuan() ?></td>
                            <td align="right"><?= $a->getMarket_price_yuan() ?></td>
                            <td align="center"><?= $a->pack_unit ?></td>
                            <td align="right"><?= $a->getStatusDesc() ?></td>
                            <td>
                                <a target="_blank" href="/callproductmgr/one?callproductid=<?= $a->id ?>">详情</a>
                                <a target="_blank" href="/callproductmgr/modify?callproductid=<?= $a->id ?>">修改</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <input class="btn btn-success" type="submit" value="保存序号修改">
        </form>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
