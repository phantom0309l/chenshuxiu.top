<?php
$doctorid = $configs['doctor'][0] ?? -1;
$doctorstr = $configs['showstr']['doctorstr'][0] ?? '';
?>
<div class="form-group optaskfilter-list" data-key="doctor">
    <input type="hidden" id="doctor" name="doctor" value="<?=$doctorid?>">
    <input type="hidden" id="doctorstr" name="doctorstr" value="<?=$doctorstr?>">
    <label class="col-xs-3 control-label" for="doctorid">医生</label>
    <div class="col-xs-9">
        <?php
            echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorCtrArrayForFilter(), 'doctorid', $doctorid, 'js-select2 form-control', 'width: 100%');
        ?>
    </div>
    <script>
        $(function () {
            $('#doctorid').on('change', function () {
                var me = $(this);
                var doctorid = me.val();
                var doctorstr = me.find('option:selected').text();

                $('#doctor').val(doctorid);
                $('#doctorstr').val(doctorstr);
            });
        });
    </script>
</div>