<?php
$pagetitle = "员工组新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
        <form action="/auditorgroupmgr/addpost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=120>员工组类型</th>
                    <td>
                        <div class="col-md-6" style="padding:0px">
                            <?= HtmlCtr::getRadioCtrImp(AuditorGroup::getTypeArr(), 'type', 'base' , '', 'type')?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th width=120>员工组ename</th>
                    <td>
                        <div class="col-md-2" style="padding:0px">
                            <input type="text" class="form-control" name="ename" value="" />
                        </div>
                        <div class="col-md-2" style="margin-top: 6px;">
                            ename
                        </div>
                    </td>
                </tr>
                <tr>
                    <th width=120>员工组名字</th>
                    <td>
                        <div class="col-md-2" style="padding:0px">
                            <input type="text" class="form-control" name="name" value="" />
                        </div>
                        <div class="col-md-2" style="margin-top: 6px;">
                            员工组名字
                        </div>
                    </td>
                </tr>
                <tr>
                    <th width=120>可选的基础配置</th>
                    <td>
                        <div class="col-md-2" style="padding:0px">
                            <?= HtmlCtr::getSelectCtrImp($enames, "baseEname", 0, 'baseEname js-select2 form-control') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th width=120>员工组成员</th>
                    <td>
                        <div class="col-md-10 numbers-Box" style="padding:0px">

                        </div>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" class="btn btn-success" value="提交" />
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
    $(document).on("change", ".baseEname", function(){
        var me = $(this);
        var ename = me.val();
        if(0 == ename){
            $(".numbers-Box").html();
            return;
        }
        $.ajax({
            "type" : "post",
            "data" : { ename : ename },
            "dataType" : "json",
            "url" : "/auditorGroupRefMgr/getAuditorsByEnameJson",
            "success" : function(data) {
                if(data.errno == 0){
                    $(".numbers-Box").html(data.auditorArr);
                }else{
                    alert(data.errmsg);
                }
            }
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
