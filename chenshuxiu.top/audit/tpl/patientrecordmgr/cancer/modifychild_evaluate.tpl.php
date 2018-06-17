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
            <form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="patientrecordid" value="<?= $patientrecord->id ?>" />
                    <?php 
                        $type = $patientrecord->type;
                    ?>
                    <tr>
                        <th width=140>日期</th>
                        <td>
                            <input type="text" class="calendar" name="thedate" value="<?= $patientrecord->thedate ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>类别</th>
                        <td>
                            <?php $evaluate_class = PatientRecordCancer::getOptionByCode('evaluate_class'); ?>
                            <select class="patientrecord-evaluate-class" name="<?=$type?>[class]">
                                <?php foreach ($evaluate_class as $k => $v) { ?>
                                    <option <?= $k == $patientrecord_data["class"] ? "selected" : "" ?> value="<?= $k ?>"><?= $v ?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>评价</th>
                        <td>
                            <select class="patientrecord-evaluate-assess" data-assess="<?= $patientrecord_data['assess']?>" name="<?=$type?>[assess]"></select>
                        </td>
                    </tr>
                    <tr>
                        <th>关联化疗</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(PatientRecordCancer::getChemoOptionByPatientid($patientrecord->patientid),"{$type}[relate_chemo]",$patientrecord_data["relate_chemo"],'f18'); ?>
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
<?php
$footerScript = <<<XXX
$(function () {
    var init_assess = function(){

        var class_select = $(".patientrecord-evaluate-class");
        var assess_select = $(".patientrecord-evaluate-assess");
        var assess = assess_select.data('assess');
        assess_select.empty();
        var class_value = class_select.val();
        var assesses = [];
        switch (class_value) {
            case '新辅助':
                assesses = ['完全退缩','部分退缩','稳定','进展']
                break;

            case '辅助':
                assesses = ['DFS','PD']
                break;

            case 'RECIST':
                assesses = ['CR','PR','SD','PD']
                break;
            default:

        }

        for (var i = 0; i < assesses.length; i++) {
            var selected = "";
            if( assesses[i] == assess ){
                selected = "selected";
            }
            assess_select.append("<option " + selected + " value=\'" + assesses[i] + "\'>" + assesses[i] + "</option>")
        }
    }
    $(".patientrecord-evaluate-class").on("change", function(){
        init_assess();
    })

    init_assess();
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
