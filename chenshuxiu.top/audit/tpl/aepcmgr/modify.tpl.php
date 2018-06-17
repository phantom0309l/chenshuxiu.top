<?php
$pagetitle = "答卷修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.none{ display: none;}
.J-product{ margin-top: 20px;}
.J-product input{ width:100%;}
.J-medicine{ margin-top: 20px;}
.J-medicine input{ width:100%;}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <input type="hidden" name="xquestionsheetid" id="xquestionsheetid" value="<?= $xanswersheet->xquestionsheetid ?>" />
            <form action="/aepcmgr/modifypost" method="post" id="aepcForm">
                <input type="hidden" name="xanswersheetid" value="<?= $xanswersheet->id ?>" />
                <?php
                    foreach ($xanswersheet->getAnswers() as $a) {
                        $defaultHide = '';
                        if ($a->isDefaultHide()) {
                            $defaultHide = 'style="display:none;"';
                        }
                    ?>
                        <div class='questionDiv sheet-question-box <?=$a->xquestion->ename?> delete-<?=$a->id;?>' <?=$defaultHide?>>
                            <?php echo $a->getHtml(); ?>
                        </div>
                    <?php
                    }
                ?>
                <div>
                    <input type="submit" class="sheet-question-subit J-submitBtn" value="提交答卷" />
                </div>
                <br />
                <br />
            </form>
        </section>
    </div>
    <div class="clear"></div>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="productShell none">
                <div class="table-responsive">
                    <table class="table table-bordered J-product">
                    <tr>
                        <td>产品名称</td>
                        <td>生产厂家</td>
                        <td>适应症</td>
                        <td>生产批号</td>
                        <td>剂型</td>
                        <td>单次剂量</td>
                        <td>使用频率</td>
                        <td>给药途径</td>
                        <td>开始时间</td>
                        <td>结束时间</td>
                    </tr>
                </table>
                </div>
            </div>

            <div class="medicineShell none">
                <div class="table-responsive">
                    <table class="table table-bordered J-medicine">
                <tr>
                    <td>产品名称</td>
                    <td>生产厂家</td>
                    <td>适应症</td>
                    <td>生产批号</td>
                    <td>剂型</td>
                    <td>单次剂量</td>
                    <td>使用频率</td>
                    <td>给药途径</td>
                    <td>开始时间</td>
                    <td>结束时间</td>
                </tr>
            </table>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
    var app = {
        saveProductNode : null,
        saveMedicineNode : null,
        init : function(){
            var self = this;
            if(self.isPC()){
                return;
            }
            self.huixianProductHtml();
            self.huixianMedicineHtml();
            self.handleOndblclick();
            self.handleSubmit();
        },
        handleOndblclick : function(){
            $("input:radio").dblclick(function(){
                var me = $(this);
                me.removeAttr('checked');
            });
        },
        isPC : function(){
            var xquestionsheetid = parseInt( $("#xquestionsheetid").val() );
            return 275209356 == xquestionsheetid;
        },
        huixianProductHtml : function(){
            var self = this;
            var textareas = $(".sheet-question-textarea");
            var parentNode = textareas.eq(0).parent().parent();
            var node = parentNode.find(".sheet-question-content").next();
            self.saveProductNode = node.find(".sheet-question-textarea");
            //隐藏原来的html
            node.hide();
            self.fillDefaultProductShell();
            var newNode = $( $(".productShell").html() );
            parentNode.append(newNode);
            self.fillInputProductShell();

        },

        huixianMedicineHtml : function(){
            var self = this;
            var textareas = $(".sheet-question-textarea");
            var parentNode = textareas.eq(1).parent().parent();
            var node = parentNode.find(".sheet-question-content").next();
            self.saveMedicineNode = node.find(".sheet-question-textarea");
            //隐藏原来的html
            node.hide();
            self.fillDefaultMedicineShell();
            var newNode = $( $(".medicineShell").html() );
            parentNode.append(newNode);
            self.fillInputMedicineShell();
        },
        fillDefaultProductShell : function(){
            var self = this;
            var node = $(".J-product");
            var saveProductNode = self.saveProductNode;
            var val = saveProductNode.val();
            var dataArr = JSON.parse( decodeURIComponent(val) );
            var len = dataArr.length;
            for(var n=0; n<5; n++){
                var item = self.createProductItem();
                node.append(item);
            }

        },
        fillInputProductShell : function(){
            var self = this;
            var node = $(".J-product");
            var saveProductNode = self.saveProductNode;
            var val = saveProductNode.val();
            var dataArr = JSON.parse( decodeURIComponent(val) );
            var len = dataArr.length;
            if(len){
                $.each(dataArr, function(i,a){
                    var tdNodes = node.find(".tritem").eq(i).find("td");
                    tdNodes.each(function(j,tdnode){
                        console.log(a[j]);
                        $(tdnode).find("input").val(a[j]);
                    });
                })
            }
        },
        fillDefaultMedicineShell : function(){
            var self = this;
            var node = $(".J-medicine");
            var saveMedicineNode = self.saveMedicineNode;
            var val = saveMedicineNode.val();
            var dataArr = JSON.parse( decodeURIComponent(val) );
            var len = dataArr.length;
            for(var n=0; n<5; n++){
                var item = self.createMedicineItem();
                node.append(item);
            }
        },
        fillInputMedicineShell : function(){
            var self = this;
            var node = $(".J-medicine");
            var saveMedicineNode = self.saveMedicineNode;
            var val = saveMedicineNode.val();
            var dataArr = JSON.parse( decodeURIComponent(val) );
            var len = dataArr.length;
            if(len){
                $.each(dataArr, function(i,a){
                    var tdNodes = node.find(".tritem").eq(i).find("td");
                    tdNodes.each(function(j,tdnode){
                        console.log(a[j]);
                        $(tdnode).find("input").val(a[j]);
                    });
                })
            }
        },
        createProductItem : function(){
            var str = '<tr class="product-item tritem">\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
            </tr>';
            return $(str);
        },
        createMedicineItem : function(){
            var str = '<tr class="medicine-item tritem">\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
                <td><input type="text"/></td>\
            </tr>';
            return $(str);
        },
        handleSubmit : function(){
            var self = this;
            $(".J-submitBtn").on("click", function(e){
                e.preventDefault();
                self.setProductData();
                self.setMedicineData();
                $("#aepcForm").submit();
            })
        },
        getData : function(pnodestr){
            var self = this;
            var pnode = $(pnodestr);
            var items = pnode.find(".tritem");
            var d = [];
            items.each(function(){
                var me = $(this);
                var inputNodes = me.find("input");
                var isFilledItem = self.isFilledItem(me);
                if(isFilledItem){
                    var obj = {};
                    inputNodes.each(function(i,node){
                        obj[i] = $.trim($(node).val());
                    })
                    d.push(obj);
                }
            });
            return d;
        },
        setProductData : function(){
            var self = this;
            var saveProductNode = self.saveProductNode;
            var data = self.getData(".J-product");
            var dataStr = encodeURIComponent( JSON.stringify(data) );
            console.log(dataStr);
            saveProductNode.val(dataStr);
        },
        setMedicineData : function(){
            var self = this;
            var saveMedicineNode = self.saveMedicineNode;
            var data = self.getData(".J-medicine");
            var dataStr = encodeURIComponent( JSON.stringify(data) );
            console.log(dataStr);
            saveMedicineNode.val(dataStr);
        },
        isFilledItem : function(item){
            var inputNodes = item.find("input");
            var cnt = 0;
            inputNodes.each(function(){
                var me = $(this);
                var val = $.trim(me.val());
                if(val.length){
                    cnt++;
                }
            })
            if( item.hasClass('product-item')){
                return cnt > 1;
            }else{
                return cnt > 0;
            }
        }
    };
    app.init();
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
