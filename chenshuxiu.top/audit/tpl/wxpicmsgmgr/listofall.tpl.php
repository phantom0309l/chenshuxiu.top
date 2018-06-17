<?php
$pagetitle = "库存列表";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js"
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .control-label{ width:65px; text-align:left; padding-right:0px;}
.searchBar{ border-radius:3px;}
STYLE;
$pageScript = <<<SCRIPT
$(function(){
    $('.js-select2').select2();
})
SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php';
?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="text-align:left; background:#fafafa;" class="searchBar clearfix">
            <form action="/wxpicmsgmgr/listofall" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">疾病：</label>
                    <div class="col-sm-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(),"diseaseid",$diseaseid,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <input type="submit" value="筛选" class="btn btn-primary btn-block"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
            <tbody>
                <?php
                foreach ($wxpicmsgs as $i => $a) {
                    ?>
                <tr>
                    <td>
                        <p>
                            <span><?= $i + 1 ?></span>
                            <span><?= $a->wxuser->nickname ?></span>
                            <span><?= $a->patient->name ?></span>
                            <span><?= $a->patient->doctor->name ?></span>
                        </p>
                        <?php if($a->picture instanceof Picture){ ?>
                            <p>
                                <img src="<?= $a->picture->getSrc(600,600)?>"/>
                            </p>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
        </table>
        </div>
        <?php if($pagelink){ include $dtpl . "/pagelink.ctr.php"; } ?>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
