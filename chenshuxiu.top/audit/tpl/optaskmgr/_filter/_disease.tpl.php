<?php
$diseaseids = empty($configs['disease']) ? [-2] : $configs['disease'];
$diseasestr = $configs['showstr']['diseasestr'] ?? ['全部'];

$diseaseids_input = implode(',', $diseaseids);
$diseasestr_input = implode(',', $diseasestr);
?>
<div class="form-group optaskfilter-list" data-key="disease">
    <input type="hidden" id="disease" name="disease" value="<?=$diseaseids_input?>">
    <input type="hidden" id="diseasestr" name="diseasestr" value="<?=$diseasestr_input?>">
    <label class="col-xs-3 control-label" for="diseaseids">疾病</label>
    <div class="col-xs-9">
        <?php
        echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::getDiseaseCtrArrayForFilter(), 'diseaseids', $diseaseids, "");
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="diseaseids"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var diseaseids = [];
                var diseasestr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="diseaseids"]').each(function () {
                        var m = $(this);
                        if (m.val() != -2) {
                            diseaseids.push(m.val());
                            diseasestr.push(m.data('value'));
                            m.prop('checked', false);
                        }

                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                        }
                    });
                    if (checked_cnt == 0) {
                        $('input[name="diseaseids"]').each(function () {
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
                    $('input[name="diseaseids"]').each(function () {
                        var m = $(this);
                        checkbox_cnt ++;
                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                            if (m.val() != -2) {
                                diseaseids.push(m.val());
                                diseasestr.push(m.data('value'));
                            }
                        }

                        if (m.val() == -2) {
                            if (m.prop('checked') == true) {
                                m.prop('checked', false);
                                checked_cnt --;
                            }
                        }
                    });
                    if (checkbox_cnt - checked_cnt == 1 || checked_cnt == 0) {
                        $('input[name="diseaseids"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                }

                var diseaseidstr = diseaseids.join(',');
                var diseasestr = diseasestr.join(',');

                $('input[name="diseaseids"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        diseaseidstr = '-2';
                        diseasestr = '全部';
                    }
                });

                $('#disease').val(diseaseidstr);
                $('#diseasestr').val(diseasestr);
            });
        });
    </script>
</div>