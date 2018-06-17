<!-- 图表区 -->
<input type="hidden" id="wxpatientid" value="<?= $wxpatientid ?>" />
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
                <label>
                    <input type="checkbox" value="patientMsg" checked class="typestrItem" />
                    <span>患者消息</span>
                </label>
                <label>
                    <input type="checkbox" value="patientScale" checked class="typestrItem" />
                    <span>患者评估</span>
                </label>
                <label>
                    <input type="checkbox" value="patientHwk" checked class="typestrItem" />
                    <span>患者作业</span>
                </label>
                <label>
                    <input type="checkbox" value="opsMsg" checked class="typestrItem" />
                    <span>运营消息</span>
                </label>
                <label>
                    <input type="checkbox" value="systemMsg" checked class="typestrItem" />
                    <span>系统消息</span>
                </label>
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
