<style>
#_ds_disease_select {
	background-color: #369;
	border: 1px solid #39f;
	margin-top: 13px;
	font-size: 14px;
	padding: 2px 2px 2px 10px;
	border-radius: 3px;
}

._ds_disease_option {
	padding: 2px 0px 2px 10px;
	background-color: #39f;
}
</style>
<?php
$auditorDiseaseRefs = AuditorDiseaseRefDao::getListByAuditor($myauditor);
$mydiseaseid = $mydisease->id;
?>
<select id="_ds_disease_select">
<?php foreach( $auditorDiseaseRefs as $a ) {?>
    <?php if( $a->diseaseid == $mydiseaseid ) {?>
        <option value="<?= $a->diseaseid ?>" selected="selected" class="_ds_disease_option"> <?= $a->disease->name ?> </option>
    <?php }else{?>
        <option value="<?= $a->diseaseid ?>" class="_ds_disease_option"> <?= $a->disease->name ?> </option>
    <?php }?>
<?php }?>
</select>
<script>
    $(function() {
        $("#_ds_disease_select").on("change",function () {
            var diseaseid = parseInt($(this).val());
            $.ajax({
                type: "post",
                url: "/index/setdiseaseidcookiejson",
                data: {"diseaseid": diseaseid},
                dataType: "text",
                success: function (){
					//防止因切换疾病导致的bug
					var specialArr = ["/optaskmgr/listnew", "/patientmgr/list"];
					var pathname = location.pathname;
					if($.inArray(pathname, specialArr) > -1){
	                    window.location.href = pathname;
					}else{
	                    window.location.href = window.location.href;
					}
                }
            });
        });
    });
</script>
