<?php
$pagetitle = "患者月度留存及服药患者留存";
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
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td>报到月份</td>
                    <td>当月报到人数</td>
                    <?php for($i=2; $i<14; $i++){?>
                        <td>第<?=$i?>月活跃人数</td>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                    <?php if(count($items) > 0){
                        foreach($items as $item){
                            $baodaodate = $item["baodaodate"];
                            ?>
                            <tr>
                                <td>
                                    <?=$baodaodate?>
                                </td>
                                <?php for($i=1; $i<14; $i++){?>
                                    <?php
                                    $baodaomonth_thismonth_offsetcnt = XDateTime::getDateDiffOfMonth(date("Y-m-d", strtotime($baodaodate)), date("Y-m-d", time()));
                                    if($baodaomonth_thismonth_offsetcnt >= $i){ ?>
                                        <td><?=$item["column_".$i]?></td>
                                    <?php } ?>
                                <?php } ?>
                            </tr>
                    <?php
                        }
                    }?>
                </tbody>
            </table>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td>报到月份</td>
                    <td>当月服药人数</td>
                    <?php for($i=2; $i<14; $i++){?>
                        <td>第<?=$i?>月服药人数</td>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php if(count($drug_items) > 0){
                    foreach($drug_items as $drug_item){
                        $baodaodate = $drug_item["baodaodate"];
                        ?>
                        <tr>
                            <td>
                                <?=$baodaodate?>
                            </td>
                            <?php for($i=1; $i<14; $i++){?>
                                <?php
                                $baodaomonth_thismonth_offsetcnt = XDateTime::getDateDiffOfMonth(date("Y-m-d", strtotime($baodaodate)), date("Y-m-d", time()));
                                if($baodaomonth_thismonth_offsetcnt >= $i){ ?>
                                    <td><?=$drug_item["column_".$i]?></td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                }?>
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