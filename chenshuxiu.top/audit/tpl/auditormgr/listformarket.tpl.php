<?php
$pagetitle = "市场列表";
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
            <form action="/auditormgr/listformarket" class="form-horizontal shopOrderForm">
                <input name="diseaseid" value="<?= $mydisease->id ?>" type="hidden"/>
                <div class="form-group">
                    <label class="col-md-2 control-label">疾病组:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseGroupCtrArray(),'diseasegroupid',$diseasegroupid,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">推荐人:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(),'auditorid_prev',$auditorid_prev,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">管辖省:</label>
                    <div class="col-md-2">
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::getAllXprovinceCtrArray(),"xprovinceid_control",$xprovinceid_control,'js-select2 form-control') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">达标 :</label>
                    <div class="col-md-6">
                        <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getYesNoCtrArray(),'is_standard', $is_standard, 'css-radio-success')?>
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
            <thead>
                <tr>
                    <td>ID</td>
                    <td>姓名</td>
                    <td>创建日期</td>
                    <td>管辖省</td>
                    <td>推荐人</td>
                    <td>本月订单</td>
                    <td>本月金额</td>
                    <td>上月订单</td>
                    <td>上月金额</td>
                    <td>管辖医生</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($auditors as $i => $a) {
                    ?>
                <tr>
                    <td>
                        <?= $a->id ?>
                    </td>
                    <td><?= $a->name ?></td>
                    <td><?= substr($a->createtime, 0, 10) ?></td>
                    <td><?= $a->controlxprovince->name ?></td>
                    <td><?= $a->prevauditor->name ?></td>
                    <td>
                        <?php
                            $themonth = date("Y-m");
                            $themonth_last = date("Y-m", strtotime(date("Y-m-01") . '-1 month'));
                            $cnt = 0;
                            $amount =  0;
                            $cnt_last = 0;
                            $amount_last =  0;
                            $auditor_arr = $results[$a->id];
                            if(!empty($auditor_arr)){
                                $themonth_arr = $auditor_arr[$themonth];
                                if(isset($themonth_arr)){
                                    $cnt = $themonth_arr["cnt"];
                                    $amount = $themonth_arr["amount"];
                                }

                                $themonth_last_arr = $auditor_arr[$themonth_last];
                                if(isset($themonth_last_arr)){
                                    $cnt_last = $themonth_last_arr["cnt"];
                                    $amount_last = $themonth_last_arr["amount"];
                                }
                            }
                        ?>
                        <?= $cnt ?>
                    </td>
                    <td>
                        <?= $amount ?>
                    </td>
                    <td>
                        <?= $cnt_last ?>
                    </td>
                    <td>
                        <?= $amount_last ?>
                    </td>
                    <td>
                        <a target="_blank" href="/doctormgr/list?auditorid_market=<?=$a->id ?>">查看</a>
                    </td>
                    <td align="center">
                        <a target="_blank" href="/auditormgr/modify?auditorid=<?=$a->id ?>">修改</a>
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

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
