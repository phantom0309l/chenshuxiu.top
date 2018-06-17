<?php
$doctorgroupids = empty($configs['doctorgroup']) ? [-2] : $configs['doctorgroup'];
$doctorgroupstr = $configs['showstr']['doctorgroupstr'] ?? ['全部'];

$doctorgroupids_input = implode(',', $doctorgroupids);
$doctorgroupstr_input = implode(',', $doctorgroupstr);
?>
<div class="form-group optaskfilter-list" data-key="doctorgroup">
    <input type="hidden" id="doctorgroup" name="doctorgroup" value="<?=$doctorgroupids_input?>">
    <input type="hidden" id="doctorgroupstr" name="doctorgroupstr" value="<?=$doctorgroupstr_input?>">
    <label class="col-xs-3 control-label" for="doctorgroup">医生组</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getCheckboxCtrImp4OneUi(CtrHelper::getDoctorGroupsCtrArrayForFilter(), 'doctorgroupids', $doctorgroupids, '');
        ?>
    </div>
    <script>
        $(function () {
            $('input[name="doctorgroupids"]').on('click', function () {
                var me = $(this);
                var click_val = me.val();

                var doctorgroupids = [];
                var doctorgroupstr = [];
                if (click_val == -2) {
                    var checked_cnt = 0;
                    $('input[name="doctorgroupids"]').each(function () {
                        var m = $(this);
                        if (m.val() != -2) {
                            doctorgroupids.push(m.val());
                            doctorgroupstr.push(m.data('value'));
                            m.prop('checked', false);
                        }

                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                        }
                    });
                    if (checked_cnt == 0) {
                        $('input[name="doctorgroupids"]').each(function () {
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
                    $('input[name="doctorgroupids"]').each(function () {
                        var m = $(this);
                        checkbox_cnt ++;
                        if (m.prop('checked') == true) {
                            checked_cnt ++;
                            if (m.val() != -2) {
                                doctorgroupids.push(m.val());
                                doctorgroupstr.push(m.data('value'));
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
                        $('input[name="doctorgroupids"]').each(function () {
                            var m = $(this);
                            if (m.val() != -2) {
                                m.prop('checked', false);
                            } else {
                                m.prop('checked', true);
                            }
                        });
                    }
                }

                var doctorgroupidstr = doctorgroupids.join(',');
                var doctorgroupstr = doctorgroupstr.join(',');

                $('input[name="doctorgroupids"]').each(function () {
                    var m = $(this);
                    if (m.val() == -2 && m.prop('checked') == true) {
                        doctorgroupidstr = '-2';
                        doctorgroupstr = '全部';
                    }
                });

                $('#doctorgroup').val(doctorgroupidstr);
                $('#doctorgroupstr').val(doctorgroupstr);
            });
        });
    </script>
</div>