<div class="optaskOneShell">
    <?php
    $pmCheck = $optask->obj;
    if ($pmCheck instanceof PatientMedicineCheck && $pmCheck->status == 2) {
        $content = json_decode($pmCheck->content); ?>
        <div class="optaskContent">
            <?php if (!empty($content)) { ?>
                <table class="table remove-margin">
                    <tbody>
                    <tr>
                        <td style="width: 80px;">
                            用药核对
                        </td>
                        <td>
                            <?php
                            $drug = $content->drug;
                            if ($drug->confirm == 1) {
                                echo "<span class='text-primary'>确认无误</span>";
                            } else {
                                echo $drug->content;
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 80px;">
                            不良反应
                        </td>
                        <td>
                            <?php
                            $untoward_effects = $content->untoward_effects;
                            if (empty($untoward_effects)) {
                                echo "<span class='text-primary'>无</span>";
                            } else {
                                foreach ($untoward_effects as $untoward_effect) {
                                    echo "<p>{$untoward_effect->title}：{$untoward_effect->content}</p>";
                                }
                            }
                            ?>
                        </td>
                    </tr>
<!--                    <tr>-->
<!--                        <td style="width: 80px;">-->
<!--                            评估-->
<!--                        </td>-->
<!--                        <td>-->
<!--                            --><?php
//                            $evaluate = $content->evaluate;
//                            if (empty($evaluate)) {
//                                echo "<span class='text-primary'>未进行评估</span>";
//                            } else {
//                                echo "{$evaluate->thedate}：{$evaluate->content}";
//                            }
//                            ?>
<!--                        </td>-->
<!--                    </tr>-->
                    </tbody>
                </table>
            <?php } ?>
        </div>
    <?php } ?>
</div>
