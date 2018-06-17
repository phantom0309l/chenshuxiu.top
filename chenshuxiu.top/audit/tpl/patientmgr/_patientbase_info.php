<!-- 患者资料 -->
<div class="patientBaseBox bgpurple p10 mt10" style="line-height: 120%">
    <p>
        <a target='_blank' href='/patientmgr/one?patientid=<?=$patient->id?>'><span>患者姓名：<?= $patient->name ?></span><i class="fa fa-search"></i></a>
        <span>患者身份证号：<?= $patient->prcrid ?></span>
    </p>
    <p>
        <span>性别：<?= $patient->getSexStr()?></span>
        <span>年龄：<?= $patient->getAgeStr()?> 岁</span>
        <span>城市：<?= $patient->getXprovinceStr();?> <?= $patient->getXcityStr(); ?></span>
    </p>

    <?php

foreach ($patient->getPcards() as $pcard) {
        $color = "#ffffbb";
        if ($patient->doctorid == $pcard->doctorid && $patient->diseaseid == $pcard->diseaseid) {
            $color = "#ec971f";
        }
        ?>
        <p class='border1' style="background-color: <?=$color?>">
        <span style="padding: 10px 10px 10px 10px">疾病-医生：</span>
        <span class='blue'><?= $pcard->disease->name ?></span>
        <span class='red'><?= $pcard->doctor->name ?></span>
        <span class='black'><?= $pcard->getYuanNeiStr() ?></span>
        <a class="btn btn-success" target="_blank" href="/pcardmgr/modifydisease?pcardid=<?=$pcard->id?>">修改疾病<i class="fa fa-pencil"></i></a>
    </p>
    <?php } ?>

    <p>
        <span> 合并症/诊断：</span>
        <?php
        foreach ($patient->getTagRefs('Disease') as $a) {
            ?>
            <a target="_blank" class="span-blue" href="/tagrefmgr/list?typestr=<?=$a->tag->typestr ?>&name=<?=$a->tag->name ?> "><?=$a->tag->name ?></a>
            <?php
        }
        ?>
        <a class="btn btn-success" target="_blank" href="/tagrefmgr/list?objtype=Patient&objid=<?=$patient->id ?>&typestr=Disease">修改</a>
    </p>
    <p>
        <span>距离上次复发时间: <?= $pcard->getDescStrOfLast_incidence_date2Today()?></span>
        <a href="/revisitrecordmgr/add?patientid=<?= $patient->id ?>" target="_blank" class="btn btn-success">新建门诊记录</a>
        <a href="/patientmgr/modify?patientid=<?= $patient->id ?>" target="_blank" class="btn btn-success">修改患者</a>
        <a href="#" id="settest" data-patientid="<?=$patient->id?>" class="btn btn-danger">设置为测试患者</a>
        <a href="#" id="setnormal" data-patientid="<?=$patient->id?>" class="btn btn-success">设置为正常患者</a>
    </p>
    <form action="/patientmgr/modifynextpmstimepost" method="post">
        <input type="hidden" name="patientid" value="<?= $patient->id?>" />
    <?php
    $pcard = $patient->getMasterPcard();
    ?>
    (MasterPcard)下一次用药核对: <input type="text" class='calendar' name="next_pmsheet_time" value="<?php echo $pcard->next_pmsheet_time == "0000-00-00 00:00:00" ? "关闭" : substr($pcard->next_pmsheet_time,0,10); ?>" />
        <input class="btn btn-danger" type="submit" value="修改时间" />
        <a class="btn btn-danger" href="/patientmgr/deletenextpmstimepost?patientid=<?= $patient->id?>">关闭</a>
    </form>
</div>
<script type="text/javascript">
    function init () {
        var is_test = <?=$patient->is_test?>;
        if (is_test == 0) {
            $("#settest").show();
            $("#setnormal").hide();
        } else if (is_test == 1) {
            $("#settest").hide();
            $("#setnormal").show();
        } else {
            $("#settest").hide();
            $("#setnormal").hide();
        }
    };
    $(function(){
        init();

        $("#settest").on("click",function(){
            var me = $(this);
            var patientid = me.data('patientid');
            $.ajax({
                url: '/patientmgr/setpatienttestjson',
                type: 'post',
                dataType: 'text',
                data: {
                    patientid: patientid
                }
            })
            .done(function(data) {
                alert(data + "success");
                $("#settest").hide();
                $("#setnormal").show();
                console.log("success");
            })
            .fail(function(data) {
                alert(data + "fail");
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });

        $("#setnormal").on('click', function() {
            var me = $(this);
            var patientid = me.data('patientid');
            $.ajax({
                url: '/patientmgr/setpatientnormaljson',
                type: 'post',
                dataType: 'text',
                data: {
                    patientid: patientid
                }
            })
            .done(function(data) {
                alert(data + "success");
                $("#settest").show();
                $("#setnormal").hide();
                console.log("success");
            })
            .fail(function(data) {
                alert(data + "fail");
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        });
    });
</script>
