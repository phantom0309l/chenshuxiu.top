<?php
$pagetitle = "快捷回复消息创建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>标题</th>
                        <td>
                            <input id="title" type="text" name="title" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>是否在医生端显示</th>
                        <td>
                            <?= HtmlCtr::getRadioCtrImp(array("0"=>"不显示","1"=>"显示"), 'show_in_doctor', 1, '', 'show_in_doctor')?>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>objtype</th>
                        <td>
                            <input id="objtype" type="text" name="objtype" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th width=140>objcode</th>
                        <td>
                            <input id="objcode" type="text" name="objcode" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>内容</th>
                        <td>
                            <textarea id="content" type="text" name="content" rows="6" style="width: 50%;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="btn btn-primary" id="addPipeTpl">提交</span>
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(document).on(
        "click",
        "#addPipeTpl",
        function() {
            var me = $(this);
            var title = $('#title').val();
            var show_in_doctor = $("input[name='show_in_doctor']:checked").val();
            var objtype = $('#objtype').val();
            var objcode = $('#objcode').val();
            var content = $('#content').val();

            if (title.length == 0) {
                //判断标题不能为空
                alert("请填写标题");
                return;
            }
            if (objtype.length == 0) {
                //判断objtype不能为空
                alert("请选择类型");
                return;
            }

            if (objcode.length == 0) {
                //判断objcode不能为空
                alert("请选择类型");
                return;
            }

            if (content.length == 0) {
                //判断内容不能为空
                alert("请填写内容");
                return;
            }
            $.ajax({
                "type" : "post",
                "data" : {
                    "title" : title ,
                    "show_in_doctor" : show_in_doctor,
                    "objtype" : objtype ,
                    "objcode" : objcode ,
                    "content" : content
                },
                "url" : "/pipetplmgr/addjson",
                "success" : function(data) {
                    if (data == 'ok') {
                        alert("亲，创建成功了！");
                    }else{
                        alert("失败了，再来一次。。。");
                    }
                }
            });
        });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
