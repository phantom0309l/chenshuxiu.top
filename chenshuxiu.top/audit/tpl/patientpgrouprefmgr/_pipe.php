<section class="content-right">
	<!-- 患者基本信息区 -->
    <div class="contentBoxTitle">
        <span class="patient_name_title"></span>
        基本信息
    </div>
    <div id="patientBaseShell" class="contentBoxBox"></div>
    <!-- 事件区 -->
    <div id="pipeeventShellTitle" class="contentBoxTitle">
        <span class="patient_name_title"></span>
        事件
    </div>
    <div id="pipeeventShell" class="contentBoxBox <?= ($mydisease->id==1)?'':'none'; ?> "></div>
    <!-- 院外医嘱和流页面 -->
    <div class="contentBoxTitle">
        <a class="taskBoxTitle tab-btn-highlight">院外医嘱任务列表</a>
        <a class="pipesBoxTitle tab-btn-highlight">全部流</a>
        <a class="pipesWxOpMsgTitle tab-btn-highlight">医助流</a>
    </div>
    <!-- 院外医嘱任务列表 -->
    <div id="taskShell" class="contentBoxBox mt10 none"></div>
    <!-- 医患流展示 -->
    <div id="pipeShell" class="contentBoxBox"></div>
    <!-- 医助流展示 -->
    <div id="pipeWxOpMsgShell" class="contentBoxBox mt10 none">
        <div class="mt10" id="wxopmsgreply"></div>
        <div id="pipeWxOpMsgDetail"></div>
    </div>
    <!-- 查看更多 -->
    <div class="showMoreShell">
        <span class="btn btn-default AP" id="showMore">查看更多</span>
    </div>
</section>
