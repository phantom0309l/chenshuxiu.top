<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/1/22
 * Time: 13:35
 */

$page_title = "用药核对";
include_once($tpl . "/_common/_header.tpl.php");
?>
<style>

</style>
<div class="page js_show CURRENT_TPL_NAME">
    <!--    分层的话，头部写在page__hd-->
    <div class="page__hd"></div>
    <!--    主要内容-->
    <div class="page__bd">

    </div>
    <!--    小尾巴-->
    <?php include_once($tpl . "/_common/_taskeasy.tpl.php"); ?>
</div>
<!--弹出层
弹出 fc.showPopup($('#popup1'));
收起 fc.closePopup($(this).parent());
-->
<div id="popup1" class="page js_show weui-popup__modal">
    <div class="page js_show">
        <div class="page__hd">
            <div class="border-b pd-15 fc-text_primary">
                <i class="icon-advice"></i>
                <span style="font-size: 18px; vertical-align: middle;">医嘱变更</span><span
                        style="vertical-align: middle;">（医生给您开了哪些药？）</span>
            </div>
            <ul class="tip-ul pink-bg" style="padding: 5px 15px;">
                <li>请上传医生为您开具的用药医嘱单的照片（比如标题为"XX医院处方笺"、"XX医院服药说明"等的单子）</li>
                <li>若用药医嘱单不慎丢失，您可以将当前的用药情况在下方填写（书写格式：药名、单次剂量、频率、调药规则）</li>
            </ul>
        </div>
        <div class="page__bd border-t" style="padding-bottom: 60px;">
            <form class="J_form" data-type="doctor_advice_change">
                <input type="hidden" name="type" value="doctor_advice_change">
                <input type="hidden" name="patientmedicinecheckid" value="123456">
                <div class="weui-cell border-b" style="padding: 15px 26px 15px 30px;">
                    <div class="weui-cell__bd">
                        <div class="weui-uploader">
                            <div class="weui-uploader__hd">
                                <p class="weui-uploader__title">用药医嘱照片<span
                                            class="fc-text_primary"
                                            style="font-size: 14px;">（请保持字体清晰可见）</span>
                                </p>
                            </div>
                            <div class="weui-uploader__bd">
                                <ul class="weui-uploader__files"
                                    data-picnum="0"
                                    data-must=1
                                    data-title="用药医嘱照片">
                                </ul>
                                <div class="weui-uploader__input-box"
                                     style="background-image: url(<?= $img_uri ?>/static/img/uploadimgbg.png); background-size: 100%;border-style:none;width:60px; height:60px">
                                    <input class="weui-uploader__input J_uploader"
                                           accept="image/*"
                                           name="imgfile"
                                           type="file">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="weui-cell border-b" style="padding: 15px 26px 15px 30px;">
                    <div class="weui-cell__bd">
                        <textarea class="weui-textarea J_form_input J_drug_content" rows="5" id="advice_change_content"
                                  name="advice_change_content"
                                  placeholder="请填写用药医嘱（书写格式：药名、单次剂量、频率、调药规则）"></textarea>
                    </div>
                </div>
                <div class="weui-btn-area push-20-t">
                    <button type="submit" class="fc-btn fc-btn_primary">提交</button>
                </div>
            </form>
        </div>
    </div>
    <a href="javascript:;" class="fc-btn closepopup fc-text_primary border-t">关闭</a>
</div>

<div class="hide" style="display: none;">
    <input id="wx_appid" value="<?= $wxshop->appid ?>"/>
    <input id="wx_timestamp" value="<?= $timestamp ?>"/>
    <input id="wx_signature" value="<?= $jsapi_signature ?>"/>
    <input id="wx_nonceStr" value="<?= WxShop::$nonceStr ?>"/>
</div>

<script>
    $(function () {
        // relative path "fangcunyisheng.com/wwwroot/img/v5/page/wx/base.js"

        // 初始化wx jssdk，已经在footer中初始化了。
//        fc.weixin.init(wx_jssdk_config);


        // 在当前页面显示成功
        // fc.showMsgSuccess({});

        // 成功页面 默认配置
        // var defaults = {
        //     title: "操作成功",  // 标题
        //     desc: "",   // 描述
        //     delay: 0,   // 延时关闭，单位ms
        //     z_index: 2, // z-index，
        //     container: null // 目标容器，默认为body
        // }


        // 关闭微信浏览器
        // delay 延时关闭，单位ms
//        fc.closeWxBroswer(2000);
    })
</script>

<?php include_once($tpl . "/_common/_footer.tpl.php"); ?>

