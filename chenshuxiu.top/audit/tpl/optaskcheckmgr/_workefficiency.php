<div class="we-top clear" style="height: 20px;margin-bottom: 5px;">
    <h4 class="fl">工作效率统计</h4>

    <div class="fr">
        <div class="form-group">
            <label class="col-md-3 we-time-label">请选择时间段</label>
            <div class="col-md-6 time-box" >
                <input type="text" class="form-control we-time-slot" style="background-color:#fff;" name="we-time-slot" value="<?= $default_time_slot?>" readonly >
                <i class="fa fa-align-justify fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div class="we-content clear">
    <div class="we-content-item col-md-4 fl">
        <span class="item-title">   <i class="si si-folder-alt fa-2x"></i> 关闭任务统计</span>
        <div class="item-content">
            <p class="optask_cnt">关闭任务总数： <span>0</span>条</p>

            <div class="optask_list" id="optask_list"></div>

        </div>
    </div>
    <div class="we-content-item border-lr col-md-4 fl">
        <span class="item-title"> <i class="si si-call-end fa-2x"></i> 电话统计</span>
        <div class="item-content">

            <ul class="cdrmeeting_cnt_list">
                <li class="cnt_call_out">呼出电话数量: <span>0</span>个</li>
                <li class="cnt_call_out_succ">呼出且接通的电话数量：<span>0</span>个</li>
                <li class="avg_call_out">呼出且接通的平均通话时长：<span>0</span>分钟</li>
                <li class="cnt_call_in">接起呼入的电话数量：<span>0</span>个</li>
                <li class="avg_call_in">接起呼入的平均通话时长：<span>0</span>分钟</li>
                <li class="last_meeting">最后一个电话的结束时间：<span>0000.00.00 - 00:00:00</span></li>
            </ul>

            <div class="cdrmeeting_out_list" id="cdrmeeting_out_list"></div>
        </div>
    </div>
    <div class="we-content-item col-md-4 fl">
        <span class="item-title"><i class="si si-bubble fa-2x" style="font-size:14px;margin-right: 10px;"></i>消息统计</span>
        <div class="item-content">
            <ul class="push_msg_list">
                <li class="cnt">发出的消息数量:<span>0</span>条</li>
                <li class="last_msg">最后一条消息的发出时间：<span>0000.00.00 - 00:00:00</span></li>
            </ul>
        </div>
    </div>
</div>