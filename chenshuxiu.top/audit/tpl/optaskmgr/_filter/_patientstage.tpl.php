<?php
$patientstageids = empty($configs['patientstage']) ? [-2] : $configs['patientstage'];
$patientstagestr = $configs['showstr']['patientstagestr'] ?? ['全部'];

$patientstageids_input = implode(',', $patientstageids);
$patientstagestr_input = implode(',', $patientstagestr);
?>
<div class="form-group optaskfilter-list" data-key="patientstage">
    <input type="hidden" id="patientstage" name="patientstage" value="<?=$patientstageids_input?>">
    <input type="hidden" id="patientstagestr" name="patientstagestr" value="<?=$patientstagestr_input?>">
    <label class="col-xs-3 control-label" for="patientstageids">患者阶段</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::getPatientStagesCtrArrayForFilter(false), 'patientstageids', $patientstageids, "");
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="patientstageids"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var patientstageids = [];
                var patientstagestr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="patientstageids"]').each(function () {
                        var m = $(this);
                        if (m.val() != -2) {
                            patientstageids.push(m.val());
                            patientstagestr.push(m.data('value'));
                            m.prop('checked', false);
                        }

                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                        }
                    });
                    if (checked_cnt == 0) {
                        $('input[name="patientstageids"]').each(function () {
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
                    $('input[name="patientstageids"]').each(function () {
                        var m = $(this);
                        checkbox_cnt ++;
                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                            if (m.val() != -2) {
                                patientstageids.push(m.val());
                                patientstagestr.push(m.data('value'));
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
                        $('input[name="patientstageids"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                }

                var patientstageidstr = patientstageids.join(',');
                var patientstagestr = patientstagestr.join(',');

                $('input[name="patientstageids"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        patientstageidstr = '-2';
                        patientstagestr = '全部';
                    }
                });

                $('#patientstage').val(patientstageidstr);
                $('#patientstagestr').val(patientstagestr);
            });
        });
    </script>
</div>