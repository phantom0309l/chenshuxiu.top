<style>
    #_ds_disease_select {
        background-color: #438EB9;
        color: #fff;
        font-size: 14px;
        padding: 2px 2px 2px 10px;
        border-radius: 3px;
    }

    ._ds_disease_option {
        padding: 2px 0px 2px 10px;
    }

    .disease-select {
        max-height: 500px;
        overflow-y: scroll;
    }
</style>
<?php
$auditorDiseaseRefs = AuditorDiseaseRefDao::getListByAuditor($myauditor);
$mydiseaseid = $mydisease->id;
?>
<div class="col-md-6">
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
        <?php
        if ($mydisease instanceof Disease) {
            $title_disease_name = $mydisease->name;
        } else {
            $title_disease_name = "全部疾病";
        }

        echo $title_disease_name;
        ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu disease-select">
        <li><a tabindex="-1" href="javascript:" data-diseaseid="0">全部疾病</a></li>
        <?php
        foreach ($auditorDiseaseRefs as $a) {
            ?>
            <li><a tabindex="-1" href="javascript:" data-diseaseid="<?= $a->diseaseid; ?>"><?= $a->disease->name; ?></a></li>
            <?php
        }
        ?>
    </ul>
</div>
<script>
    $(function () {
        $(".disease-select li a").on("click", function () {
            var diseaseid = parseInt($(this).data('diseaseid'));
            $.ajax({
                type: "post",
                url: "/index/setdiseaseidcookiejson",
                data: {"diseaseid": diseaseid},
                dataType: "text",
                success: function () {
                    //防止因切换疾病导致的bug
                    var specialArr = ["/optaskmgr/listnew", "/patientmgr/list"];
                    var pathname = location.pathname;
                    if ($.inArray(pathname, specialArr) > -1) {
                        window.location.href = pathname;
                    } else {
                        var url = window.location.href;
                        var urls = url.split('&pagenum=');
                        window.location.href = urls[0];
                    }
                }
            });
        });
    });
</script>
