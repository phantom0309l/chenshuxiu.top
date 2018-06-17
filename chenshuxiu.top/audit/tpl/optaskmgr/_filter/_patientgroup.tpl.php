<?php
$patientgroupids = empty($configs['patientgroup']) ? [-2] : $configs['patientgroup'];
$patientgroupstr = $configs['showstr']['patientgroupstr'] ?? ['全部'];

$patientgroupids_input = implode(',', $patientgroupids);
$patientgroupstr_input = implode(',', $patientgroupstr);
?>
<div class="form-group optaskfilter-list" data-key="patientgroup">
    <input type="hidden" id="patientgroup" name="patientgroup" value="<?=$patientgroupids_input?>">
    <input type="hidden" id="patientgroupstr" name="patientgroupstr" value="<?=$patientgroupstr_input?>">
    <label class="col-xs-3 control-label" for="patientgroupids">患者组</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::getPatientGroupsCtrArrayForFilter(false), 'patientgroupids', $patientgroupids, "");
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="patientgroupids"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var patientgroupids = [];
                var patientgroupstr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="patientgroupids"]').each(function () {
                        var m = $(this);
                        if (m.val() != -2) {
                            patientgroupids.push(m.val());
                            patientgroupstr.push(m.data('value'));
                            m.prop('checked', false);
                        }

                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                        }
                    });
                    if (checked_cnt == 0) {
                        $('input[name="patientgroupids"]').each(function () {
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
                    $('input[name="patientgroupids"]').each(function () {
                        var m = $(this);
                        checkbox_cnt ++;
                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                            if (m.val() != -2) {
                                patientgroupids.push(m.val());
                                patientgroupstr.push(m.data('value'));
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
                        $('input[name="patientgroupids"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                }

                var patientgroupidstr = patientgroupids.join(',');
                var patientgroupstr = patientgroupstr.join(',');

                $('input[name="patientgroupids"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        patientgroupidstr = '-2';
                        patientgroupstr = '全部';
                    }
                });

                $('#patientgroup').val(patientgroupidstr);
                $('#patientgroupstr').val(patientgroupstr);
            });
        });
    </script>
</div>