<?php
$pagetitle = "给<span class='red'>{$doctor->name}</span>医生的患者发送模板消息";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <input type="hidden" id="doctorid" value="<?= $doctor->id ?>"/>
                <tr>
                    <td width=140>微信号</td>
                    <td>
                        <select id="wxshopSelect">
                            <?php foreach( $wxshops as $wxshop ){ ?>
                                <option value="<?= $wxshop->id ?>" <?= $wxshop->id == $current_wxshop->id ? 'selected' : ''?> ><?= $wxshop->name ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>微信模板</td>
                    <td>
                        <select id="wxtemplateSelect">
                            <?php foreach( $wxtemplates as $wxtemplate ){ ?>
                                <option value="<?= $wxtemplate->id ?>" <?= $wxtemplate->id == $current_wxtemplate->id ? 'selected' : ''?> ><?= $wxtemplate->title ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>示例</td>
                    <td>
                        <textarea name="content" cols="80" rows="8" style="border:none;" readonly ><?= $current_wxtemplate->content ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>正文（头部）</td>
                    <td>
                        <textarea cols="60" rows="3" class="first"></textarea>
                    </td>
                </tr>
                <?php foreach( $content_title_arr as $title ){ ?>
                    <tr>
                        <td><?= $title ?></td>
                        <td>
                            <?php if($current_wxtemplate->ename == "followupNotice" && $title == "随访内容"){ ?>
                            (发送消息时，xx(小写英文半角)将替换为患者姓名)<br/>
                            <?php } ?>
                            <?php if(strstr($title,"内容")){ ?>
                                <textarea cols="60" rows="3" class="keyword"></textarea>
                            <?php }else{ ?>
                                <input type="text" value="" class="keyword" />
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>备注</td>
                    <td>
                        <textarea cols="60" rows="3" class="remark"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>url</td>
                    <td>
                        <textarea cols="60" rows="3" class="url"></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <span class="sendBtn btn btn-default">发送</span>
                    </td>
                </tr>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        var app = {
            canSendMsg : true,
            init : function(){
                var self = this;
                self.wxshopSelectChange();
                self.wxtemplateSelectChange();
                self.sendTemplateMsg();
            },
            sendTemplateMsg : function(){
                var self = this;
                $(".sendBtn").on("click", function(){
                    var me = $(this);
                    if( confirm("确定要群发该消息吗？") ){
                        if( !self.canSendMsg ){
                            return;
                        }
                        self.canSendMsg = false;
                        me.text("正在发送...").addClass('btn-primary');
                        $.ajax({
                            url: '/wxtemplatemgr/sendJson',
                            type: 'POST',
                            dataType: 'text',
                            data: self.getSendData()
                        })
                        .done(function() {
                            me.text("已发送");
                        })
                        .fail(function() {
                        })
                        .always(function() {
                        });

                    }
                })
            },
            wxshopSelectChange : function(){
                var self = this;
                $("#wxshopSelect").on("change", function(){
                    var params = self.getParamsStr(true);
                    window.location.href = "/wxtemplatemgr/send" + params;
                })
            },
            wxtemplateSelectChange : function(){
                var self = this;
                $("#wxtemplateSelect").on("change", function(){
                    var params = self.getParamsStr(false);
                    window.location.href = "/wxtemplatemgr/send" + params;
                })
            },
            getParamsStr : function(iswxshop){
                var doctorid = $("#doctorid").val();
                var current_wxshopid = $("#wxshopSelect").val();
                var current_wxtemplateid = $("#wxtemplateSelect").val();
                if( iswxshop ){
                    return "?doctorid=" + doctorid + "&current_wxshopid=" + current_wxshopid;
                }else{
                    return "?doctorid=" + doctorid + "&current_wxshopid=" + current_wxshopid + "&current_wxtemplateid=" + current_wxtemplateid;
                }
            },
            getSendData : function(){
                var doctorid = $("#doctorid").val();
                var current_wxshopid = $("#wxshopSelect").val();
                var current_wxtemplateid = $("#wxtemplateSelect").val();
                var first = $(".first").val();
                var remark = $(".remark").val();
                var url = $(".url").val();
                var keywords = [];
                $(".keyword").each(function(){
                    keywords.push( $(this).val() );
                });

                return {
                    "doctorid" : doctorid,
                    "current_wxshopid" : current_wxshopid,
                    "current_wxtemplateid" : current_wxtemplateid,
                    "first" : first,
                    "remark" : remark,
                    "url" : encodeURIComponent(url),
                    "keywords" : keywords
                };
            }

        };
        app.init();
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
