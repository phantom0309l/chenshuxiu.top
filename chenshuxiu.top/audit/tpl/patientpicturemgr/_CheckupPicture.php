<?php
$checkuppicture = $patientpicture->obj;
$checkuptpls = CheckupTplDao::getListByDoctorAndDiseaseid_isInTkt_isInAdmin($checkuppicture->doctor, null);
?>

<div>
    <div id="ishascheckuptpl" data-ishascheckuptpl='<?php if($checkuppicture->checkup instanceof Checkup && $checkuppicture->checkup->xanswersheetid>0 ) {echo 1;}else{echo 0;}?>' data-checkuptplid="<?= $checkuppicture->checkup->checkuptplid ?>"></div>
    <select id="checkupTplList" class="form-control">
        <option class="checkupTplList-item" value="0">
            未选择
        </option>
        <?php foreach($checkuptpls as $checkuptpl){?>
            <option class="checkupTplList-item" value="<?= $checkuptpl->id?>">
                <?=$checkuptpl->title?>
            </option>
        <?php }?>
    </select>
    <div id="checkupList" class="mt10"></div>
</div>
<script type="text/javascript">
    $(function(){
        function getCheckupListhtml (checkuptplid){
            var checkuppictureid = <?= $checkuppicture->id?>;
            var patientid = <?= $patient->id?>;
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuppictureid : checkuppictureid,
                    checkuptplid : checkuptplid,
                    patientid : patientid
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/checkupListhtml",
                "success" : function(data) {
                    $("#checkupList").html(data);
                }
            });
        }
        $("#checkupTplList").on("change",function(){
            var me = $(this);
            var checkuptplid = parseInt(me.val());
            getCheckupListhtml(checkuptplid);
        });

        if( $("#ishascheckuptpl").data("ishascheckuptpl") ){
            var checkuptplid = $('#ishascheckuptpl').data('checkuptplid');
            $("#checkupTplList").find("option[value="+checkuptplid+"]").attr("selected",true);
            getCheckupListhtml(checkuptplid);
        }
    });
</script>
