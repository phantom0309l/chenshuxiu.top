<?php
$pagetitle = "药物商品修改";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
        <form action="/medicineproductmgr/modifypost" method="post">
                <input type='hidden' name='medicineproductid' value="<?= $medicineproduct->id ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>sfda_medicineid</th>
                        <td>
                        <?= $medicineproduct->sfda_medicineid?>
                    </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th width='140'>通用名</th>
                        <td>
                            <input style="width: 300px" type="text" name="name_common" value="<?= $medicineproduct->name_common ?>" />
                            *
                        </td>
                        <td>法定名称，如:盐酸托莫西汀胶囊</td>
                    </tr>
                    <tr>
                        <th>通用名英文</th>
                        <td>
                            <input style="width: 300px" type="text" name="name_common_en" value="<?= $medicineproduct->name_common_en ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>商品名</th>
                        <td>
                            <input type="text" name="name_brand" value="<?= $medicineproduct->name_brand ?>" />
                            *
                        </td>
                        <td>品牌名，如:择思达</td>
                    </tr>
                    <tr>
                        <th>商品名英文</th>
                        <td>
                            <input type="text" name="name_brand_en" value="<?= $medicineproduct->name_brand_en ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>药物</th>
                        <td>
                            <div class="col-md-6">
                                <?= HtmlCtr::getSelectCtrImp(CtrHelper::toMedicineCtrArrayForAudit($medicines),"medicineid",$medicineproduct->medicineid,'js-select2 form-control') ?>
                            </div>
                            <div class="col-md-3">
                                <a href="/medicinemgr/list?medicine_name=<?= $sfda_medicine->name_common ?>" target='_blank'>去新建或修改药物</a>
                            </div>
                        </td>
                        <td>对应的 medicine</td>
                    </tr>
                    <tr>
                        <th>化学名</th>
                        <td>
                            <input type="text" name="name_chem" value="<?= $medicineproduct->name_chem ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>化学名英文</th>
                        <td>
                            <input type="text" name="name_chem_en" value="<?= $medicineproduct->name_chem_en ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>给药途径</th>
                        <td>
                            <input type="text" name="drug_way" value="<?= $medicineproduct->drug_way ?>" />
                            *
                        </td>
                        <td>如:口服、输液等</td>
                    </tr>
                    <tr>
                        <th>单次用药剂量</th>
                        <td>
                            <input type="text" name="drug_dose" value="<?= $medicineproduct->drug_dose ?>" />
                            *
                        </td>
                        <td>如:一次6-7片</td>
                    </tr>
                    <tr>
                        <th>用药频率</th>
                        <td>
                            <input type="text" name="drug_frequency" value="<?= $medicineproduct->drug_frequency ?>" />
                            *
                        </td>
                        <td>如:一日3次</td>
                    </tr>
                    <tr>
                        <th>包装单位</th>
                        <td>
                            <input type="text" name="pack_unit" value="<?= $medicineproduct->pack_unit ?>" />
                            *
                        </td>
                        <td>盒</td>
                    </tr>
                    <tr>
                        <th>图片</th>
                        <td>
                        <?php
                        $picWidth = 150;
                        $picHeight = 150;
                        $pictureInputName = "pictureid";
                        $isCut = false;
                        $picture = $medicineproduct->picture;
                        require_once ("$dtpl/picture.ctr.php");
                        ?>
                    </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>主要原料</th>
                        <td>
                            <textarea rows="2" cols="80" name="yuanliao"><?= $medicineproduct->yuanliao ?></textarea>
                        </td>
                        <td>健客网</td>
                    </tr>
                    <tr>
                        <th>主要作用</th>
                        <td>
                            <textarea rows="3" cols="80" name="zuoyong"><?= $medicineproduct->zuoyong ?></textarea>
                        </td>
                        <td>健客网</td>
                    </tr>
                    <tr>
                        <th>用法用量</th>
                        <td>
                            <textarea rows="3" cols="80" name="yongfa"><?= $medicineproduct->yongfa ?></textarea>
                        </td>
                        <td>健客网</td>
                    </tr>
                    <tr>
                        <th>说明书</th>
                        <td>
                            <textarea rows="4" cols="80" name="content"><?= $medicineproduct->content ?></textarea>
                        </td>
                        <td>健客网</td>
                    </tr>
                    <tr>
                        <th>剂型</th>
                        <td>
                            <input type="text" name="type_jixing" value="<?= $medicineproduct->type_jixing ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>产品类别</th>
                        <td>
                            <input type="text" name="type_chanpin" value="<?= $medicineproduct->type_chanpin ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>单位规格</th>
                        <td>
                            <input type="text" name="size_chengfen" value="<?= $medicineproduct->size_chengfen ?>" />
                        </td>
                        <td>如:100ml:10g</td>
                    </tr>
                    <tr>
                        <th>包装规格</th>
                        <td>
                            <input type="text" name="size_pack" value="<?= $medicineproduct->size_pack ?>" />
                        </td>
                        <td>如:15粒/盒</td>
                    </tr>
                    <tr>
                        <th>批准日期</th>
                        <td>
                            <input type="text" name="pizhun_date" value="<?= $medicineproduct->pizhun_date ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>批准文号</th>
                        <td>
                            <input type="text" name="piwenhao" value="<?= $medicineproduct->piwenhao ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>药品本位码</th>
                        <td>
                            <input type="text" name="benweima" value="<?= $medicineproduct->benweima ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>生产单位</th>
                        <td>
                            <input style="width: 300px" type="text" name="company_name" value="<?= $medicineproduct->company_name ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>生产单位英文</th>
                        <td>
                            <input type="text" name="company_name_en" value="<?= $medicineproduct->company_name_en ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>状态</th>
                        <td>
                        <?= HtmlCtr::getRadioCtrImp([0=>"下线",1=>"上线"],"status",$medicineproduct->status," ");?>
                    </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>运营备注</th>
                        <td>
                            <input type="text" name="remark" value="<?= $medicineproduct->remark ?>" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交" />
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
$footerScript = <<<XXX
$(function() {
    App.initHelper('select2');
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
