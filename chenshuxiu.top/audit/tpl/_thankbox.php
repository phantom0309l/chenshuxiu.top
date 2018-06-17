<div class="modal fade" id="thankBox" tabindex="-1" role="dialog"
     aria-labelledby="thankBoxLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>

                <h4 class="modal-title" id="thankBoxLabel">
                    添加感谢留言
                </h4>
            </div>

            <div class="modal-body">
                <form class="thankBox">
                    <input type="hidden" class="typestr" value="thank"/>

                    <div class="form-group">
                        <label>留言内容</label>
                        <textarea class="form-control thankBox-content" rows="7"></textarea>
                    </div>
                    <p class="thankBox-notice text-success none text-right"></p>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    关闭
                </button>

                <button type="button" class="btn btn-primary thank-btn">
                    提交
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
