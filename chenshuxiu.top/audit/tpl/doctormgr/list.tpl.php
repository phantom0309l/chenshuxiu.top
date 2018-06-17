<?php
$pagetitle = "医生列表 Doctors";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <span style="color:red;">创建医生需先搜索医生名，确认医生是否已创建过！</span>
            <div class="searchBar">
                <form class="form-horizontal" action="/doctormgr/list" method="get">
                    <div class="form-group mt10">
                        <label class="control-label col-md-2">医生姓名</label>
                        <div class="col-md-3">
                            <input class="form-control" type="text" name="doctor_name" value="<?= $doctor_name ?>" placeholder="医生姓名模糊搜索" />
                        </div>
                        <label class="control-label col-md-2" style="text-align:left">状态</label>
                        <div class="col-md-3">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorStatusCtrArray(true),"status",$status,'form-control');?>
                        </div>
                    </div>
                    <div class="form-group mt10">
                        <label class="control-label col-md-2">医院</label>
                        <div class="col-md-3">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toHospitalCtrArray($hospitals,true),"hospitalid",$hospitalid,'js-select2 form-control'); ?>
                        </div>
                        <label class="control-label col-md-2" style="text-align:left">市场负责人</label>
                        <div class="col-md-3">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),"auditorid_market",$auditorid_market,'js-select2 form-control');?>
                        </div>
                    </div>
                    <div class="form-group mt10">
                        <label class="control-label col-md-2">省份</label>
                        <div class="col-md-3">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllXprovinceCtrArray(),"xprovinceid",$xprovinceid,'js-select2 form-control');?>
                        </div>
                        <label class="control-label col-md-2" style="text-align:left">城市</label>
                        <div class="col-md-3">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAllXcityByXprovinceidCtrArray($xprovinceid),"xcityid",$xcityid,'js-select2 form-control');?>
                        </div>
                    </div>
                    <div class="form-group mt10">
                        <label class="control-label col-md-2">运营负责人</label>
                        <div class="col-md-3">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getAuditorCtrArray(),"auditorid_yunying",$auditorid_yunying,'js-select2 form-control');?>
                        </div>
                        <label class="control-label col-md-2">医生分组</label>
                        <div class="col-md-3">
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorGroupsCtrArray(),"doctorgroupid",$doctorgroupid,'js-select2 form-control');?>
                        </div>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选" />
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>创建日期</td>
                        <td>所属医院</td>
                        <td width="180">疾病</td>
                        <td width="220">服务号/二维码</td>
                        <td>姓名</td>
                        <td>市场/运营责任人</td>
                        <td width="150">所在分组</td>
                        <td width="130">ALK项目</td>
                        <td>code</td>
                        <td>user:username</td>
                        <td>操作</td>
                        <td>状态</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($doctors as $a) {
                ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->hospital->shortname ?></td>
                        <td>
                            <?= $a->getDiseaseNamesStr()?>
                            <a href="/doctorDiseaseRefMgr/list?doctorid=<?= $a->id ?>">修改</a>
                        </td>
                        <td>
                        	<?php
                                $doctorWxShopRefs = $a->getDoctorWxShopRefs();
                                foreach ($doctorWxShopRefs as $i => $ref) {
                                ?>
                                    <div>
                                        <a href="/doctorwxshoprefmgr/list?wxshopid=<?=$ref->wxshopid ?>">
                                            <?= $ref->wxshop->name?>
                                        </a>
                                    </div>
                                <?php
                                }
                            ?>
                            <a href="/doctorWxShopRefMgr/list?doctorid=<?= $a->id ?>">修改</a>
                        </td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->marketauditor->name ?>/<?=$a->yunyingauditor->name?> </td>
                        <td data-doctorid="<?=$a->id?>">
                            <div class="">
                            <input type="hidden" id="current_val-<?=$a->id?>" value="<?=$a->doctorgroupid?>">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorGroupsCtrArray(),"doctorgroupid",$a->doctorgroupid,'form-control select-doctorgroup');?>
                            </div>
                        </td>
                        <td>
                            <?php
                            if ($a->is_alk == 1) {
                                $checked = "checked";
                                $showstr = "Yes";
                            } else {
                                $checked = "";
                                $showstr = "No";
                            }
                            ?>
                            <label class="css-input switch switch-info">
                                <input type="checkbox" <?=$checked?> class="is_alk" data-is_alk="<?=$a->is_alk?>" data-doctorid="<?=$a->id?>">
                                <span></span>
                                <span id="title_is_alk-<?=$a->id?>"><?=$showstr?></span>
                            </label>
                        </td>
                        <td><?= $a->code ?></td>
                        <td><?= $a->user->username ?></td>
                        <td width="300">
                            <a class="btn btn-default btn-xs" target="_blank" href="/doctorconfigmgr/overview?doctorid=<?= $a->id ?>"><i class="si si-rocket"></i> 配置</a>
                            <a class="btn btn-default btn-xs" target="_blank" href="/doctormgr/fetchInfo?doctorid=<?= $a->id ?>"><i class="si si-globe"></i> 抓取信息</a>
                            <br />
                            <br />
                            <a class="push-10-r" target="_blank" href="/doctormgr/oneforchangeauditormarket?doctorid=<?= $a->id ?>">变更市场负责人</a>
                            <a class="push-10-r" target="_blank" href="/wxtemplatemgr/send?doctorid=<?= $a->id ?>">群发消息</a>
                            <a class="push-10-r" target="_blank" href="/doctormedicinerefmgr/list?doctorid=<?= $a->id ?>">医生用药详情</a>
                            <a class="push-10-r" target="_blank" href="/doctormedicinepkgmgr/list?doctorid=<?= $a->id ?>">用药套餐</a>
                            <a class="push-10-r" target="_blank" href="/patientremarktplmgr/list?doctorid=<?= $a->id ?>">症状体征</a>
                            <a class="push-10-r" target="_blank" href="/doctor_hezuomgr/add?doctorid=<?= $a->id ?>">添加合作医生</a>
                            <a class="push-10-r" target="_blank" href="/dc_doctorprojectmgr/list?doctorid=<?= $a->id ?>">医生项目</a>
                        </td>
                        <td><?= $a->status ? '开通' : '未开通' ?></td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=12>
