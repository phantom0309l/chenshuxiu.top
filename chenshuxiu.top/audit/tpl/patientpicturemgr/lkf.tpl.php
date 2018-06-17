<?php
$pagetitle = "图片归档 肝肾功专用";
$cssFiles = [
    $img_uri . "/v5/page/audit/patientpicturemgr/one/one.css?v=20170113",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170829',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/page/audit/patientpicturemgr/one/one.js?v=20170822",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170829',
]; //填写完整地址
$pageStyle = <<<STYLE
#main-container {
    background: #f5f5f5 !important;
}
#picture-box {
    overflow: hidden;
}
.myform label {
    font-weight: 500;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<?php $patient = $patientpicture->patient; ?>
    <div class="col-md-12" id="top">
        <section class="col-md-6 content-left">
            <div id="picture-box" style="background:#ccc;width:100%;max-height:800px;">
            <img class="img-big" src="<?= $patientpicture->obj->picture->getSrc() ?>" data-url="<?= $patientpicture->obj->picture->getSrc() ?>">
            </div>
        </section>
        <section class="col-md-6">
            <div class="block">
                <div class="block-header">
                    <h3 class="block-title"><?=$patient->name?></h3>
                </div>
                <div class="block-content">
                    <p>
                        图片来源：<?= $patientpicture->getSourceDesc()?>
                        <span id="changestatusBox">[<?= $patientpicture->getStatusDesc()?>]</span>
                    </p>
                </div>
            </div>
            <?php
            $typestr_code = $patientpicture->getTypeCode();
            $typestr_code_url_arr = array(
                "WxPicMsg" => array('title'=>"非检查报告","url"=>"/patientpicturemgr/changeobjpost?patientpictureid={$patientpicture->id}&objtype=WxPicMsg"),
                "CheckupPicture" => array('title'=>"检查报告","url"=>"/patientpicturemgr/changeobjpost?patientpictureid={$patientpicture->id}&objtype=CheckupPicture"),
                "wbc" => array('title'=>"血常规专用","url"=>"/patientpicturemgr/wbc?patientpictureid={$patientpicture->id}"),
                "lkf" => array('title'=>"肝肾功专用","url"=>"/patientpicturemgr/lkf?patientpictureid={$patientpicture->id}"),
            );
            ?>
            <div class="block" id="pp_objtype" data-objtype="<?= $typestr_code ?>">
                <ul class="nav nav-tabs nav-tabs-alt">
                <?php foreach( $typestr_code_url_arr as $k => $arr ){?>
                    <?php if( $patientpicture->source_type == 'CheckupPicture' && $k == 'WxPicMsg'){
                        continue;
                    }?>
                    <?php if( "lkf" == $k ){?>
                        <li class="active">
                            <a href="#btabs-alt-static-home"><?=$arr['title']?></a>
                        </li>
                    <?php }else{?>
                        <li class="">
                            <a href="<?=$arr['url']?>"><?=$arr['title']?></a>
                        </li>
                    <?php }?>
                <?php }?>
                <li class="pull-right">
                    <a href="/patientpicturemgr/changestatuspost?patientpictureid=<?=$patientpicture->id?>&status=2"><i class="si si-ban"></i> 无意义</a>
                </li>
                </ul>
                <div class="block-content tab-content">
                    <form action="/patientpicturemgr/lkfpost" method="post" class="form-horizontal myform">
                        <input type="hidden" name="patientpictureid" value="<?= $patientpicture->id ?>" />
                        <div class="form-group">
                            <label class="col-xs-10">日期</label>
                            <div class="col-xs-10">
                                <input type="text" name="thedate" class="calendar form-control" readonly value="<?= $thedate ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-10">ALT</label>
                            <div class="col-xs-10">
                                <input class="form-control" type="number" name="lkf_alt" step="0.001" value="<?= $lkf_alt ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-10">ALP</label>
                            <div class="col-xs-10">
                                <input class="form-control" type="number" name="lkf_alp" step="0.001" value="<?= $lkf_alp ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-10">TBIL</label>
                            <div class="col-xs-10">
                                <input class="form-control" type="number" name="lkf_tbil" step="0.001" value="<?= $lkf_tbil ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-10">Cr</label>
                            <div class="col-xs-10">
                                <input class="form-control" type="number" name="lkf_cr" step="0.001" value="<?= $lkf_cr ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-10">
                                <button class="btn btn-success btn-sm btn-minw" >提交</button>
                            </div>
                        </div>
                    </form>
                    <p></p>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
$(function(){
    $('.img-big').viewer({
        inline: true,
        url: 'data-url',
        navbar: false,
        scalable: false,
        fullscreen: false,
        shown: function (e) {
        },
    });
});
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
