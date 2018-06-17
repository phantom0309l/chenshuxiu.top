<?php
$pagetitle = "配置 Of 疾病[{$disease->name}]";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="page-header">
                        <h4>关联评估量表</h4>
                    </div>
                    <a class="btn btn-primary" target='_blank' href="/diseasepapertplrefmgr/list?diseaseid=<?=$disease->id?>">修改</a>
                    <div class="border1 p10">
                <?php foreach( $diseasepapertplrefs as $a){?>
                        <p><?=$a->papertpl->title ?></p>
                <?php }?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="page-header">
                        <h4>关联课程</h4>
                    </div>
                    <a class="btn btn-primary" target='_blank' href="/coursemgr/list">修改</a>
                    <div class="border1 p10">
                <?php foreach( $diseasecourserefs as $a){?>
                        <p><?=$a->course->title ?><?=$a->course->subtitle?></p>
                <?php }?>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
