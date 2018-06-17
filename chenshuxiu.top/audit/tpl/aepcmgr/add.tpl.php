<?php
$pagetitle = "{$papertpl->title}";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .none{ display: none;}
    .J-product{ margin-top: 20px;}
    .J-product input{ width:100%;}
    .J-medicine{ margin-top: 20px;}
    .J-medicine input{ width:100%;}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <input type="hidden" id="xanswersheetid" value="<?= $paper->xanswersheetid ?>"/>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/aepcmgr/addpost" method="post" id="aepcForm">
                <input type="hidden" id="xquestionsheet-title" value="<?= $xquestionsheet->title ?>">
                <input type="hidden" id="papertplid" name="papertplid" value="<?= $papertpl->id ?>">
                <input type="hidden" id="thepatientid" name="thepatientid" value="<?= $thepatientid ?>">
                <?php
                    foreach ($xquestionsheet->getQuestions($issimple) as $a) {
                        $defaultHide = '';
                        if ($a->isDefaultHide()) {
                            $defaultHide = 'style="display:none;"';
                        }
                        ?>
                            <div class='questionDiv <?=$a->ename?>' <?=$defaultHide?>>
                                <?php echo $a->getHtml (); ?>
                            </div>
                            <div style="clear: both"></div>
                        <?php
                    }
                ?>
                <div>
                    <input type="submit" class="sheet-question-subit J-submitBtn" value="提交" />
                </div>
            </form>
        </section>
    </div>

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
        <tr class="product-item tritem">
            <td><input type="text"/></td>
            <td><input type="text" value="礼来" readonly/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="product-item tritem">
            <td><input type="text"/></td>
            <td><input type="text" value="礼来" readonly/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="product-item tritem">
            <td><input type="text"/></td>
            <td><input type="text" value="礼来" readonly/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="product-item tritem">
            <td><input type="text"/></td>
            <td><input type="text" value="礼来" readonly/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="product-item tritem">
            <td><input type="text"/></td>
            <td><input type="text" value="礼来" readonly/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
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
        <tr class="medicine-item tritem">
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="medicine-item tritem">
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="medicine-item tritem">
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="medicine-item tritem">
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
        <tr class="medicine-item tritem">
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
            <td><input type="text"/></td>
        </tr>
    </table>
        </div>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
        var app = {
            canClick : true,
            saveProductNode : null,
            saveMedicineNode : null,
            init : function(){
                var self = this;
                self.createProductHtml();
                self.createMedicineHtml();
                self.handleOndblclick();
                self.initEventNo();
                self.handleSubmit();
            },
            handleOndblclick : function(){
                $("input:radio").dblclick(function(){
                    var me = $(this);
                    me.removeAttr('checked');
                });
            },
            initEventNo : function(){
                var node = $(".XQuestionSheet-275143946-275148346").find("input[type=text]");
                var xanswersheetid = $('#xanswersheetid').val();
                node.val(xanswersheetid);
            },
            createProductHtml : function(){
                var self = this;
                var parentNode = $(".XQuestionSheet-275143946-275200646");
                var node = parentNode.find(".sheet-question-content").next();
                self.saveProductNode = node.find(".sheet-question-textarea");
                //隐藏原来的html
                node.hide();
                var newNode = $( $(".productShell").html() );
                parentNode.append(newNode);
            },
            createMedicineHtml : function(){
                var self = this;
                var parentNode = $(".XQuestionSheet-275143946-275201196");
                var node = parentNode.find(".sheet-question-content").next();
                self.saveMedicineNode = node.find(".sheet-question-textarea");
                //隐藏原来的html
                node.hide();
                var newNode = $( $(".medicineShell").html() );
                parentNode.append(newNode);
            },
            handleSubmit : function(){
                var self = this;
                $(".J-submitBtn").on("click", function(e){
                    e.preventDefault();

                    if(false == self.canClick){
                        alert("请勿重复点击！");
                    }
                    self.canClick = false;
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
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
