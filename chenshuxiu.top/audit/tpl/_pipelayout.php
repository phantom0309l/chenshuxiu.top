<!-- 图表区 -->
<input type="hidden" id="wxpatientid" value="<?= $wxpatientid ?>" />
<?php
if ($wxpatientid == 0) {
    ?>
    <div id="chartShellTitle" class="contentBoxTitle">
        <span class="patient_name_title"></span>
        图表
    </div>
    <div id="chartShell" class="contentBoxBox"></div>
    <?php
}
?>
<!-- 患者基本信息区 -->
<div class="contentBoxTitle">
    <span class="patient_name_title"></span><span class="patient_name_title_statusstr red"></span>
    基本信息
</div>
<div id="patientBaseShell" class="contentBoxBox"></div>

<div class="pipeMainBox">
    <!-- 院外医嘱和流页面 -->
    <div class="pipeMainBox-t contentBoxTitle">
        <a class="pipesBoxTitle tab-btn-highlight btn btn-success">全部流</a>
        <a class="taskBoxTitle tab-btn-highlight">院外医嘱任务列表</a>
        <a class="pipesWxOpMsgTitle tab-btn-highlight">医助流</a>
    </div>
    <div class="pipeMainBox-c">
        <!-- 医患流展示 -->
        <div class="pipeMainBox-c-item">
            <p class="typestrBox">
                <?php foreach($arr_filter as $arr_filter_one){ ?>
                    <label class="css-input css-checkbox css-checkbox-info push-10-r">
                        <input type="checkbox" value="<?= $arr_filter_one["ids"] ?>" checked class="typestrItem" />
                        <span></span><?= $arr_filter_one["name"] ?>
                    </label>
                <?php } ?>
                <button class="btn btn-info btn-xs push-10-r typeAllBtn" data-ischecked="1">全部</button>
                <button class="btn btn-default btn-xs push-10-r cancelAllBtn" data-ischecked="1">取消全部勾选</button>
            </p>
            <div id="pipeShell"></div>
            <!-- 查看更多 -->
            <div class="showMoreShell">
                <span class="btn btn-default AP" id="showMore">查看更多</span>
            </div>
        </div>
        <!-- 院外医嘱任务列表 -->
        <div id="taskShell" class="pipeMainBox-c-item none"></div>
        <!-- 医助流展示 -->
        <div class="pipeMainBox-c-item mt10 none">
            <div id="pipeWxOpMsgShell">
                <div class="mt10" id="wxopmsgreply"></div>
                <div id="pipeWxOpMsgDetail"></div>
            </div>
            <!-- 查看更多 -->
            <div class="showMoreShell">
                <span class="btn btn-default AD" id="showMore">查看更多</span>
            </div>
        </div>
    </div>
</div>
