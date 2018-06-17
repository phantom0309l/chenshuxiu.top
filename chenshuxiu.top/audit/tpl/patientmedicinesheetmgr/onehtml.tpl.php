<style>
.tali {
    text-align:left !important;
}
.h4-title {
    margin-bottom: 10px;
    padding-left: 10px; 
    border-left: 2px solid #44b4a6;
}
</style>

<h5 class="push-10"><i class="si si-user"></i> <?=$patient->name?> 患者</h5>
<div class="block block-bordered">
    <ul class="nav nav-tabs nav-tabs-alt bg-gray-lighter" data-toggle="tabs">
        <li> <a href="javascript:">当前用药核对</a> </li>
        <li> <a href="javascript:">历史用药医嘱</a> </li>
        <li class="active" > <a href="javascript:">患者用药反馈</a> </li>
    </ul>
    <div class="block-content tab-content">
    <div class="tab-pane">
        <div>
            <?php if ($patient->diseaseid == 1) { ?>
                <a target="_blank" class="btn btn-default btn-sm mb10" href="/patientmedicinetargetmgr/add?patientid=<?= $patient->id ?>"><i class="fa fa-plus"></i> 添加</a>
            <?php } else { ?>
                <a class="btn btn-default btn-sm mb10" href="/patientmedicinetargetmgr/detailofpatient/?patientid=<?= $patient->id ?>"><i class="fa fa-plus"></i> 添加</a>
            <?php } ?>
            <?php
                $pcard = $patient->getMasterPcard();
            ?>
            <?php if( $pcard->send_pmsheet_status ){?>
                <span class="fr send_pmsheet_yes text-success">已发送</span>
                <span class="fr send_pmsheet_no none text-warning">未发送</span>
            <?php }else{?>
                <span class="fr send_pmsheet_yes none text-success">已发送</span>
                <span class="fr send_pmsheet_no text-warning">未发送</span>
            <?php }?>
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <th>药品</th>
                    <th>剂量</th>
                    <th>频率</th>
                    <th>方案</th>
                    <th>操作</th>
                </thead>
                <?php foreach( $pmtargets as $pmtarget){?>
                    <tr>
                        <td><?= $pmtarget->medicine->name ?></td>
                        <td><?= $pmtarget->drug_dose ?></td>
                        <td><?= $pmtarget->drug_frequency?></td>
                        <td>
                            <?php
                            $pmpkg = PatientMedicinePkgDao::getLastByPatientid($patient->id);
                            $pmpkgitem = PatientMedicinePkgItemDao::getByPatientmedicinepkgidMedicineid($pmpkg->id,$pmtarget->medicineid);
                            echo "{$pmpkgitem->drug_change}";?>
                        </td>
                        <td>
                            <a target="_blank" class="btn btn-default btn-xs" href="/patientmedicinetargetmgr/modify?patientmedicinetargetid=<?=$pmtarget->id?>"><i class="fa fa-pencil"></i></a>
                            <a class="delete_pmtarget btn btn-danger btn-xs" data-patientmedicinetargetid="<?=$pmtarget->id?>"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                <?php }?>
            </table>
            </div>
        </div>
        <div class="push-10">
            <a href="javascript:" id="sendmsg" data-patientid="<?= $patient->id?>" class="btn btn-success btn-sm" ><i class="fa fa-share"></i> 发送给患者</a>
            <a target="_blank" href="<?= $wx_uri ?>/patientmedicinesheet/one?openid=<?=$openid?>" class="btn btn-success btn-sm" ><i class="fa fa-eye"></i> 预览</a>
        </div>
    </div>
    <!--/end of tab-pane-->
    <div class="tab-pane">
        <div>
            <?php
            foreach($pmpkgs as $patientmedicinepkg){
                $patientmedicinepkgitems = array();
                if ($patientmedicinepkg instanceof PatientMedicinePkg) {
                    $patientmedicinepkgitems = PatientMedicinePkgItemDao::getListByPatientmedicinepkgid($patientmedicinepkg->id);
                }
                ?>
                <div>
                    <h4 class="h4-title"><?=$patientmedicinepkg->revisitrecord->thedate?></h4>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>药名</th>
                            <th>剂量</th>
                            <th>频率</th>
                            <th>调药方案</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($patientmedicinepkgitems as $a) {
                            ?>
                            <tr>
                                <td><?=$a->medicine->name ?></td>
                                <td><?=$a->drug_dose ?></td>
                                <td><?=$a->getDrug_frequencyStr(); ?></td>
                                <td><?=$a->drug_change ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <!--/end of tab-pane-->
    <div class="tab-pane active">
        <div class="form-horizontal">
            <?php
            foreach($pmsheets as $patientmedicinesheet){
                $patientmedicinesheetitems = PatientMedicineSheetItemDao::getListByPatientmedicinesheetid($patientmedicinesheet->id);
                ?>
                <h4 class="h4-title"><?=$patientmedicinesheet->thedate?>
                    <?php if( $patientmedicinesheet->auditstatus == 0 ){?>
                        <span class="fr">
                            <a class="auditwrong btn btn-danger btn-sm push-10-r" style="margin-top:-10px;" data-patientmedicinesheetid="<?= $patientmedicinesheet->id?>"><i class="fa fa-close"></i> 错误</a>
                            <a class="auditright btn btn-success btn-sm" style="margin-top: -10px;" data-patientmedicinesheetid="<?= $patientmedicinesheet->id?>"><i class="fa fa-check"></i> 正确</a>
                        </span>
                    <?php }else{?>
                        <span class="fr send_pmsheet_yes mt10 text-success font-s13 font-w500"><i class="fa fa-check-circle"></i> 已审核</span>
                    <?php }?>
                </h4>
                <div class="clearfix"></div>
                <div class="table-responsive">
                <table class="table table-bordered table-striped ">
                    <thead>
                    <tr>
                        <th>药名</th>
                        <th>剂量</th>
                        <th>频率</th>
                        <th style="min-width:150px;">对错</th>
                        <th>备注</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($patientmedicinesheetitems as $a) {
                        ?>
                        <tr>
                            <td><label class="control-label tali"><?=$a->medicine->name ?></label></td>
                            <td>
                                <div class="col-md-6 remove-padding-l">
                                    <input type="text" name="drug_dose" class="pmsi-drug_dose form-control" value="<?=$a->drug_dose ?>" />
                                </div>
                                <div class="col-md-6 remove-padding-l text-left">
                                   <label class="control-label tali">医嘱：<?=$a->target_drug_dose; ?></label>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-6 remove-padding-l">
                                    <input type="text" name="drug_frequency" class="pmsi-drug_frequency form-control" value="<?=$a->drug_frequency ?>" />
                                </div>
                                <div class="col-md-6 remove-padding-l text-left">
                                   <label class="control-label tali">医嘱：<?=$a->target_drug_frequency; ?></label>
                                </div>
                            </td>
                            <td><?php echo HtmlCtr::getSelectCtrImp(PatientMedicineSheetItem::$statusDescArray, 'status', $a->status , 'pmsi-status form-control'); ?></td>
                            <td><textarea class="form-control pmsi-auditremark"><?=$a->auditremark?></textarea></td>
                            <td style="vertical-align:middle">
                                <button class="pmsiModify  btn btn-default btn-xs" data-patientmedicinesheetitemid="<?=$a->id?>">
                                    <i class="fa fa-save"></i>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                </div>
                <div class="bg-gray-lighter push-20" style="padding: 10px;">
                    <p class="font-w600">患者反馈：</p>
                    <?=$patientmedicinesheet->content?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
<!--/end of tab-pane-->
    </div>
</div>

<script>
    $(function(){
        $(document).off("click", ".nav-tabs>li").on("click", ".nav-tabs>li", function() {
            var me = $(this);
            var index = me.index();
            var tab = me.parent().parent();
            var contents = tab.children(".tab-content").children(".tab-pane");
            me.addClass("active").siblings().removeClass("active");
            contents.eq(index).show().siblings().hide();
        });
        $(".pmsiModify").on("click",function(){
            var me = $(this);
            var me_tr = me.parents('tr');

            $.ajax({
                type: "post",
                url: "/patientmedicinesheetitemmgr/modifypost",
                data: {
                    "drug_dose": me_tr.find(".pmsi-drug_dose").val(),
                    "drug_frequency": me_tr.find(".pmsi-drug_frequency").val(),
                    "auditremark": me_tr.find(".pmsi-auditremark").val(),
                    "status": me_tr.find(".pmsi-status").val(),
                    "patientmedicinesheetitemid": me.data("patientmedicinesheetitemid")
                },
                dataType: "text",
                success: function () {
                    alert('保存成功');
                }
            });
        });

        $("#sendmsg").on("click",function(){
            if(! confirm("确定发送？")){
                return;
            }
            var patientid = $(this).data("patientid");

            $.ajax({
                "type" : "get",
                "data" : {
                    patientid : patientid
                },
                "url" : "/patientmedicinesheetmgr/sendmsgJson",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("发送成功");
                        $(".send_pmsheet_yes").show();
                        $(".send_pmsheet_no").hide();
                    }
                }
            });
        });

        $(".auditright").on("click",function(){
            if(! confirm("确定审核正确？")){
                return;
            }
            var patientmedicinesheetid = $(this).data("patientmedicinesheetid");
            var me = $(this);
            $.ajax({
                "type" : "get",
                "data" : {
                    patientmedicinesheetid : patientmedicinesheetid
                },
                "url" : "/patientmedicinesheetmgr/auditrightJson",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("审核完毕");
                        me.hide();
                        me.prev().hide();
                    }
                }
            });
        });

        $(".auditwrong").on("click",function(){
            if(! confirm("确定审核错误？")){
                return;
            }
            var patientmedicinesheetid = $(this).data("patientmedicinesheetid");
            var me = $(this);
            $.ajax({
                "type" : "get",
                "data" : {
                    patientmedicinesheetid : patientmedicinesheetid
                },
                "url" : "/patientmedicinesheetmgr/auditwrongJson",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("审核完毕");
                        me.hide();
                        me.next().hide();
                    }
                }
            });
        });

        $(".delete_pmtarget").on("click",function(){
            if(! confirm("确定删除？")){
                return;
            }
            var patientmedicinetargetid = $(this).data("patientmedicinetargetid");
            var me = $(this);
            $.ajax({
                "type" : "get",
                "data" : {
                    patientmedicinetargetid : patientmedicinetargetid
                },
                "url" : "/patientmedicinetargetmgr/deletepost",
                "dataType" : "text",
                "success" : function(data){
                    if(data == 'success'){
                        alert("删除完毕");
                        me.parent().parent().hide();
                    }
                }
            });
        });
    });

</script>
