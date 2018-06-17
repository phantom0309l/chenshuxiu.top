<?php
$pagetitle = '用药核对';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .drugsheetRemark{
        border:1px solid #ccc;
        background: #f7f7f7;
        padding: 10px;
        margin-top: 20px;
    }
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>" id="patientid" />
        <section class="col-md-12">
        <div class="searchBar">
            <p>
                <span>患者：<?= $patient->name ?></span>
                <span>所属医生:<?= $patient->doctor->name?></span>
            </p>
            <div>
                <a class="btn btn-default" href="/drugsheetmgr/list?patientid=<?= $patient->id ?>">查看用药列表</a>
            </div>
        </div>

        <?php include_once $tpl . "/drugsheetmgr/_nearly2.php"; ?>

        <div>填写日期：<?= $drugsheet->thedate ?></div>
        <?php if($drugsheet->remark){ ?>
            <div class="drugsheetRemark">其他：<?= $drugsheet->remark ?></div>
        <?php } ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>药名</td>
                        <td>剂量</td>
                        <td>频率</td>
                        <td>备注</td>
                        <td>操作</td>
                    </tr>
                </thead>
            <?php
            $drugitems = $drugsheet->getDrugItems();
            foreach ($drugitems as $i => $a) {
            ?>
                <tr>
                    <td>
                        <?= $a->medicine->name?>
                    </td>
                    <td>
                        <input style="width: 40px;" value="<?= $a->value ?>" type="text" class="medicinevalue" /> <?= $a->medicine->unit?>
                    </td>
                    <td>
                        <?php echo HtmlCtr::getSelectCtrImp($drug_frequency_arr,'drug_frequency',$a->drug_frequency,"drug_frequency"); ?>
                    </td>
                    <td>
                        <textarea style="width: 230px; height: 100px; overflow: auto;" class="content"><?= $a->content ?></textarea>
                    </td>
                    <td>
                        <button data-drugitemid="<?= $a->id ?>" class="modify btn btn-default">修改</button>
                        <button data-drugitemid="<?= $a->id ?>" class="delBtn btn btn-default">删除</button>
                    </td>
                </tr>
            <?php } ?>
        </table>
            </div>
            <?php $pagetitle = "新增用药记录";include $tpl . "/_pagetitle.php"; ?>
            <div>
                <input type="text" placeholder="按药品名或者商品名搜索" class="medicine_name"/>
                <span class="btn btn-primary searchMedicine">搜索</span>
                <div class="medicineBox"></div>
            </div>
            <form action="/drugitemmgr/addpost" method="post" id="drugitemForm">
                <input type="hidden" id="drugsheetid" name="drugsheetid" value="<?= $drugsheet->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th>药名id</th>
                        <td>
                            <input type="text" value="" name="medicineid" id="medicineid" />
                        </td>
                    </tr>
                    <tr>
                        <th>剂 量：</th>
                        <td>
                            <input type="text" value="" name="value" id="medicinevalue"  />
                        </td>
                    </tr>
                    <tr>
                        <th>用药日期：</th>
                        <td>
                            <input type="text" name="record_date" value="<?= $drugsheet->thedate ?>" id="record_date" readonly />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class="btn btn-primary submitBtn" style="margin-left: 70px;">提交</span>
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
$(function(){
    var app = {
        init : function(){
            var self = this;
            //搜索逻辑
            self.handleSearchMedicines();

            self.drugItemAdd();
            self.drugItemModify();
            self.drugItemDel();
        },
        handleSearchMedicines : function(){
            $(".searchMedicine").on("click", function(){
                var val = $.trim( $(".medicine_name").val() );
                if(val.length == 0){
                    alert("请输入药名");
                    return;
                }
                $.ajax({
                    type: "post",
                    url: "/medicinemgr/listOfSearchHtml",
                    data: {"medicine_name" : val},
                    dataType: "html",
                    success : function(data){
                        $(".medicineBox").html(data);
                    }
                });
            })
        },
        drugItemAdd : function(){
            var self = this;
            $(".submitBtn").on("click", function(){
                var val1 = $.trim( $("#medicineid").val() );
                var val2 = $.trim( $("#medicinevalue").val() );
                if(val1.length==0 || val2.length==0){
                    alert("请输入必填项");
                    return;
                }
                $("#drugitemForm").submit();
            });
        },
        drugItemModify : function(){
            var self = this;
            $(".modify").on("click", function(){
                var me = $(this);
                var drugitemid = me.data("drugitemid");
                var parentNode = me.parents("tr");
                var data = self.getData(parentNode);
                data.drugitemid = drugitemid;
                $.ajax({
                    type: "post",
                    url: "/drugitemmgr/modifynewJson",
                    data:data,
                    dataType: "text",
                    success : function(){
                        alert("保存成功");
                        window.location.href = window.location.href;
                    }
                });
            });
        },
        drugItemDel : function(){
            var self = this;
            $(".delBtn").on("click", function(){
                if( confirm("确定要删除吗?") ){
                    var me = $(this);
                    var drugitemid = me.data("drugitemid");
                    $.ajax({
                        type: "post",
                        url: "/drugitemmgr/deleteJson",
                        data:{"drugitemid" : drugitemid},
                        dataType: "text",
                        success : function(){
                            alert("删除成功");
                            window.location.href = window.location.href;
                        }
                    });
                }
            });
        },
        getNodeVal : function( node ){
            var str = "";
            if(node.length){
                var val = $.trim( node.val() );
                str = val;
            }
            return str;
        },
        getData : function(root){
            var self = this;
            var value = self.getNodeVal( root.find(".medicinevalue") );
            var drug_frequency = self.getNodeVal( root.find(".drug_frequency") );
            var content = self.getNodeVal( root.find(".content") );
            return {
                "value" : value,
                "drug_frequency" : drug_frequency,
                "content" : content
            };
        }
    };
    app.init();
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
