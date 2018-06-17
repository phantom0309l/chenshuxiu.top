<div class="btn-group">
    <?php
        $arr = [
            'treat' => '治疗',
            'checkup' => '检查'
        ];
        foreach ($arr as $type_en => $type_cn) {
            if ($typestr == $type_en) {
                $bedtktconfig_check = BedTktConfigDao::getByDoctoridType($doctor->id, $type_en);
                if ($bedtktconfig_check instanceof BedTktConfig) {
                ?>
                    <a class="btn btn-success" style="width:150px;" href="/bedtktconfigmgr/<?=$type_en?>modify?bedtktconfigid=<?=$bedtktconfig_check->id?>"><?= $type_cn ?></a>
                <?php
                }else{
                    ?>
                        <a class="btn btn-success" style="width:150px;" href="/bedtktconfigmgr/<?=$type_en?>add?doctorid=<?=$doctor->id?>&typestr=<?=$type_en?>"><?= $type_cn ?></a>
                    <?php
                }
            } else {
                $bedtktconfig_check = BedTktConfigDao::getByDoctoridType($doctor->id, $type_en);
                if ($bedtktconfig_check instanceof BedTktConfig) {
                ?>
                    <a class="btn btn-default" style="width:150px;" href="/bedtktconfigmgr/<?=$type_en?>modify?bedtktconfigid=<?=$bedtktconfig_check->id?>"><?= $type_cn ?></a>
                <?php
                }else{
                    ?>
                        <a class="btn btn-default" style="width:150px;" href="/bedtktconfigmgr/<?=$type_en?>add?doctorid=<?=$doctor->id?>&typestr=<?=$type_en?>"><?= $type_cn ?></a>
                    <?php
                }
            }
        }
    ?>
</div>