<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="searchBar <?= $doctor_name ? "" : "hidden" ?>" style="border: none">
                <a class="btn btn-success" href="/doctormgr/add">医生新建</a>
            </div>
        </section>
    </div>

    <div class="clear"></div>

<?php
$footerScript = <<<XXX
$(function() {
    $('.is_alk').on('click', function(){
        var me = $(this);

        var is_alk = me.data('is_alk');
        var doctorid = me.data('doctorid');

        var showstr = "";
        if (is_alk == 1) {
            is_alk = 0;
            showstr = "No";
        } else if(is_alk == 0) {
            is_alk = 1;
            showstr = "Yes";
        } else {
            alert("数据错误");
            return false;
        }

        $.ajax({
            url: '/doctormgr/changeis_alkJson',
            type: 'get',
            dataType: 'text',
            async: false,
            data: {
                doctorid: doctorid,
                is_alk: is_alk
            },
            "success": function (data) {
                if (data == 'success') {
                    me.data('is_alk', is_alk);
                    $("#title_is_alk-" + doctorid).text(showstr);
                }
            }
        });
    });

    App.initHelper('select2');

    $(".select-doctorgroup").on('change', function () {
        var me = $(this);
        var doctorgroupid = me.val();
        var doctorid = me.parents('td').data('doctorid');

        if (false == confirm("确定修改分组吗?")) {
            var last = $("#current_val-" + doctorid).val();
            me.val(last);

            return false;
        }

        $.ajax({
            url: '/doctormgr/modifydoctorgroupjson',
            type: 'get',
            dataType: 'text',
            data: {
                doctorid: doctorid,
                doctorgroupid: doctorgroupid
            },
            "success": function (data) {
                if (data == 'ok') {
                    if (doctorgroupid == 0) {
                        alert("成功移出分组");
                    } else {
                        alert("修改成功");
                    }
                }
            }
        });
    });

    $("#xprovinceid").on('change', function(){
        var me = $(this);

        var xprovinceid = me.val();

        $.ajax({
            "type" : "get",
            "data" : {
                xprovinceid : xprovinceid
            },
            "dataType" : "json",
            "url" : "/xcitymgr/getxcitys",
            "success" : function(data) {
                var htmlstr = "";
                $.each(data['data'], function (index, info) {
                    htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                });

                $("#xcityid").html(htmlstr);
                $("#select2-xcityid-container").html("");
            }
        });
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
