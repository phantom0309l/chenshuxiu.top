<?php
$pagetitle = "{$auditor->name}绑定疾病";
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
                        <th>疾病名称</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diseases as $a) { ?>
                    <tr>
                        <td>
                            <?= $a->name ?>
                        </td>
                        <td>
                            <span class="btn btn-default bindBtn <?= $auditor->hasBindDisease($a->id) ? "btn-primary" : "" ?>" data-status="1" data-diseaseid="<?= $a->id ?>">开启</span>
                            <span class="btn btn-default bindBtn <?= $auditor->hasBindDisease($a->id) ? "" : "btn-primary" ?>" data-status="0" data-diseaseid="<?= $a->id ?>">关闭</span>
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
                var diseaseid = me.data("diseaseid");
                var status = me.data("status");
                $.ajax({
                    url: '/auditordiseaserefmgr/bindOrUnbindDiseaseJson',
                    type: 'post',
                    dataType: 'text',
                    data: {auditorid: auditorid, diseaseid: diseaseid, status: status}
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
