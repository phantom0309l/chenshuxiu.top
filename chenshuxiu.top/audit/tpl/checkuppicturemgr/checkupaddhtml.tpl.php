<form id="checkupaddpost-form" method="post">
    <input type="hidden" name="checkuppictureid" value="<?= $checkuppicture->id ?>" />
    <input type="hidden" name="patientid" value="<?= $patient->id ?>" />
    <input type="hidden" name="checkuptplid" value="<?= $checkuptpl->id ?>" />
    <div style="margin-bottom: 20px;">
        <div class="triangle-blue"></div>
        <span class="question-title"> 检查日期 </span>
        <input type="text" name="check_date" class="calendar answer-box" readonly value="<?=date("Y-m-d")?>" />
    </div>
    <?php

    if( $checkuptpl->xquestionsheet instanceof XQuestionSheet ){
        foreach ($checkuptpl->xquestionsheet->getQuestions() as $a) {
            echo "<div>".$a->getQuestionCtr()->getHtml()."</div>";
        }
        ?>
        <div class="checkupaddpost-btn btn btn-info" style="margin: 10px;">保存</div>
    <?php }else{
        echo "没有设置相应问卷";
    }

    ?>
</form>

<script>
    $(document).ready(function(){
        $(".checkupaddpost-btn").on("click",function(){
            var data = $('#checkupaddpost-form').serialize();
            $.ajax({
                "type": "post",
                "data": data,
                "dataType": "html",
                "url": "/checkuppicturemgr/checkupaddpost",
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
