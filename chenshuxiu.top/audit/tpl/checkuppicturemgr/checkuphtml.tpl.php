<div>
    <select id="checkupTplList">
        <option class="checkupTplList-item" value="0">
            未选择
        </option>
        <?php foreach($checkuptpls as $checkuptpl){?>
            <option class="checkupTplList-item" value="<?= $checkuptpl->id?>">
                <?=$checkuptpl->title?>
            </option>
        <?php }?>
    </select>
    <a target="_blank" href="/patientmgr/modify?patientid=<?= $patient->id?>">修改<?= $patient->name?>基本信息</a>
    <div id="checkupList"></div>
</div>
<script type="text/javascript">
    $(function(){
        $("#checkupTplList").on("change",function(){
            var me = $(this);
            var checkuptplid = parseInt(me.val());
            var patientid = <?= $patient->id?>;
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuptplid : checkuptplid,
                    patientid : patientid
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/checkupListhtml",
                "success" : function(data) {
                    $("#checkupList").html(data);
                }
            });
        });
    });
</script>