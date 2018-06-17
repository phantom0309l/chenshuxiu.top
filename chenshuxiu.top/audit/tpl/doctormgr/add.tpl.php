<?php
$pagetitle = "医生新建";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/jquery-validation/jquery.validate.min.js",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/page/audit/doctormgr/add/add.js",
]; //填写完整地址
$pageStyle = <<<STYLE
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
    <section class="col-md-12">
        <form class="js-validation-bootstrap form-horizontal" action="/doctormgr/addpost" method="post">
            <!--基本信息-->
            <div class="block block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">Doctor</h3>
                </div>
                <div class="block-content">
                    <div style="width:50%;">
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-name">姓名 <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" id="val-name" name="name" placeholder="请输入医生姓名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400">性别 <span class="text-white">*</span></label>
                            <div class="col-md-9">
                                <label class="css-input css-radio css-radio-warning push-10-r">
                                    <input type="radio" name="gender" value="1"><span></span> 男
                                </label>
                                <label class="css-input css-radio css-radio-warning">
                                    <input type="radio" name="gender" value="2"><span></span> 女
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-title">职称</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" id="val-title" name="title" placeholder="请输入职称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-hospital">医院 <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getHospitalCtrArray(), 'hospitalid', $hospitalid, 'js-select2 form-control'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-department">科室 <span class="text-white">*</span></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" id="val-department" name="department" placeholder="请输入医生所属科室">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-mobile">手机号 <span class="text-white">*</span></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" id="val-mobile" name="mobile" placeholder="请输入医生手机号">
                            </div>
                        </div>
                    </div>
                </div><!--/end of block-content-->
            </div><!--/end of block-->
            <!--疾病-->
            <div class="block block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">疾病</h3>
                </div>
                <div class="block-content">
                    <div style="width:80%;">
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
                                    $i++;
                                    $disease_arr[$id] = $value;
                                }
                            }
                            ?>
                            <label class="col-xs-12 font-w400">疾病 <span class="text-danger">*</span></label>
                            <div class="col-md-12">
                                <?php foreach ($disease_arr as $id => $a) { ?>
                                    <label class="css-input css-checkbox css-checkbox-success">
                                        <input type="checkbox" name="diseaseids[]" value="<?= $id ?>"><span></span> <?= $a ?>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div><!--/end of block-content-->
            </div><!--/end of block-->

            <!--User-->
            <div class="block block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">User</h3>
                </div>
                <div class="block-content">
                    <div style="width:60%;">
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-username">登录名 <span class="text-danger">* 规则：先填写医生姓名全拼；如果有重名，填写 地名+医生名 例如：bjzhangsan(北京张三)</span></label>
                            <div class="col-md-7">
                                <input class="form-control" type="text" id="val-username" name="username" placeholder="请输入登录名">
                            </div>
                            <div class="col-md-5">
                                <span class="text-danger">必须医生名全拼! 不能用汉字。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-password">密码 <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input readonly="" class="form-control" type="text" id="val-password" name="password" placeholder="密码会自动生成">
                            </div>
                            <div class="col-md-5">
                                <span class="text-danger">自动为医生创建密码，请在XX处查看密码</span>
                            </div>
                        </div>
                    </div>
                </div><!--/end of block-content-->
            </div><!--/end of block-->
            <!--负责人&状态-->
            <div class="block block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">负责人&状态</h3>
                </div>
                <div class="block-content">
                    <div style="width:50%;">
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-status">状态 <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <label class="css-input css-radio css-radio-warning push-10-r">
                                    <input type="radio" name="status" value="1"><span></span> 开通
                                </label>
                                <label class="css-input css-radio css-radio-warning">
                                    <input type="radio" name="status" value="0"><span></span> 未开通
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-market">
                                主管
                            </label>
                            <div class="col-md-9">
                                <?php echo HtmlCtr::getMultiSelectCtrImp([], 'superior_doctorids[]', [], "js-select2 form-control doctor-superior"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-market">市场负责人 <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(), 'auditorid_market', 0, "js-select2 form-control"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-audit">运营负责人 <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(), 'auditorid_yunying', 0, "js-select2 form-control"); ?>
                            </div>
                        </div>
                    </div>
                </div><!--/end of block-content-->
            </div><!--/end of block-->
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
                                <input class="form-control" type="text" name="menzhen_offset_daycnt" placeholder="请输入数字"
                                       value="<?= $doctor->menzhen_offset_daycnt ?>">
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
                                <input class="form-control calendar" type="text" name="menzhen_pass_date" placeholder="请输入门诊开通时间"
                                       value="<?= $doctor->menzhen_pass_date ?>">
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

                    </div>
                </div>
                <!--/end of block-content-->
            </div>
            <!--/end of block-->
            <div class="block block block-bordered">
                <div class="block-header bg-gray-lighter">
                    <h3 class="block-title">其他</h3>
                </div>
                <div class="block-content">
                    <div style="width:80%;">
                        <div class="form-group">
                            <label class="col-xs-12 font-w400" for="val-status">服务备注 <span class="text-white">*</span></label>
                            <div class="col-md-9">
                                <label class="css-input switch switch-success">
                                    <textarea class="form-control" id="example-textarea-input" name="service_remark" rows="6" cols="80"
                                              placeholder="请输入服务备注内容"></textarea>
                                </label>
                            </div>
                        </div>
                    </div>
                </div><!--/end of block-content-->
            </div><!--/end of block-->
            <div class="form-group">
                <div class="col-xs-12">
                    <button class="btn btn-sm btn-primary btn-minw" type="submit">保存</button>
                </div>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<SCRIPT
$(function() {
    App.initHelper('select2');
    $('.doctor-superior').select2({
        ajax: {
            url: "/doctormgr/searchdoctorjson",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              var diseaseids = [];
              $('input[name="diseaseids[]"]:checked').each(function() {
                diseaseids.push($(this).val());
              });
              return {
                q: params.term, // search term
                diseaseids: diseaseids,
                page: params.page || 1
              };
            },
            processResults: function (data, params) {
              params.page = params.page || 1;

              console.log(data.data)
              return {
                results: data.data.list,
                //pagination: {
                  //more: (params.page * 30) < data.total_count
                //}
              };
            },
            cache: true
        },
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
