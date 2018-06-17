<div>
    <div id="ishascheckup" data-ishascheckup='<?= $checkuppicture->checkup instanceof Checkup ? 1 : 0 ?>' data-checkupid="<?= $checkuppicture->checkupid ?>"></div>
    <input type="hidden" id="checkuppictureid" value="<?=$checkuppicture->id?>">
    <div class="checkup-add btn btn-primary btn-sm"  data-checkuptplid="<?=$checkuptpl->id?>">添加</div>
    <div class="table-responsive push-10-t">
        <table class="table table-bordered tdcenter">
        <thead>
        <tr>
            <th>创建日期</th>
            <th>检查日期</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($checkups as $a) {
            if( false == $a->xanswersheet instanceof XAnswerSheet ){
                continue;
            }
            ?>
            <tr class="checkup-modify-tr">
                <td><?= $a->getCreateDay(); ?></td>
                <td><?= $a->check_date; ?></td>
                <td>
                    <div class="checkup-modify btn btn-primary" data-checkupid="<?=$a->id?>">查看</div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>
    <div class="xanswersheet-Box" style="border: 1px solid #ddd;padding: 10px"></div>
</div>
<script type="text/javascript">
    $(function(){
        $(".checkup-add").on("click",function(){
            var me = $(this);
            var checkuppictureid = $("#checkuppictureid").val();
            var checkuptplid = me.data("checkuptplid");
            var patientid = <?=$patient->id?>;
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuppictureid : checkuppictureid,
                    checkuptplid : checkuptplid,
                    patientid : patientid
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/checkupaddhtml",
                "success" : function(data) {
                    $(".xanswersheet-Box").html(data);
                    $(".checkup-modify-tr").removeClass('bg-info');
                }
            });
        });

        $(".checkup-modify").on("click",function(){
            var me = $(this);
            var checkuppictureid = $("#checkuppictureid").val();
            var checkupid = me.data("checkupid");
            var patientid = <?=$patient->id?>;
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuppictureid : checkuppictureid,
                    checkupid : checkupid,
                    patientid : patientid
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/checkupmodifyhtml",
                "success" : function(data) {
                    $(".xanswersheet-Box").html(data);
                    $(".checkup-modify-tr").removeClass('bg-info');
                    me.parent().parent().addClass("bg-info");
                }
            });
        });

        if( $("#ishascheckup").data("ishascheckup") ){
            $.ajax({
                "type" : "post",
                "data" : {
                    checkuppictureid : $("#checkuppictureid").val(),
                    checkupid : $("#ishascheckup").data("checkupid"),
                    patientid : <?=$patient->id?>
                },
                "dataType" : "html",
                "url" : "/checkuppicturemgr/checkupmodifyhtml",
                "success" : function(data) {
                    $(".xanswersheet-Box").html(data);
                    $(".checkup-modify-tr").removeClass('bg-info');
                }
            });
        }
    });
</script>
