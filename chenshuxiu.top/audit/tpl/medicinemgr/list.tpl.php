<?php
$pagetitle = "药品库 Medicines";
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
                <a class="btn btn-success" href="/medicinemgr/add">药品新建</a>
            </div>
            <div class="searchBar">
                <form action="/medicinemgr/list" method="get">
                    <div class="mt10">
                        <label for="medicine_name">按药名：</label>
                        <input id="medicine_name" name="medicine_name" value="<?= $medicine_name?>">
                    </div>
                    <div class="mt10">
                        <label for="groupstr">分组名：</label>
                        <input id="groupstr" name="groupstr" value="<?= $groupstr?>">
                        <a class="btn btn-success" href="/medicinemgr/grouplist">分组列表</a>
                    </div>
                    <div class="mt10">
                        <label>只显示官网展示：</label>
                        <input type="checkbox" id="isshow-yes" name="isshow" value="1" <?php if($isshow){ ?> checked <?php } ?>>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="查找">
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>
                            商品名/name
                            <br />
                            学名
                        </td>
                        <td>分组</td>
                        <td>单位</td>
                        <td>图片</td>
                        <td>内容</td>
                        <td>
                            官网
                            <br />
                            展现
                        </td>
                        <td>给药途径</td>
                        <td>用药时机</td>
                        <td>标准用法</td>
                        <td>剂量</td>
                        <td>频率</td>
                        <td>
                            调药
                            <br />
                            规则
                        </td>
                        <td>操作</td>
                        <td>关联疾病</td>
                    </tr>
                </thead>
                <tbody>
<?php
if (empty($medicines)) {
    echo "在此疾病下没有合适筛选条件的药物";
}
foreach ($medicines as $a) {
    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->name?>
                            <br />
                            <span class="gray"><?= $a->scientificname ?></span>
                        </td>
                        <td><?= $a->groupstr === '' ? '(未分类)' : $a->groupstr ?></td>
                        <td><?= $a->unit ?></td>
                        <td><?php if($a->picture){ ?><img src="<?=$a->picture->getSrc(40,40)?>"><?php } ?></td>
                        <td><?=$a->getSubContent() ?></td>
                        <td><?=XConst::$Bools[$a->isshow]?></td>
                        <td><?= empty($a->getArrDrug_way())?'':'...'; ?></td>
                        <td><?= empty($a->getArrDrug_timespan())?'':'...'; ?></td>
                        <td><?= empty($a->getArrDrug_std_dosage())?'':'...'; ?></td>
                        <td><?= empty($a->getArrDrug_dose())?'':'...'; ?></td>
                        <td><?= empty($a->getArrDrug_frequency())?'':'...'; ?></td>
                        <td><?= empty($a->getArrDrug_change())?'':'...'; ?> </td>
                        <td>
                            <a target="_blank" href="/medicinemgr/modify?medicineid=<?= $a->id ?>">改</a>
                        </td>
                        <td>
    <?php
    $refs = $a->getDiseaseMedicineRefs();
    ?>
                            <a target="_blank" href='/medicinemgr/dmrefadd?medicineid=<?=$a->id?>'>
                            关联<?= count($refs)?>
                            </a>
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
