<div class="modal fade" id="pipelevelFixBox" tabindex="-1" role="dialog"
     aria-labelledby="pipelevelFixBoxLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>

                <h4 class="modal-title" id="pipelevelFixBoxLabel">
                    反馈AI判别错误
                </h4>
            </div>

            <div class="modal-body">
                <form class="pipelevelFixBox">
                    <input type="hidden" class="typestr" value="thank"/>

                    <div class="form-group">
                        <label>内容</label>
                        <textarea class="form-control pipelevelFixBox-content" rows="7" readonly="readonly"></textarea>
                    </div>
                    <p class="pipelevelFixBox-notice text-success none text-right"></p>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    关闭
                </button>

                <button type="button" class="btn btn-primary pipelevelfix-btn">

                </button>
            </div>

        </div>
    </div>
</div>
<script>
$(function(){
    $('.modal-dialog').draggable({ cursor: "move"});
});
</script>
