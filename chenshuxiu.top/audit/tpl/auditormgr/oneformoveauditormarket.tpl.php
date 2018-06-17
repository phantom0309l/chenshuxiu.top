<?php
$pagetitle = "变换市场负责人列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.col-md-2 {
    height:34px;
    line-height: 34px;
}
.clear {
    overflow: hidden;
}
.radio-div {
    margin-top:10px;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form action="/auditormgr/oneformoveauditormarket" method="get" class="pr">
                    <div class="form-group mt10 clear">
                        <label class="control-label col-md-2">按市场负责人筛选：</label>
                        <div class="col-md-3">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllAuditorCtrArray(),"auditorid_market",$auditorid_market,'js-select2 form-control');?>
                        </div>
                    </div>
                    <div class="form-group mt10 clear">
                        <label class="control-label col-md-2">省份</label>
                        <div class="col-md-3">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllXprovinceCtrArray(),"xprovinceid",$xprovinceid,'js-select2 form-control');?>
                            <div class="radio-div">
                                <input name="xprovinceStatus" id="xprovinceidTrue" type="radio" value="1" checked />
                                <label for="xprovinceidTrue">正选</label>
                                <input name="xprovinceStatus" id="xprovinceidFalse" type="radio" value="0" />
                                <label for="xprovinceidFalse">反选</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt10 clear">
                        <label class="control-label col-md-2" style="text-align:left">城市</label>
                        <div class="col-md-3">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllXcityByXprovinceidCtrArray($xprovinceid),"xcityid",$xcityid,'js-select2 form-control');?>
                            <div class="radio-div">
                                <input name="xcityStatus" id="xcityidTrue" type="radio" value="1" checked />
                                <label for="xcityidTrue">正选</label>
                                <input name="xcityStatus" id="xcityidFalse" type="radio" value="0" />
                                <label for="xcityidFalse">反选</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="筛选" />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>名字</td>
                        <td>变更至市场人员</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            if(count($auditors)>0){
            foreach ($auditors as $auditor) {
                ?>
                    <tr>
                        <td><?= $auditor->id ?></td>
                        <td><?= $auditor->name ?></td>
                        <td><?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),"auditorid_market", 0, 'f18');?></td>
                        <td width="300">
                            <a class="btn btn-default changeAuditorMarket" data-id="<?= $auditor->id ?>">变更</a>
                        </td>
                    </tr>
                <?php }} ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function () {
        $("#auditorid_market").on("change",function () {
            var id = $(this).find("option:selected").val()
            var name = $(this).find("option:selected").text()
            name = name.split(' ')[2]

            $(".table-responsive table tbody tr:eq(0) td:eq(0)").html(id)
            $(".table-responsive table tbody tr:eq(0) td:eq(1)").html(name)
            $(".changeAuditorMarket").attr('data-id',id)
        })

        $(".changeAuditorMarket").on("click", function () {
            var me = $(this);
            var id = me.data("id");
            var selected_auditor = me.parents("tr").find("#auditorid_market").find("option:selected")
            to_auditorid_market = selected_auditor.val();
            to_auditorid_market_name = selected_auditor.text();
            var xprovinceid = $("#xprovinceid").val()
            var xprovinceStatus = $("input[name=xprovinceStatus]:checked").val()
            var xcityid = $("#xcityid").val()
            var xcityStatus = $("input[name=xcityStatus]:checked").val()

            if(to_auditorid_market == 0){
                alert("请选择要变更为的市场负责人！");
                return false;
            }
            if(!confirm("确认市场负责人变更为 ["+to_auditorid_market_name+"] 吗?")){
    			return false;
    		}

            var data = {};
            data.to_auditorid_market = to_auditorid_market;
            data.from_auditorid_market = id;
            data.xprovinceid = xprovinceid
            data.xcityid = xcityid
            data.xprovinceStatus = xprovinceStatus
            data.xcityStatus = xcityStatus

            $.ajax({
                "type" : "get",
                "data" : data,
                "dataType" : "html",
                "url" : "/auditormgr/moveauditormarketjson",
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
    $(function () {
      $("#xprovinceid").on('change', function(){
        var me = $(this);

        var xprovinceid = me.val();

        $.ajax({
            "type" : "get",
            "data" : {
                xprovinceid : xprovinceid
            },
            "dataType" : "json",
            "url" : "/xcitymgr/getxcitys",
            "success" : function(data) {
                var htmlstr = "";
                $.each(data['data'], function (index, info) {
                    htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                });

                $("#xcityid").html(htmlstr);
                $("#select2-xcityid-container").html("");
            }
        });
    });


    });



XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
