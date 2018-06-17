<?php
$pagetitle = "基本信息修改";
$sideBarMini = true;
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css"]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/jquery-validation/jquery.validate.min.js",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/page/audit/doctormgr/modify/modify.js"]; // 填写完整地址
$pageStyle = <<<STYLE
.div-static-info {
    margin-bottom: 20px;
    border-bottom: 1px dashed #eee;
}
.table-static-info td {
    padding-left:0 !important;
    border: 0 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    font-weight: 500;
    color: #333;
}
.select2-container--default .select2-selection--multiple {
    border: 1px solid #e6e6e6;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border: solid #ccc 1px;
    outline: 0;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php";?>
    <div class="content-div">
        <section class="col-md-12">
            <form class="js-validation-bootstrap form-horizontal" action="/doctormgr/modifypost" method="post">
                <input type="hidden" name="doctorid" value="<?= $doctor->id ?>" />
                <!--基本信息-->
                <div class="col-md-12 col-xs-12 remove-padding">
                    <div class="block block block-bordered" id="basicinfo">
                        <div class="block-header bg-gray-lighter">
                            <h3 class="block-title">Doctor</h3>
                        </div>
                        <div class="block-content">
                            <div class="col-md-12 col-xs-12 clearfloat">
                                <div class="div-static-info">
                                    <div class="table-responsive">
                                        <table class="table border-white-op table-static-info">
                                        <tr>
                                            <td>医生ID</td>
                                            <td><?=$doctor->id?></td>
                                        </tr>
                                        <tr>
                                            <td>创建时间</td>
                                            <td><?=$doctor->createtime?></td>
                                        </tr>
                                        <tr>
                                            <td>code</td>
                                            <td><?=$doctor->code?><span class="text-danger">（一旦使用，不能修改，如需修改请联系老史）</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>头像</td>
                                            <td>
                                                <?php if($doctor->headimg_picture instanceof Picture){ ?>
                                                    <img src="<?= $doctor->headimg_picture->getSrc(100, 100); ?>" />
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-name">
                                        姓名
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" id="val-name" name="name" placeholder="请输入医生姓名" value="<?=$doctor->name?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400">
                                        性别
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <label class="css-input css-radio css-radio-warning push-10-r">
                                            <input type="radio" name="gender" value="1" <?php if ($doctor->sex == 1) {?> checked="" <?php }?>>
                                            <span></span>
                                            男
                                        </label>
                                        <label class="css-input css-radio css-radio-warning">
                                            <input type="radio" name="gender" value="2" <?php if ($doctor->sex==2){?> checked="" <?php }?>>
                                            <span></span>
                                            女
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-title">
                                        职称
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" id="val-title" name="title" placeholder="请输入职称" value="<?=$doctor->title?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-hospital">
                                        医院
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-6">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getHospitalCtrArray(), 'hospitalid', $doctor->hospitalid, 'js-select2 form-control'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-department">
                                        科室
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" id="val-department" name="department" placeholder="请输入医生所属科室" value="<?=$doctor->department?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-mobile">
                                        手机号
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" id="val-mobile" name="mobile" placeholder="请输入医生手机号" value="<?=$doctor->mobile?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-mobile">
                                        简介
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" id="example-textarea-input" name="brief" rows="6" cols="80" placeholder="请输入医生简介"><?=$doctor->brief?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-mobile">
                                        擅长
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" id="example-textarea-input" name="be_good_at" rows="6" cols="80" placeholder="请输入医生擅长"><?=$doctor->be_good_at?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/end of block-content-->
                    </div>
                    <!--/end of block-->
                    <!--疾病-->
                    <div class="block block block-bordered" id="disease">
                        <div class="block-header bg-gray-lighter">
                            <h3 class="block-title">疾病</h3>
                        </div>
                        <div class="block-content">
                            <div class="col-md-9 col-xs-12 clearfloat">
                                <div class="form-group">
                            <?php
                            $default_diseaseid = 0;
                            $i = 0;
                            $disease_arr = array();
                            foreach (CtrHelper::getDiseaseCtrArray(false) as $id => $value) {
                                $wxshop = WxShopDao::getByDiseaseid($id);
                                if ($wxshop instanceof WxShop) {
                                    if ($i == 0) {
                                        $default_diseaseid = $id;
                                    }
                                    $i ++;
                                    $disease_arr[$id] = $value;
                                }
                            }
                            ?>
                                    <label class="col-xs-12 font-w400">
                                        疾病
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-12">
                                    <?php foreach ($disease_arr as $id => $a) { ?>
                                        <label class="css-input css-checkbox css-checkbox-success">
                                            <input type="checkbox" name="diseaseids[]" value="<?=$id?>" <?php if(in_array($id, $doctorDiseaseIds)) { ?> checked="" <?php } ?>>
                                            <span></span> <?=$a?>
                                        </label>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/end of block-content-->
                    </div>
                    <!--/end of block-->
                    <!--User-->
                    <div class="block block block-bordered" id="user">
                        <div class="block-header bg-gray-lighter">
                            <h3 class="block-title">User</h3>
                        </div>
                        <div class="block-content">
                            <div class="col-md-12 col-xs-12 clearfloat">
<?php if ($myauditor->isSuperman()) { ?>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-name">
                                        登录名
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="text" id="val-name" name="username" placeholder="请输入登录用户名" value="<?=$doctor->user->username?>">
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-danger">慎重修改！</span>
                                    </div>
                                </div>
<?php } else { ?>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-name">
                                        登录名
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="form-control-static"><?=$doctor->user->username?></div>
                                    </div>
                                </div>
<?php } ?>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-password">
                                        密码
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input readonly="" class="form-control" type="text" id="val-password" placeholder="">
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-danger">密码不能修改</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/end of block-content-->
                    </div>
                    <!--/end of block-->
                    <!--负责人&状态-->
                    <div class="block block block-bordered" id="market">
                        <div class="block-header bg-gray-lighter">
                            <h3 class="block-title">负责人&状态</h3>
                        </div>
                        <div class="block-content">
                            <div class="col-md-12 col-xs-12 clearfloat">
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-status">
                                        状态
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <label class="css-input css-radio css-radio-warning push-10-r">
                                            <input type="radio" name="status" value="1" <?= $doctor->status == 1 ? "checked" : "" ?>/>
                                            <span></span>
                                            开通
                                        </label>
                                        <label class="css-input css-radio css-radio-warning">
                                            <input type="radio" name="status" value="0" <?= $doctor->status == 0 ? "checked" : "" ?>/>
                                            <span></span>
                                            未开通
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-market">
                                        主管
                                    </label>
                                    <div class="col-md-9">
                            <?php echo HtmlCtr::getMultiSelectCtrImp($relatedDoctorArr,'superior_doctorids[]',$doctor->getSuperiorDoctorIds(), "js-select2 form-control doctor-superior"); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-market">
                                        市场负责人
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),'auditorid_market',$doctor->auditorid_market, "js-select2 form-control"); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-audit">
                                        运营负责人
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),'auditorid_yunying',$doctor->auditorid_yunying, "js-select2 form-control"); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/end of block-content-->
                    </div>
                    <!--/end of block-->

                    <!--开药门诊-->
                    <div class="block block block-bordered" id="medicine">
                        <div class="block-header bg-gray-lighter">
                            <h3 class="block-title">开药门诊</h3>
                        </div>
                        <div class="block-content">
                            <div class="col-md-12 col-xs-12 clearfloat">
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400">
                                        开药门诊
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-3">
                                        <input class="form-control" type="text" name="menzhen_offset_daycnt" placeholder="请输入n" value="<?=$doctor->menzhen_offset_daycnt?>">
                                    </div>
                                    <div class="col-md-9">
                                        <span class="text-warning">医生允许患者报到n天后开启门诊，值为0时，表示永不开启</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400">
                                        门诊开通时间
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-3">
                                        <input class="form-control calendar" type="text" name="menzhen_pass_date" placeholder="请输入门诊开通时间" value="<?=$doctor->menzhen_pass_date?>">
                                    </div>
                                    <div class="col-md-9">
                                        <span class="text-warning"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-status">
                                        延伸处方(续方)审核
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <label class="css-input css-radio css-radio-warning push-10-r">
                                            <input type="radio" name="is_audit_chufang" value="1" <?= $doctor->is_audit_chufang == 1 ? "checked" : "" ?> />
                                            <span></span>
                                            开通
                                        </label>
                                        <label class="css-input css-radio css-radio-warning">
                                            <input type="radio" name="is_audit_chufang" value="0" <?= $doctor->is_audit_chufang == 0 ? "checked" : "" ?>/>
                                            <span></span>
                                            未开通
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400" for="val-status">
                                        签约
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <label class="css-input css-radio css-radio-warning push-10-r">
                                            <input type="radio" name="is_sign" value="1" <?= $doctor->is_sign == 1 ? "checked" : "" ?> />
                                            <span></span>
                                            已签约
                                        </label>
                                        <label class="css-input css-radio css-radio-warning">
                                            <input type="radio" name="is_sign" value="0" <?= $doctor->is_sign == 0 ? "checked" : "" ?>/>
                                            <span></span>
                                            未签约
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 font-w400">
                                        绑定药品
                                        <span class="text-white">*</span>
                                    </label>
                                    <div class="col-md-3">
                                        <a href="/doctorshopproductrefmgr/binddoctor?doctorid=<?= $doctor->id ?>" target="_blank">去绑定</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/end of block-content-->
                    </div>
                    <!--/end of block-->

                <!--其他-->
                <div class="block block block-bordered" id="other">
                    <div class="block-header bg-gray-lighter">
                        <h3 class="block-title">其他</h3>
                    </div>
                    <div class="block-content">
                        <div class="col-md-12 col-xs-12 clearfloat">
                            <div class="form-group">
                                <label class="col-xs-12 font-w400" for="val-name">
                                    对应[科室医生id]
                                    <span class="text-white">*</span>
                                </label>
                                <div class="col-md-9">
                                    <input class="form-control" type="text" id="val-name" name="pdoctorid" placeholder="请输入科室医生id" value="<?=$doctor->pdoctorid?>">
                                </div>
                                <div class="col-md-3">
                                    <span class="text-warning">不知道可以不填写</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 font-w400" for="val-name">
                                    关联doctorid
                                    <span class="text-white">*</span>
                                </label>
                                <div class="col-md-9">
                                    <input class="form-control" type="text" id="val-name" name="patients_referencing" placeholder="请输入doctorid" value="<?=$doctor->patients_referencing?>">
                                </div>
                                <div class="col-md-3">
                                    <span class="text-warning">格式：,12,34,56</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 font-w400" for="val-status">
                                    使用APP医患对话功能
                                    <span class="text-white">*</span>
                                </label>
                                <div class="col-md-9">
                                    <label class="css-input css-radio css-radio-warning push-10-r">
                                        <input type="radio" name="module_pushmsg" value="1" <?php if ($doctor->module_pushmsg == 1) {?> checked="" <?php }?>>
                                        <span></span>
                                        是
                                    </label>
                                    <label class="css-input css-radio css-radio-warning">
                                        <input type="radio" name="module_pushmsg" value="0" <?php if ($doctor->module_pushmsg==0){?> checked="" <?php }?>>
                                        <span></span>
                                        否
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 font-w400" for="val-status">
                                    服务备注
                                    <span class="text-white">*</span>
                                </label>
                                <div class="col-md-9">
                                    <label class="css-input switch switch-success">
                                        <textarea class="form-control" id="example-textarea-input" name="service_remark" rows="6" cols="80" placeholder="请输入服务备注内容"><?=$doctor->service_remark?></textarea>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <button class="btn btn-sm btn-primary btn-minw" type="submit">保存</button>
                                </div>
                            </div>
                        </div><!--end of clearfloat -->
                    </div>
                    <!--/end of block-content-->
                </div>
                <!--/end of block-->
            </div>
            </form>
            <div>
            <?php
            $pagetitle = "医生操作记录(todo)";
            include $tpl . "/_pagetitle.php";
            ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>类型</th>
                            <th>内容</th>
                            <th>操作人</th>
                            <th>操作时间</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php foreach( $comments as $a){?>
                        <tr>
                            <td><?= $a->typestr ?></td>
                            <td><?= $a->title ?></td>
                            <td><?= ($a->user instanceof User) ? $a->user->getAuditor()->name : "--"; ?></td>
                            <td><?= $a->createtime ?></td>
                        </tr>
                <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
$(function() {
App.initHelper('select2');
$('.doctor-superior').select2({
    multiple: true,
    allowClear: true,
    placeholder: {
        id: '-1', // the value of the option
        text: '请选择主管医生'
    }
});
});
SCRIPT;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
