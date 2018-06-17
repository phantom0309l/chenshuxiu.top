<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    $pmsideeffect = $optask->obj;
    include $tpl . "/_pagetitle.php"; ?>
    <div class="optaskContent">
        <p>
            <?= $pmsideeffect->getResultDesc()?>
        </p>
        <div class="mt10" style="float:right;">
            <div class="btn btn-success pmsideeffectAudit"  data-resultstatus="1" data-resultdesc="资料正确" data-pmsideeffectid="<?= $pmsideeffect->id ?>">
                资料正确
            </div>
            <div class="btn btn-success pmsideeffectAudit" data-resultstatus='2' data-resultdesc='资料不正确' data-pmsideeffectid="<?= $pmsideeffect->id ?>">
                资料不正确
            </div>
            <div class="btn btn-success pmsideeffectAudit" data-resultstatus='3' data-resultdesc='没有上传' data-pmsideeffectid="<?= $pmsideeffect->id ?>">
                没有上传
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
