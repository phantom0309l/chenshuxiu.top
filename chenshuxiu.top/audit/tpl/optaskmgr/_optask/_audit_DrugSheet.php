<div class="optaskOneShell">
<?php
$patient = $optask->patient;
$drugsheet = $optask->obj;
$pagetitle = "{$patient->name}{$optask->optasktpl->title}详情";
include $tpl . "/_pagetitle.php"; ?>
<div class="optaskOneShell">
    <?php if ($drugsheet instanceof DrugSheet) {
        $drugitems = $drugsheet->getDrugItems();
    ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>类型</th>
                    <th>行为日期</th>
                    <th>药名</th>
                    <th>剂量</th>
                    <th>备注</th>
                </tr>
            </thead>
            <tbody class="tc">
                <?php foreach($drugitems as $a){
                    if( false == $a instanceof DrugItem ){
                        continue;
                    }
                ?>
                <tr>
                    <td><?= $a->getTypeDesc() ?></td>
                    <td><?= $a->record_date ?></td>
                    <td><?= $a->medicine->name ?></td>
                    <td><?= $a->value ?></td>
                    <td><?= $a->content ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if($drugsheet->remark){ ?>
            <div>其他用药信息：<?= $drugsheet->remark ?></div>
        <?php } ?>
        <a class="btn btn-default" target="_blank" href="/drugsheetmgr/updatedrugitems?drugsheetid=<?= $drugsheet->id ?>">去更改用药</a>
    <?php } ?>
</div>
</div>
