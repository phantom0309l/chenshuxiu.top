<?php
$pagetitle = "按医生汇总 药品数";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
        .div10 {
            margin-bottom: 10px
        }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
                <input type="hidden" name="doctorid" value="<?= $doctorid ?>">
        <div class="table-responsive">
            <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td>医生</td>
                        <td>关联药品数</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($doctors as $a) {
                        ?>
                        <tr>
                            <td><?= $a->name ?></td>
                            <td>
                                <a target="_blank"
                                   href="/doctormedicinerefmgr/list?doctorid=<?= $a->id ?>"><?= $a->getDoctorMedicineRefCnt() ?></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
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
