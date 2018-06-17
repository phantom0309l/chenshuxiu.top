<div class="modal fade" id="doubtBox" tabindex="-1" role="dialog"
     aria-labelledby="doubtBoxLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>

                <h4 class="modal-title" id="doubtBoxLabel">患者类型切换</h4>
            </div>

            <div class="modal-body">
                <form class="doubtBox form-horizontal">
                    <?php $doubt_type_arr = CtrHelper::getDoubt_typeCtrArray();?>
                    <div class="form-group">
                        <label class="col-md-2 text-right">当前类型</label>
                        <div class="col-md-4 red">
                            <?= $doubt_type_arr[$patient->doubt_type] ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">类型</label>
                        <div class="col-md-4">
                            <?= HtmlCtr::getSelectCtrImp($doubt_type_arr,'doubt_type',0,'js-select2 form-control doubt_type_select') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">添加备注</label>
                        <div class="col-md-10">
                            <textarea class="form-control doubtBox-content" rows="7"></textarea>
                        </div>
                    </div>
                    <p class="doubtBox-notice text-success none text-right"></p>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-default doubt-btn">保存</button>
            </div>

        </div>
    </div>
</div>
<script>
$(function(){
    $('.modal-dialog').draggable({ cursor: "move"});
});
</script>
