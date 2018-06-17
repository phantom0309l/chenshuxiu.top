<?php
$pagetitle = "医生二维码管理";
$sideBarMini = true;
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageScript = <<<SCRIPT
$(function(){
    $(document).on('click', '#wx-delete', function() {
        if (!confirm("确定要删除吗？")) {
            return false;
        }
        location.href = $(this).data('href');
    });
})
SCRIPT;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <?php include_once $tpl . "/doctorconfigmgr/_menu.tpl.php";?>
    <div class="content-div">
        <section class="col-md-12">
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table table-bordered table-triped">
                        <thead>
                            <tr>
                                <th width=20>#</th>
                                <th width=100>id</th>
                                <th>服务号</th>
                                <th>二维码</th>
                                <th>二维码类型</th>
                                <th>下载</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php
            foreach ($doctorWxShopRefs as $i => $ref) {
                ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= $ref->id ?></td>
                                <td>
                                <?=$ref->wxshopid?>
                                <?= $ref->wxshop->name?>
                                <span class="gray">(<?= $ref->wxshop->diseaseid ?> <?= $ref->wxshop->disease->name ?>)</span>
                                </td>
                                <td align="center">
                                    <a href="<?= $ref->getQrUrl() ?>" target="_blank">
                                        <img width="100" src="<?= $ref->getQrUrl() ?>" />
                                        <br />
                                        点击查看大图
                                    </a>
                                </td>
                                <td>
                        <?= $ref->isDefaultQr() ? '默认' : "专属疾病：{$ref->disease->name}"?>
                            </td>
                                <td>
                        <?php if($ref->wxshopid == 1){ ?>
                                <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_zhuopai_heng?doctorwxshoprefid=<?=$ref->id ?>">adhd-桌牌-横</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_zhuopai_shu?doctorwxshoprefid=<?=$ref->id ?>">adhd-桌牌-竖</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_namecard_front_green?doctorwxshoprefid=<?=$ref->id ?>">adhd-名片-正面-绿色</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_namecard_back_green?doctorwxshoprefid=<?=$ref->id ?>">adhd-名片-背面-绿色</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_namecard_back_green_fix?doctorwxshoprefid=<?=$ref->id ?>">adhd-名片-背面-绿色(复杂)</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_namecard_front_blue?doctorwxshoprefid=<?=$ref->id ?>">adhd-名片-正面-蓝色</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/adhd_namecard_back_blue?doctorwxshoprefid=<?=$ref->id ?>">adhd-名片-背面-蓝色</a>
                        <?php }else{ ?>
                                <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/zhuopai_shu?doctorwxshoprefid=<?=$ref->id ?>">桌牌-竖</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/namecard_front?doctorwxshoprefid=<?=$ref->id ?>">绿色-名片-正面</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/namecard_back?doctorwxshoprefid=<?=$ref->id ?>">绿色-名片-背面</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/namecard2_front?doctorwxshoprefid=<?=$ref->id ?>">名片2-正面</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/namecard2_back?doctorwxshoprefid=<?=$ref->id ?>">名片2-背面</a>
                                    <br />
                                    <a target="_blank" href="/doctorwxshoprefmgr/namecard2_back_cancer?doctorwxshoprefid=<?=$ref->id ?>">名片2-背面-肿瘤</a>
                        <?php } ?>
                            </td>
                                <td>
                                    <p>
                                        <button type="button" class="btn btn-default btn-xs" data-formaction="/doctorwxshoprefmgr/addonlyonediseasepost" data-doctorwxshoprefid="<?=$ref->id?>" data-title="<?php echo $ref->wxshop->name . ' '. $ref->wxshop->disease->name ?>"
                                            data-target="#modal-addqrcode" data-toggle="modal"
                                        >
                                            <i class="fa fa-plus"></i>
                                            二维码
                                        </button>
                                    </p>
                                    <p>
                                        <a class="btn btn-danger btn-xs" id="wx-delete" data-href="/doctorwxshoprefmgr/deletepost?doctorwxshoprefid=<?=$ref->id ?>" href="javascript:">删除</a>
                                    </p>
                                </td>
                            </tr>
            <?php } ?>
                    </tbody>
                    </table>
                </div>
            <?php
            if ($doctor instanceof Doctor) {
                ?>
                <div class="border1 p10">
                    <span><?=$doctor->name ?></span>
                    <span class="f16 text-warning"> 补充关联服务号 (新医生只需要绑定一个服务号!!!)</span>
                    <div class="mt10">
                <?php if(count($notBind_wxshops) > 0) {  ?>
                        <form action="/doctorwxshoprefmgr/addpost">
                            <input type="hidden" name="doctorid" value="<?=$doctor->id ?>">
                            <?php foreach ($notBind_wxshops as $k => $a) { ?>
                            <label class="css-input css-checkbox css-checkbox-success">
                                <input type="checkbox" name="wxshopids[]" value="<?=$a->id?>">
                                <span></span> <?=$a->name?>(<?=$a->disease->name?>)
                            </label>
                            <br />
                            <?php } ?>
                            <input class="btn btn-success" type="submit" title="提交" />
                        </form> <?php }else{ echo "已全部绑定完毕!"; }?>
                    </div>
                </div>
                <div class="border1 p10 mt10">
                    <span><?=$doctor->name ?></span>
                    <span class="f16 text-warning"> 已关联疾病: </span>
                    <br />
                    <br />
                    <span>
                <?php
                foreach ($doctor->getDoctorDiseaseRefs() as $doctorDiseaseRef) {
                    echo $doctorDiseaseRef->disease->name;
                    echo "<br/>";
                }
                ?>
                    </span>
                    <br />
                    <a class="btn btn-success" href="/doctormgr/modify?doctorid=<?= $doctor->id ?>">修改关联疾病</a>
                </div>
            <?php
            }
            ?>
            </div>
        </section>
    </div>
</div>
<div class="modal" id="modal-addqrcode" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button">
                                <i class="si si-close"></i>
                            </button>
                        </li>
                    </ul>
                    <h3 class="block-title">添加疾病专属二维码</h3>
                </div>
                <div class="block-content">
                    <form class="form form-horizontal" id="modal-form">
                        <p id="title-addqrcode"></p>
                        <input type="hidden" id="doctorwxshoprefid" name="doctorwxshoprefid" value="">
                        <input type="hidden" id="frompage" name="frompage" value="doctorconfig">
                        <div class="form-group">
                            <label class="control-label col-md-3 font-w400 text-left" style="width: 60px; text-align: left">疾病</label>
                            <div class="col-md-9">
                                <?php foreach($doctordiseaserefs as $a){ ?>
                                <label class="css-input css-radio css-radio-info push-10-r">
                                    <input type="radio" name="diseaseid" value="<?=$a->diseaseid?>">
                                    <span></span> <?=$a->disease->name?>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button id="modal-save" class="btn btn-sm btn-primary" type="button" data-dismiss="modal">
                    <i class="fa fa-check"></i>
                    保存
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
$(function(){
$('#modal-addqrcode').on('show.bs.modal', function(e) {
    var button = $(e.relatedTarget)
    var id = button.data('doctorwxshoprefid');
    var title = button.data('title');
    var action = button.data('formaction');
    $('#doctorwxshoprefid').val(id);
    $('#title-addqrcode').html(title);
    $('#modal-form').attr('action', action);
    $('#modal-save').on('click', function(){
        $('#modal-form').submit();
    });
});
});
SCRIPT
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
