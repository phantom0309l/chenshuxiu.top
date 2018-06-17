<div class="block">
    <div class="block-content pt10">
        <form class="form-horizontal">
            <input type="hidden" id="select_doctorid" value="<?= $doctor->id ?>">
            <div class="form-group push-10">
                <div class="col-xs-8 mb10">
                    <label class="push-5-r" for="content_repty"><?= $myauditor->name ?> 医助</label>
                    <a class="btn btn-default btn-sm deleteNew" href="javascript:void(0);"
                       data-doctorid="<?= $doctor->id ?>">去new</a>
                </div>
                <div class="col-xs-12 push-10">
                    <textarea class="form-control" id="content_repty" name="content_repty" rows="5"
                              placeholder="请输入内容"></textarea>
                </div>
                <div class="col-xs-12">回复给:<span style="color:red"><?= $doctor->name ?>医生</span></div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <a href="#" class="btn btn-sm btn-info" id="auditor_reply_to_doctor"><i
                                class="fa fa-send push-5-r"></i>回复</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(document).off('click', '.deleteNew').on('click', '.deleteNew', function () {
            var me = $(this);

            var doctorid = me.data('doctorid');

            $.ajax({
                type: "get",
                url: "/doctormgr/deletenewjson",
                dataType: "html",
                data: {
                    "doctorid": doctorid
                },
                success: function (d) {
                    $("#shownew-" + doctorid).text('');
                },
                error: function (e) {
                    alert("操作失败!");
                }
            });

        });
    });
</script>

<!-- 技术测试，编号0724—1509！ -->
