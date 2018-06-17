<?php
$pagetitle = "{$auditor->name}绑定监控消息类型";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <input type="hidden" id="auditorid" name="auditorid" value="<?= $auditor->id ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>id</th>
                        <th>分组名称</th>
                        <th>ename</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditorpushmsgtpls as $k => $a) { ?>
                    <tr>
                        <td>
                            <?= $k ?>
                        </td>
                        <td>
                            <?= $a->id ?>
                        </td>
                        <td>
                            <?= $a->title ?>
                        </td>
                        <td>
                            <?= $a->ename ?>
                        </td>
                        <td>
                            <span class="btn btn-default bindBtn <?= $auditor->hasBindPushMsgTpl($a->id) ? "btn-primary" : "" ?>" data-status="1" data-auditorpushmsgtplid="<?= $a->id ?>">开启</span>
                            <span class="btn btn-default bindBtn <?= $auditor->hasBindPushMsgTpl($a->id) ? "" : "btn-primary" ?>" data-status="0" data-auditorpushmsgtplid="<?= $a->id ?>">关闭</span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function () {
    var app = {
        canClick : true,
        init : function(){
            var self = this;
            self.handleBind();
        },
        handleBind : function(){
            var self = this;
            $(".bindBtn").on("click", function(){
                var me = $(this);
                if( me.hasClass('btn-primary') ){
                    return;
                }
                if( !self.canClick ){
                    return;
                }
                self.canClick = false;
                var auditorid = self.getAuditorid();
                var auditorpushmsgtplid = me.data("auditorpushmsgtplid");
                var can_ops = me.data("status");

                if(false == auditorid>0){
                    self.canClick = true;
                    alert("请选择一个工作人员！");
                    return;
                }

                $.ajax({
                    url: '/auditorpushmsgtplrefmgr/bindOrUnbindJson',
                    type: 'post',
                    dataType: 'text',
                    data: {
                        auditorid: auditorid,
                        auditorpushmsgtplid: auditorpushmsgtplid,
                        can_ops: can_ops}
                })
                .done(function() {
                    me.parents("td").find(".btn-primary").removeClass('btn-primary');
                    me.addClass('btn-primary');
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                    self.canClick = true;
                });
            })
        },
        getAuditorid : function(){
            return $("#auditorid").val();
        }
    };
    app.init();
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
