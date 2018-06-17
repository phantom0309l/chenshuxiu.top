<?php
$pagetitle = "患者核对用药记录 PatientMedicineSheet";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
$pageStyle = <<<STYLE
.control-label {
    font-weight:500;
    text-align:left;
}
.trOnSeleted {
    background-color: #CCCCFF;
}

.trOnMouseOver {
    background-color: #CCCCFF;
}

.isclosed_objid {
    background-color: #e6e6e6
}

.isclosed {
    background-color: #eeeeee
}

.objid {
    background-color: #dff8df
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

    <div class="col-md-12" id="top">
        <section class="col-md-4 content-left collapse">
        <div class=searchBar>
            <input type="hidden" name="revisitrecordid" value="<?= $revisitrecordid?>">
            <form action="/patientmedicinesheetmgr/list" method="get" class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 remove-padding-l control-label" style="width:80px" for="">医生</label>
                    <div class="col-md-2">
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 remove-padding-l control-label" style="width:80px;" for="">患者姓名</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="patient_name" value="<?= $patient_name ?>" />
                    </div>
                </div>
                <div class="clearfix text-right">
                <input type="submit" class="btn btn-success btn-sm" value="组合刷选" />
                </div>
            </form>
        </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
            <thead>
            <tr>
                <th>患者姓名</th>
                <th>提交日期</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($patients as $a ){
                $link_name_temp = '详情';
                $sheet = $a->getLastPatientMedicineSheet();
                if( $sheet instanceof PatientMedicineSheet && $sheet->auditstatus ==0 ){$link_name_temp = '审核';}
                ?>
                <tr onmouseover="over(this)" onmouseout="out(this)" onclick="showrevisitrecord(this)" data-patientid="<?=$a->id?>">
                    <td>
                        <?php
                            if ($a instanceof Patient) {
                                echo $a->getMaskName();
                            }
                        ?>
                    </td>
                    <td><?= $sheet->thedate ?></td>
                    <td>
                        <a href="#top"><?= $link_name_temp ?></a>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td colspan="10" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
            </tr>
            </tbody>
        </table>
            </div>
    </section>
    <section class="col-md-12 content-right pb10">
        <div id="RevisitRecordHtmlShell" class="contentBoxBox"></div>
    </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        App.initHelper('select2');
        showrevisitrecord();
    })
    function over(tr){
        var className = $(tr).attr('id');
        $(tr).removeClass(className).addClass('trOnMouseOver');
    }
    function out(tr){
        var className = $(tr).attr('id');
        $(tr).removeClass('trOnMouseOver').addClass(className);
    }
    function showrevisitrecord(tr){
        //$("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver");
        //$(tr).addClass("trOnSeleted");

        //$(".content-right").show();
        //var me = $(tr);
        //var patientid = me.data("patientid");
        var patientid = "{$patientid}";

        $("#RevisitRecordHtmlShell").html('');
        $.ajax({
            "type" : "get",
            "data" : {
                patientid : patientid
            },
            "dataType" : "html",
            "url" : "/patientmedicinesheetmgr/oneHtml",
            "success" : function(data) {
                $("#RevisitRecordHtmlShell").html(data);
            }
        });
    }
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
