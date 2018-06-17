<style>
    /*.tab-content .form-group label {*/
    /*font-weight: 500;*/
    /*width: 95px;*/
    /*text-align: left;*/
    /*padding-right: 0;*/
    /*}*/
</style>
<div class="mt10">
    <div class="block push-10-t">
        <ul class="nav nav-tabs nav-tabs-alt">
            <?php
            $patientrecordtpls = PatientRecordHelper::getPatientRecordTpls($patient);
            $i = 0;
            foreach ($patientrecordtpls as $key => $value) {
                if ($i == 0) {
                    $className = "active";
                } else {
                    $className = "";
                }
                $i++;
                ?>
                <li class="<?= $className ?>"><a href="javascript:"><?= $key ?></a></li>
                <?php
            }
            ?>
        </ul>
        <div class="block-content tab-content">
            <?php
            $patientrecordtpls = PatientRecordHelper::getPatientRecordTpls($patient);
            $i = 0;
            foreach ($patientrecordtpls as $key => $value) {
                if ($i == 0) {
                    $className = "active";
                } else {
                    $className = "";
                }
                $i++;
                ?>
                <div class="tab-pane <?= $className ?>" id="btabs-pr-tab1">
                    <?php include_once $tpl . "/patientrecordmgr/{$value}"; ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $(".patientrecord-addBtn").on("click", function () {
                var me = $(this);
                if (me.data('confirm') == true) {
                    if (!confirm('是否提交？')) {
                        return false;
                    }
                }
                var data = me.parents('.patientrecord-panel').serialize();
                $.ajax({
                    "type": "post",
                    "url": "/patientrecordmgr/addjson",
                    dataType: "json",
                    data: data,
                    "success": function (res) {
                        if (res.errno === '0') {
                            alert('保存成功');
                            $(".divOnSelected").find('.showOptask').click();
                            var tt = setInterval(function () {
                                if ($('.onePatientHtml .tab-menuAuto').length > 0) {
                                    $('.onePatientHtml .tab-menuAuto').find('li:last-child').click();
                                    clearInterval(tt);
                                }
                            }, 200);
                        } else {
                            alert(res.errmsg);
                        }
                    },
                    "error": function () {
                        alert('保存失败');
                    }
                });
            })
        });
    </script>
</div>
