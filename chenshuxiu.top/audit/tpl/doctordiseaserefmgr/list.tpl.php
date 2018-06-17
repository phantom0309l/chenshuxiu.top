<?php
$pagetitle = "医生-疾病-关联-列表  Of ";
if ($doctor instanceof Doctor) {
    $pagetitle .= "医生 ( {$doctor->name} )";
} elseif ($disease instanceof Disease) {
    $pagetitle .= "疾病 ( {$disease->name} )";
} else {
    $pagetitle .= " ALL";
}
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="mt10 table table-bordered">
                <thead>
                    <tr>
                        <td width=40>#</td>
                        <td>医生</td>
                        <td>疾病</td>
                        <td>
                            稳定复诊周期
                            <br />
                            (天数)
                        </td>
                    </tr>
                </thead>
                <tbody>
        <?php
        foreach ($doctorDiseaseRefs as $i => $ref) {
            ?>
                    <tr>
                        <td><?=$i+1 ?></td>
                        <td>
                            <a href="/doctorDiseaseRefMgr/list?doctorid=<?=$ref->doctorid?>"><?=$ref->doctor->name ?></a>
                        </td>
                        <td>
                            <a href="/doctorDiseaseRefMgr/list?diseaseid=<?=$ref->diseaseid?>"><?=$ref->disease->name ?></a>
                        </td>
                        <td><?=$ref->visit_daycnt;?></td>
                    </tr>

        <?php } ?>
            </tbody>
            </table>
            </div>
            <?php if($doctor instanceof Doctor){ ?>
            <div class="p10 border1">
                <span><?=$doctor->name ?></span>
                <span class="f16 blue"> 关联疾病:</span>
                <form action="/doctorDiseaseRefMgr/bindDiseasePost" method="post">
                    <input type="hidden" name="doctorid" value="<?= $doctor->id ?>" />
                    <br /> <?= HtmlCtr::getCheckboxCtrImp(CtrHelper::getDiseaseCtrArray(false), "diseaseids[]", $doctor->getDiseaseIdArray(), ' '); ?>
                    <br />
                    <br />
                    <input class="btn btn-success" type="submit" value="修改关联疾病提交" />
                </form>
            </div>
            <div class="p10 border1">
                <span><?=$doctor->name ?></span>
                <span class="f16 blue">
                    已关联服务号:
                    <span class="red">(关联疾病后, 请关联服务号)</span>
                </span>
                <br />
                <br />
                <span>
                <?php
                foreach ($doctor->getDoctorWxShopRefs() as $doctorWxShopRef) {
                    echo $doctorWxShopRef->wxshop->name;
                    echo "<br/>";
                }
                ?>
                </span>
                <br />
                <a class="btn btn-success" href="/doctorWxShopRefMgr/list?doctorid=<?= $doctor->id ?>">修改关联服务号</a>
            </div>
            <?php } ?>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
