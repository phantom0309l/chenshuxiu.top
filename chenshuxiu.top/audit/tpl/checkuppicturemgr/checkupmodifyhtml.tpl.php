<form id="checkupmodifypost-form" action="/checkuppicturemgr/checkupmodifypost" method="post">
    <input type="hidden" name="checkuppictureid" value="<?= $checkuppicture->id ?>" />
    <input type="hidden" name="patientid" value="<?= $patient->id ?>" />
    <input type="hidden" name="checkupid" value="<?= $checkup->id ?>" />
    <div style="margin-bottom: 20px;">
        <div class="triangle-blue"></div>
        <span class="question-title"> 检查日期 </span>
        <input type="text" name="check_date" class="calendar answer-box" readonly value="<?=$checkup->check_date?>" />
    </div>
    <?php

    foreach ($checkup->xanswersheet->getAnswers() as $a) {
        echo "<div>".$a->getQuestionCtr()->getHtml()."</div>";
    }
    ?>
    <div class="checkupmodifypost-btn btn btn-info" style="margin: 10px;">保存并关联</div>

</form>

<script>
    $(document).ready(function(){
        $(".checkupmodifypost-btn").on("click",function(){
            var data = $('#checkupmodifypost-form').serialize();
            $.ajax({
                "type": "post",
                "data": data,
                "dataType": "html",
                "url": "/checkuppicturemgr/checkupmodifypost",
                "success": function(d) {
                    if (d != 'ok') {
                        alert('保存失败，请联系技术人员');
                    } else {
                        alert("已保存");
                        $("#changestatusBox").html("已归档");
                    }
                }
            });
        });
    });
</script>
