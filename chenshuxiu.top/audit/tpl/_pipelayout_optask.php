<!-- 图表区 -->
<input type="hidden" id="wxpatientid" value="<?= $wxpatientid ?>" />
<div class="pipeMainBox block">
    <!-- 院外医嘱和流页面 -->
<!--
    <div class="pipeMainBox-t contentBoxTitle" style="border:0">
        <a class="pipesBoxTitle btn btn-success">全部流</a>
        <a class="taskBoxTitle btn btn-default">院外医嘱任务列表</a>
        <a class="pipesWxOpMsgTitle btn btn-default">医助流</a>
    </div>
-->
    <ul class="nav nav-tabs nav-tabs-alt nav-tabs-left">
        <li class="active">
            <a href="javascript:">全部流</a>
        </li>
        <li>
            <a href="javascript:">院外医嘱任务列表</a>
        </li>
        <li>
            <a href="javascript:">只看图片</a>
        </li>
        <li class="pull-right">
            <a href="javascript:" ><i class="fa fa-newspaper-o"></i> 医助代填量表</a>
        </li>
    </ul>
    <div class="pipeMainBox-c block-content tab-content">
        <!-- 医患流展示 -->
        <div class="pipeMainBox-c-item tab-pane active" id="btabs-static-all">
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
            <div class="showMoreShell text-center">
                <a href="javascript:" class="btn btn-default AP push-10" id="showMore"><i class="fa fa-angle-double-down"></i> 查看更多</a>
            </div>
        </div>
        <!-- 院外医嘱任务列表 -->
        <div id="taskShell" class="pipeMainBox-c-item none tab-pane">
            <p class="text-center">暂无数据</p>
        </div>
        <!-- 只看图片列表 -->
        <div class="pipeMainBox-c-item none tab-pane">
            <div id="picOnlyShell">

            </div>
            <!-- 查看更多 -->
            <div class="showMorePicOnlyShell text-center">
                <a href="javascript:" class="btn btn-default AO push-10" id="showMore"><i class="fa fa-angle-double-down"></i> 查看更多</a>
            </div>
        </div>
        <!-- 医助流展示 -->
        <!--
        <div class="pipeMainBox-c-item none tab-pane" id="btabs-static-assistant">
            <div id="pipeWxOpMsgShell">
                <div id="wxopmsgreply"></div>
                <div id="pipeWxOpMsgDetail"></div>
            </div>
            <div class="showMoreShell text-center">
                <a href="javascript:" class="btn btn-default AD push-10" id="showMore"><i class="fa fa-angle-double-down"></i> 查看更多</a>
            </div>
        </div>
        -->
        <div id="papertplHtmlShell" class="tab-pane"></div>
    </div>
</div>
<?php include dirname(__FILE__) . "/pipemgr/_ocrPictureModel.php"; ?>

<script>
//重要！！这是一个全局都可以生效的tabclick
    $(document).off("click", ".nav-tabs>li").on("click", ".nav-tabs>li", function() {
        var me = $(this);
        var index = me.index();
        var tab = me.parent().parent();
        var contents = tab.children(".tab-content").children(".tab-pane");
        me.addClass("active").siblings().removeClass("active");
        contents.eq(index).show().siblings().hide();
    });
    $(function () {
        $(document).on('click','.cdr-btn',function () {
            var id = $(this).attr('data-cdr-id');
            var that = this;
            var cdr_meeting_box = $(that).parents('div.btnSection').siblings('div.block-content').find('div.cdr-meeting-box');
            var content = cdr_meeting_box.html();

                $.ajax({
                    url     :   '/cdrmeetingmgr/onecdrmeetinghtml?id='+id,
                    type    :   'get',
                    dataType:   'html',
                    success :   function (response) {
                        cdr_meeting_box.show().html(response);
                        $('textarea').each(function () {
                            this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
                        }).on('input', function () {
                            this.style.height = 'auto';
                            this.style.height = (this.scrollHeight) + 'px';
                        });
                    }
                })
        });

        $(document).on('click', '.cdr-meeting-box .cdr-close-btn', function () {
            $(this).parents('.cdr-meeting-box').hide();
        });

        $(document).on('blur', '.cdr-textarea', function() {
            var text = $(this).val();
            var cdr_meeting_id = $(this).parents('form[name="cdr-form"]').children('input[name="cdr-meeting-id"]').val();
            var index = $(this).attr('data-index');
            var that = this;
            var text_back = $(this).attr('data-text-back');

            if(text == null || text == '') {
                $(this).val(text_back);
                alert('不能修改为空');
                return false;
            }

            if(text == text_back) {
                return false;
            }

            $.ajax({
                url     :   '/cdrmeetingmgr/changecdrjsonpost',
                type    :   'post',
                data    :   {
                    'text'  :   text,
                    'cdr_meeting_id' : cdr_meeting_id,
                    'index' :  index
                },
                dataType:   'json',
                success :   function (response) {
                    if(response.errcode == 0){
                        $(that).html(response.cdr_json_text);
                    }
                }
            })
        });
    })

</script>

