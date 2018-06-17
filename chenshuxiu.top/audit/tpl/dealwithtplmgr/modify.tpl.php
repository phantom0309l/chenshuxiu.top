<?php
$pagetitle = "快捷回复修改";
$cssFiles = [
    $img_uri . '/static/css/jquery-ui.autocomplete.min.css?v=20180208',
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208',
    $img_uri . '/v5/common/select_doctor.js?v=20180530',
    $img_uri . '/v5/common/search_patient.js?v=20180530',
]; // 填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/dealwithtplmgr/modifypost" method="post">
            <input type="hidden" name="dealwithtplid" value="<?= $dealwithtpl->id ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>dealwithtplid</th>
                    <td width=50%><?= $dealwithtpl->id?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $dealwithtpl->createtime?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>修改时间</th>
                    <td><?= $dealwithtpl->updatetime?></td>
                    <td></td>
                </tr>
                <tr>
                    <th>疾病分组</th>
                    <td>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(true),"diseasegroupid",$dealwithtpl->diseasegroupid, "diseasegroupid"); ?>
                    </td>
                    <td>
                        选择"
                        <span class="red">不分组</span>
                        ", 即为"
                        <span class="blue">公司通用</span>
                        ", 如: "询问患者能否收到消息"
                        <br />
                        否则, 限定在
                        <span class="blue">疾病组</span>
                        内可见
                    </td>
                </tr>
                <tr>
                    <th>疾病</th>
                    <td>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(true),"diseaseid",$dealwithtpl->diseaseid); ?>
                    </td>
                    <td>
                        选择"
                        <span class="red">全部</span>
                        ",
                        <span class="blue">疾病组</span>
                        内通用
                        <br />
                        否则, 显示在
                        <span class="blue">疾病区块</span>
                    </td>
                </tr>
                <tr>
                    <th>医生</th>
                    <td>
                        <?php $doctorid = $dealwithtpl->doctorid; ?>
                        <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                    </td>
                    <td class="blue">疾病和医生最好不要同时选择</td>
                </tr>
                <tr>
                    <th>
                        groupstr 分组
                    </th>
                    <td class="groupstr-Box">
                        分组：<input id="groupstr" type="text" name="groupstr" value="<?= $dealwithtpl->groupstr?>" />
                    </td>
                    <td>
                        <span class="blue">多动症：</span>开药门诊 行为问题 用药相关 疾病知识 平台服务 其他
                        <br />
                        <span class="blue">非多动症：</span>回复/反馈 问用药 问复诊 问诊/询问 提醒/通知 催复诊 催检查/评估/量表 催资料 流程/知识 其他
                    </td>
                </tr>
                <tr>
                    <th>标题</th>
                    <td>
                        <input id="title" type="text" name="title" style="width: 80%;" value="<?= $dealwithtpl->title?>" />
                    </td>
                    <td>
                        请认真填写, 要有
                        <span class="blue">区分度</span>
                        , 方便浏览查找;
                        <br />
                        <span class="blue">医生专用</span>
                        的时加后缀, 如: "标题标题标题
                        <span class="red">- 王迁</span>
                        "
                    </td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td>
                        <textarea id="msgcontent" name="msgcontent" rows="6" style="width: 80%;"><?= $dealwithtpl->msgcontent ?></textarea>
                    </td>
                    <td>
                        占位符说明: (有需求找老史)
                        <br />
                        <span class="red">pp</span>
                        (小写) : 患者姓名
                        <br />
                        <span class="red">dd</span>
                        (小写) : 医生姓名
                        <br />
                        <span class="red">DD</span>
                        (大写) : 疾病名
                    </td>
                </tr>
                <tr>
                    <th>关键词</th>
                    <td>
                        <textarea id="keywords" name="keywords" rows="2" style="width: 90%;"><?= $dealwithtpl->keywords ?></textarea>
                    </td>
                    <td>关键词, 匹配患者的咨询问题</td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="修改" />
                    </td>
                    <td></td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(document).on("change", ".diseasegroupid", function(){
        var me = $(this);
        var diseasegroupid = me.val();
        if(0==diseasegroupid){
            return;
        }
        $.ajax({
            "type" : "post",
            "data" : { diseasegroupid : diseasegroupid },
            "dataType" : "json",
            "url" : "/dealWithTplMgr/getGroupstrs",
            "success" : function(data) {
                if(data.errno == 0){
                    $(".groupstr-Box").html(data.groupstrs);
                }else{
                    alert(data.errmsg);
                }
            }
        });
    });
     $(document).on("click", ".groupstrradio", function(){
        var me = $(this);
        var groupstr = me.next("label").text();
        $("#groupstr").val(groupstr);
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
