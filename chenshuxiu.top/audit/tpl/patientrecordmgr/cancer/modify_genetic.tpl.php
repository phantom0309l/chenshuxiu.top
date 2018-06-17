<?php
$pagetitle = "备注修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/patientrecordmgr/modifypost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="patientrecordid" value="<?= $patientrecord->id ?>" />
                    <?php 
                        $type = $patientrecord->type;
                    ?>
                    <tr>
                        <th width=140>检测日期</th>
                        <td>
                            <input type="text" class="calendar" name="thedate" value="<?= $patientrecord->thedate ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>名称</th>
                        <td>
                            <?php
                                $selects = explode(',', $patientrecord_data['items']);

                                $display_str = 'display:none';
                                if (in_array('其他', $selects)) {
                                    $display_str = "";
                                }
                                echo HtmlCtr::getCheckboxCtrImp4OneUi(PatientRecordCancer::getOptionByCode('genetic'),"items", $selects, '');
                            ?>
                            <input type="text" style="display:none" class="form-control" id="genetic_items" name="genetic[items]" value="<?=$patientrecord_data['items']?>">
                            <input type="text" style="<?=$display_str?>" class="form-control" id="genetic_item_other" name="genetic[item_other]" value="<?=$patientrecord_data['item_other']?>">
                        </td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td>
                            <textarea name="content" rows="4" cols="40"><?= $patientrecord->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
<script type="text/javascript">
    $(function(){
        $(document).on('click', 'input[name="items"]', function(){
            var option_other = 0;
            var options = [];
            $('input[name="items"]').each(function() {
                var m = $(this);

                if (m.prop('checked') == true) {
                    options.push(m.val());

                    if (m.val() == '其他') {
                        option_other = 1;
                    }
                }

                if (m.prop('checked') == true && m.val() == '其他') {
                    option_other = 1;
                }
            });

            var options_str = options.join(',');

            $('#genetic_items').val(options_str);

            if (option_other == 1) {
                $("#genetic_item_other").show();
            } else {
                $("#genetic_item_other").hide();
            }
        });
    });
</script>
