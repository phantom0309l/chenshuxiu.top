<?php
$pagetitle = "患者住院预约审核详情 BatMsg";
$cssFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170820',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170820',
]; //填写完整地址
$pageStyle = <<<STYLE
    #content {
        width: 500px;
        height: 160px;ss
        border: 1px solid #ddd;
    }
    .liCandaler-top{
        background-color: #f9fdff;
        padding: 8px 5px 8px 10px;
        border: 1px solid #ddd;
        margin: 0px 0 0px 0;
    	font-size: 20px;
    }
    .tab-pane > div {
        display: inline-block;
    }
    .tab-pane img {
        width: 100px;
        height: 100px;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<?php 
    $config_content = json_decode($bedtktconfig->content, true); 
    $extra_info = json_decode($bedtkt->extra_info, true);
?>
<div class="col-md-12">
    <div class="col-sm-12">
    <div class="block">
        <ul class="nav nav-tabs nav-tabs-alt nav-justified">
            <li class="active">
                <a class="" href="javascript:">基本信息</a>
            </li>
            <?php if($config_content['is_zhuyuan_photo_show'] == 1) { ?>
            <li>
                <a href="javascript:">住院证</a>
            </li>
            <?php } ?>
            <?php if ($config_content['is_gangongneng_photo_show'] == 1) { ?>
            <li class="">
                <a href="javascript:">肝肾功</a>
            </li>
            <?php } ?>
            <?php if ($config_content['is_xuechanggui_photo_show'] == 1) { ?>
            <li>
                <a href="javascript:">血常规</a>
            </li>
            <?php } ?>
            <?php if ($config_content['is_xindiantu_show'] == 1) { ?>
            <li>
                <a href="javascript:">心电图</a>
            </li>
            <?php } ?>
            <?php if ($config_content['is_xueshuantanlitu_show'] == 1) { ?>
            <li>
                <a href="javascript:">血栓弹力图</a>
            </li>
            <?php } ?>
            <?php if ($config_content['is_fengshimianyijiancha_show'] == 1) { ?>
            <li>
                <a href="javascript:">风湿免疫检查</a>
            </li>
            <?php } ?>
            <?php if ($config_content['is_shuqianqitajiancha_show'] == 1) { ?>
            <li>
                <a href="javascript:">术前其他检查</a>
            </li>
            <?php } ?>
        </ul>
        <div class="block-content tab-content">
            <div class="tab-pane active">
            <table class="table  table-bordered">
                        <tr>
                            <th>患者姓名</th>
                            <td><?= $bedtkt->patient->name ?></td>
                        </tr>
                        <tr>
                            <th width=150>患者预约日期</th>
                            <td><?= $bedtkt->want_date?></td>
                        </tr>
                        <tr>
                            <th>患者下单日期</th>
                            <td><?= substr($bedtkt->want_date, 0, 10);?></td>
                        </tr>
                        <tr>
                            <th>预约类型</th>
                            <td>
                                <?php
                                    $arr = [
                                        'treat' => '住院预约<span style="color:red">[治疗]</span>',
                                        'checkup' => '住院预约<span style="color:red">[检查]</span>'
                                    ];
                                    echo $arr["{$bedtkt->typestr}"];
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>医保类型</th>
                            <td>
                                <?php
                                    if ($bedtkt->fee_type == 'beijing') {
                                        echo "北京";
                                    } else if ($bedtkt->fee_type == 'notbeijing') {
                                        echo "非北京";
                                    } else {
                                        echo "未知";
                                    }
                                ?>
                            </td>
                        </tr>
                        <!--大量额外字段-->
                        <?php if ($config_content['is_idcard_show']) { ?>
                        <tr>
                            <th>身份证号</th>
                            <td><?= $extra_info['idcard']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($config_content['is_zhuyuanhao_show']) { ?>
                        <tr>
                            <th>住院号</th>
                            <td><?= $extra_info['zhuyuanhao']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($config_content['is_bingshi_show']) { ?>
                        <tr>
                            <th>病史</th>
                            <td><?= $extra_info['bingshi']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($config_content['is_linchuangbiaoxian_show']) { ?>
                        <tr>
                            <th>临床表现</th>
                            <td><?= $extra_info['linchuangbiaoxian']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($config_content['is_otherdisease_show']) { ?>
                        <tr>
                            <th>其他疾病</th>
                            <td><?= $extra_info['otherdisease']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($config_content['is_xingongnengfenji_show']) { ?>
                        <tr>
                            <th>心功能分级</th>
                            <td><?= $extra_info['xingongnengfenji']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($config_content['is_shoushuriqi_show']) { ?>
                        <tr>
                            <th>身份证号</th>
                            <td><?= $extra_info['shoushuriqi']; ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th>预约医生</th>
                            <td><?= $bedtkt->doctor->name ?></td>
                        </tr>
                        <tr>
                            <th>审核状态</th>
                            <td>
                                <?php
                                    $arr = BedTkt::TYPESTR_STATUS;
                                    echo $arr["{$bedtkt->status}"];
                                ?>
                            </td>
                        </tr>
                </table>
            </div>
            <?php if($config_content['is_zhuyuan_photo_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($zhuyuans as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($config_content['is_gangongneng_photo_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($gangongnengs as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($config_content['is_xuechanggui_photo_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($xuechangguis as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($config_content['is_xindiantu_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($xindiantus as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($config_content['is_xueshuantanlitu_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($xueshuantanlitus as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($config_content['is_fengshimianyijiancha_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($fengshimianyijianchas as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($config_content['is_shuqianqitajiancha_show'] == 1) { ?>
            <div class="tab-pane">
                <?php foreach ($shuqianqitajianchas as $one) { ?>
                <div class="push-10-r">
                <img class="img-responsive viewer-toggle"  data-url="<?= $one->getImgUrl() ?>" src="<?=$one->getThumbUrl(100, 100)?>" alt="">
                <p class="push-10-t"><a target="_blank" data-objid="<?=$one->id?>" data-objtype="<?= get_class($one)?>" href="/bedtktmgr/gopatientpicture?objtype=<?=get_class($one)?>&objid=<?=$one->id?>" class="btn btn-xs btn-primary a-guidang">添加到归档图片</a></p>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <!--end of block-->
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function(){
    $(document).on("click", ".nav-tabs>li", function() {
        var me = $(this);
        var index = me.index();
        var tab = me.parent().parent();
        var contents = tab.children(".tab-content").children(".tab-pane");
        me.addClass("active").siblings().removeClass("active");
        contents.eq(index).show().siblings().hide();
    });
     $('.tab-content').viewer({
        inline: false,
        url: 'data-url',
        class: 'viewer-toggle',
        navbar: false,
        scalable: false,
        fullscreen: false,
        shown: function (e) {
        }    
    });
});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
