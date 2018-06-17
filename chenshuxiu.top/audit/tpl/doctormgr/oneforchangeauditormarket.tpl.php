<?php
$pagetitle = "医生变更市场负责人列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>名字</td>
                        <td>当前市场人员</td>
                        <td>变更至市场人员</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $doctor->id ?></td>
                        <td><?= $doctor->name ?></td>
                        <td><?= $doctor->marketauditor->name ?></td>
                        <td><?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),"auditorid_market", 0, 'f18');?></td>
                        <td width="300">
                            <a class="btn btn-default changeAuditorMarket" data-id="<?= $doctor->id ?>">变更</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function () {
        $(".changeAuditorMarket").on("click", function () {
            var me = $(this);
            var id = me.data("id");
            var selected_auditor = me.parents("tr").find("#auditorid_market").find("option:selected")
            to_auditorid_market = selected_auditor.val();
            to_auditorid_market_name = selected_auditor.text();
            if(to_auditorid_market == 0){
                alert("请选择要变更为的市场负责人！");
                return false;
            }
            if(!confirm("确认市场负责人变更为 ["+to_auditorid_market_name+"] 吗?")){
    			return false;
    		}

            var data = {};
            data.to_auditorid_market = to_auditorid_market;
            data.doctorid = id;

            $.ajax({
                "type" : "get",
                "data" : data,
                "dataType" : "html",
                "url" : "/doctormgr/changeauditormarketjson",
                "success" : function(data){
                    if(data == "ok"){
                        me.addClass("btn-primary");
                        alert("变更成功！");
                    }
                    if(data == "default"){
                        alert("变更失败，需要选择一种变更模式！");
                    }
                    if(data == "notChange"){
                        alert("没有查询到要变更的医生！");
                    }
                }
            });
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
