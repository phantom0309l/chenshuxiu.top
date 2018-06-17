<?php if( count($medicines) > 0 ){ ?>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <td>选择</td>
                <td>药品id</td>
                <td>药名</td>
                <td>学名</td>
                <td>单位</td>
            </tr>
        </thead>
    <?php
    foreach ($medicines as $i => $a) {
    ?>
        <tr>
            <td class="text-center">
                <input type="radio" name="medicineChoice" class="medicineChoice" data-medicineid="<?= $a->id ?>" data-unit="<?= $a->unit ?>" data-medicinename="<?= $a->name ?>"/>
            </td>
            <td>
                <?= $a->id ?>
            </td>
            <td>
                <?= $a->name ?>
            </td>
            <td>
                <?= $a->scientificname ?>
            </td>
            <td>
                <?= $a->unit ?>
            </td>
        </tr>
    <?php } ?>
    </table>
</div>
<?php }else{ ?>
    <div>没有匹配药品</div>
<?php } ?>
