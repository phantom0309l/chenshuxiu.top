<?php
$plantimes = empty($configs['plantime']) ? [1] : $configs['plantime'];
$plantimestr = $configs['showstr']['plantimestr'] ?? ['今日任务'];

$plantimes_input = implode(',', $plantimes);
$plantimestr_input = implode(',', $plantimestr);
?>
<div class="form-group optaskfilter-list" data-key="plantime">
    <input type="hidden" id="plantime" name="plantime" value="<?=$plantimes_input?>">
    <input type="hidden" id="plantimestr" name="plantimestr" value="<?=$plantimestr_input?>">
    <label class="col-xs-3 control-label" for="plantimes">计划完成时间</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::toOptaskPlantimeCtrArrayForFilter(), 'plantimes', $plantimes, '');
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="plantimes"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var plantimeids = [];
                var plantimestr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="plantimes"]').each(function () {
                        var m = $(this);
                        if (m.val() != -2) {
                            plantimeids.push(m.val());
                            plantimestr.push(m.data('value'));
                            m.prop('checked', false);
                        }

                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                        }
                    });
                    if (checked_cnt == 0) {
                        $('input[name="plantimes"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                } else {
                    var checkbox_cnt = 0;
                    var checked_cnt = 0;
                    $('input[name="plantimes"]').each(function () {
                        var m = $(this);
                        checkbox_cnt ++;
                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                            if (m.val() != -2) {
                                plantimeids.push(m.val());
                                plantimestr.push(m.data('value'));
                            }
                        }

                        if (m.val() == -2) {
                            if (m.prop('checked') == true) {
                                m.prop('checked', false);
                                checked_cnt --;
                            }
                        }
                    });
                    console.log(checkbox_cnt, checked_cnt, "++++++++++++");
                    if (checkbox_cnt - checked_cnt == 1 || checked_cnt == 0) {
                        $('input[name="plantimes"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                }

                var plantimeidstr = plantimeids.join(',');
                var plantimestr = plantimestr.join(',');

                $('input[name="plantimes"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        plantimeidstr = '-2';
                        plantimestr = '全部';
                    }
                });

                $('#plantime').val(plantimeidstr);
                $('#plantimestr').val(plantimestr);
            });
        });
    </script>
</div>