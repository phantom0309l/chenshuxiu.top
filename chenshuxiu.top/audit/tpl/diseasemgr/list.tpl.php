<?php
$pagetitle = "疾病列表 Disease";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <a class="btn btn-success" href="/diseasemgr/add">疾病新建</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" style="border-top: 1px solid #ccc; margin-top: 10px;">
            <thead>
                <tr>
                    <td>diseaseid</td>
                    <td>创建日期</td>
                    <td>疾病名</td>
                    <td>code</td>
                    <td>疾病分组</td>
                    <td>服务号</td>
                    <td>医生</td>
                    <td>药品</td>
                    <td>量表</td>
                    <td width=90>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($diseases as $a) {?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= $a->getCreateDay() ?></td>
                    <td><?= $a->name ?></td>
                    <td><?= $a->code ?></td>
                    <td>
                        <a href="/diseasemgr/list?diseasegroupid=<?=$a->diseasegroupid ?>"><?= $a->diseasegroup->name ?></a>
                    </td>
                    <td>
                    <?php
                    $wxshop = $a->getWxShop();
                    if ($wxshop instanceof WxShop) {
                        echo $wxshop->name;
                    } else {
                        echo "--";
                    }
                    ?>
                    </td>
                    <td>
                        <a href='/doctormgr/list?diseaseid=<?=$a->id ?>'>关联医生
                    <?php
                    $doctors = $a->getDoctors();
                    echo count($doctors);
                    ?>
                        </a>
                    </td>
                    <td>
                        <a href='/diseasemedicinerefmgr/list?diseaseid=<?=$a->id ?>'>关联药品
                    <?php
                    $refs = $a->getDiseaseMedicineRefs();
                    echo count($refs);
                    ?>
                        </a>
                    </td>
                    <td>
                        <a href='/diseasepapertplrefmgr/list?diseaseid=<?=$a->id ?>'>关联量表
                    <?php
                    $papertpls = $a->getPaperTpls();
                    echo count($papertpls);
                    ?>
                        </a>
                    </td>
                    <td>
                        <a target="_blank" href="/diseasemgr/modify?diseaseid=<?= $a->id ?>">疾病修改</a>
                        <br />
                        <a target="_blank" href="/diseasemgr/config?diseaseid=<?= $a->id ?>">疾病配置</a>
                        <br />
                        <a target="_blank" href="/msgtemplatemgr/list?diseaseid=<?= $a->id ?>">消息模板</a>
                    </td>
                </tr>
                    <?php } ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $("a.showMore").on("click",function(){
    $(this).siblings("p").toggle();
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
