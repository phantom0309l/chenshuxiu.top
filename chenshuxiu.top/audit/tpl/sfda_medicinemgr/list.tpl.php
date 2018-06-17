<?php
$pagetitle = "Sfda_medicine";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form action="/sfda_medicinemgr/list" method="get">
                    <div class="mt10">
                        <label>sfda_id:</label>
                        <input type="text" name="sfda_id" value="<?=$sfda_id ?>">
                        批准文号:
                        <input type="text" name="piwenhao" value="<?=$piwenhao ?>">
                        原批准文号:
                        <input type="text" name="piwenhao_old" value="<?=$piwenhao_old ?>">
                        药品本位码:
                        <input type="text" name="benweima" value="<?=$benweima ?>">
                        剂型:
                        <input type="text" name="type_jixing" value="<?=$type_jixing ?>">
                        产品类别:
                        <?= HtmlCtr::getSelectCtrImp(Sfda_medicine::type_chanpinJsonArray(), 'type_chanpin', $type_chanpin)?>
                        <input type="submit" class="btn btn-success" value='查找' />
                    </div>
                </form>
            </div>
            <div class="searchBar">
                <form action="/sfda_medicinemgr/list" method="get">
                    <div class="mt10">
                        <label>产品名(通用名)/产品英文名/商品名(品牌名)/生产单位:</label>
                        <input style="width: 240px" type="text" name="word" value="<?=$word ?>">
                        <input type="submit" class="btn btn-success" value='模糊搜索' />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered col-md-10">
                <thead>
                    <tr>
                        <td width=30>#</td>
                        <td>sfda_id</td>
                        <td>
                            批准日期
                            <br />
                            <span class="gray">截止日期</span>
                        </td>
                        <td>
                            批准文号
                            <br />
                            <span class="gray">原批准文号</span>
                        </td>
                        <td>
                            药品本位码
                            <br />
                            <span class="gray">本位码备注</span>
                        </td>
                        <td>
                            产品名(通用名)
                            <br />
                            name_common
                            <br />
                            name_common_en
                            <br />
                            <span class="gray">
                                商品名 (品牌名)
                                <br />
                                name_brand
                                <br />
                                name_brand_en
                            </span>
                        </td>
                        <td>
                            生产单位
                            <br />
                            company_name
                            <br />
                            company_name_en
                        </td>
                        <td>
                            剂型
                            <br />
                            type_jixing
                        </td>
                        <td>
                            产品类别
                            <br />
                            type_chanpin
                        </td>
                        <td>
                            单位规格
                            <br />
                            size_chengfen
                            <br />
                            <span class="gray">包装规格</span>
                            <br />
                            <span class="gray">size_pack</span>
                        </td>
                        <td>
                            进口
                            <br />
                            is_en
                        </td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $startRowNum = $pagelink->getStartRowNum();

                    foreach ($sfda_medicines as $i => $a) {
                        ?>
                    <tr>
                        <td><?= $startRowNum + $i ?></td>
                        <td><?= $a->sfda_id ?></td>
                        <td><?= $a->pizhun_date?>

                            <br />
                            <span class="gray"><?= $a->end_date?></span>
                        </td>
                        <td><?= $a->piwenhao?>
                            <span class="gray"><?= $a->piwenhao_old ?></span>
                        </td>
                        <td><?= $a->benweima?>
                        <?php
                        if ($a->benweima_remark) {
                            ?>
                           <br />
                            <span class="gray"><?= $a->benweima_remark ?></span>
                        <?php
                        }
                        ?>
                        </td>
                        <td>
                            <a href="/sfda_medicinemgr/list?word=<?= urlencode($a->name_common); ?>"><?= $a->name_common ?></a>
                            <br />
                            <?= $a->name_common_en?>
                            <br />
                            <a class="blue" href="/sfda_medicinemgr/list?word=<?= urlencode($a->name_brand); ?>"><?= $a->name_brand ?></a>
                            <br />
                            <span class="gray">
                            <?= $a->name_brand_en?>
                            </span>
                        </td>
                        <td>
                            <a href="/sfda_medicinemgr/list?word=<?= urlencode($a->company_name); ?>"><?= $a->company_name ?></a>
                            <br />
                            <?= $a->company_name_en?>
                        </td>
                        <td><?= $a->type_jixing ?></td>
                        <td><?= $a->type_chanpin ?></td>
                        <td><?= $a->size_chengfen?>
                            <br />
                            <span class="gray"><?= $a->size_pack ?></span>
                        </td>
                        <td><?= $a->is_en ? '进口':''; ?></td>
                        <td>
                            <a target="_blank" href="/medicineproductmgr/add?sfda_medicineid=<?= urlencode($a->id);?>">入新药品库</a>
                        </td>
                    </tr>
                            <?php
                    }
                    ?>
                    <tr>
                        <td colspan="100" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>