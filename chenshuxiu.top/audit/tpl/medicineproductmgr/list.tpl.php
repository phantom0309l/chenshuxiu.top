<?php
$pagetitle = "药物商品库 MedicineProducts";
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
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>
                            通用名
                            <br />
                            <span class="gray">商品名/化学名</span>
                        </td>
                        <td>图片</td>
                        <td>
                            给药途径 drug_way
                            <br />
                            单次服药剂量 drug_dose
                            <br />
                            用药频率 drug_frequency
                            <br />
                            包装单位 pack_unit
                        </td>
                        <td>
                            生产单位
                            <br />
                            company_name
                            <br />
                            company_name_en
                        </td>
                        <td>
                            批准文号
                            <br />
                            piwenhao
                            <br />
                            <span class="gray">
                                批准日期
                                <br />
                                pizhun_date
                            </span>
                        </td>
                        <td>
                            剂型 type_jixing
                            <br />
                            产品类别 type_chanpin
                        </td>
                        <td>
                            单位规格 size_chengfen
                            <br />
                            包装规格 size_pack
                        </td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            if (empty($medicineProducts)) {
                echo "没有药物";
            }
            foreach ($medicineProducts as $a) {
                ?>
                    <tr>
                        <td>
                <?= $a->id ?>
                <?php
                if ($a->is_tongbu_chufang_system) {
                    ?>
                            <span class="green">已同步</span>
                            <a href="/medicineproductmgr/mark_untongbuJson?medicineproductid=<?= $a->id ?>">未</a>
                <?php
                } else {
                    ?>
                            <span class="red">未同步</span>
                            <a href="/medicineproductmgr/mark_tongbuJson?medicineproductid=<?= $a->id ?>">已</a>
                <?php } ?>
                        </td>
                        <td><?= $a->name_common?>
                        <br />
                            <span class="gray"><?= $a->name_brand ?></span>
                            <br />
                            <span class="blue"><?= $a->name_chem ?></span>
                        </td>
                        <td>
                            <?php if($a->picture instanceof Picture){ ?>
                            <img src="<?=$a->picture->getSrc(100,100) ?>">
                            <?php } ?>
                        </td>
                        <td>
                            <?= $a->drug_way ?><br />
                            <?= $a->drug_dose ?><br />
                            <?= $a->drug_frequency ?><br />
                            <?= $a->pack_unit?>
                        </td>
                        <td><?= $a->company_name ?><br />
                            <span class="gray"><?= $a->company_name_en ?></span>
                        </td>
                        <td>
                            <?= $a->piwenhao?><br />
                            <span class="gray"><?= $a->pizhun_date?></span>
                        </td>
                        <td>
                            <?= $a->type_jixing ?><br />
                            <?= $a->type_chanpin?>
                        </td>
                        <td>
                            <?= $a->size_chengfen ?><br />
                            <?= $a->size_pack?>
                        </td>
                        <td>
                            <a target="_blank" href="/medicineproductmgr/modify?medicineproductid=<?= $a->id ?>">修改</a>
                            <br />
                            <?php if($a->isInShop()){ ?>
                                <a target="_blank" href="/shopproductmgr/add?objtype=MedicineProduct&objid=<?= $a->id ?>">已入商城</a>
                            <?php }else { ?>
                                <a target="_blank" href="/shopproductmgr/add?objtype=MedicineProduct&objid=<?= $a->id ?>">入驻商城</a>
                    <?php
                }
                ?>
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
