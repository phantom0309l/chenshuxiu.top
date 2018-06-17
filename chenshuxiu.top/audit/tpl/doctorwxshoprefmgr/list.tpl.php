<?php
$pagetitle = "医生-服务号-关系-列表 of ";
if ($doctor instanceof Doctor) {
    $pagetitle .= "医生 ( {$doctor->name} )";
} elseif ($wxshop instanceof WxShop) {
    $pagetitle .= "服务号 ( {$wxshop->name} )";
} else {
    $pagetitle .= " ALL";
}
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width=20>#</th>
                        <th width=100>id</th>
                        <th>服务号</th>
                        <th>医生</th>
                        <th>二维码</th>
                        <th>二维码类型</th>
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
                            <a href="/doctorwxshoprefmgr/list?wxshopid=<?=$ref->wxshopid ?>"><?=$ref->wxshopid?>
                            <?= $ref->wxshop->name?>
                            <span class="gray">(<?= $ref->wxshop->diseaseid ?> <?= $ref->wxshop->disease->name ?>)</span>
                            </a>
                        </td>
                        <td>
                            <a href="/doctorwxshoprefmgr/list?doctorid=<?=$ref->doctorid ?>"><?=$ref->doctorid ?> <?= $ref->doctor->name ?></a>
                        </td>
                        <td align="center">
                            <a href="<?= $ref->getQrUrl4Tpl() ?>" target="_blank">
                                <img width="100" src="<?= $ref->getQrUrl4Tpl() ?>" />
                                <br />
                                点击打开图片
                            </a>
                        </td>
                        <td>
                        <?= $ref->isDefaultQr() ? '默认' : "专属疾病：{$ref->disease->name}"?>
                    </td>
                        <td>
                        <?php if($ref->isDefaultQr()){ ?>
                            <a href="/doctorwxshoprefmgr/addOnlyOnedisease?doctorwxshoprefid=<?=$ref->id ?>" class="btn btn-default">添加疾病专属二维码</a>
                            <br />
                        <?php } ?>
                        <a href="/doctorwxshoprefmgr/deletepost?doctorwxshoprefid=<?=$ref->id ?>">删除</a>
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
            <span class="blue f16"> 补充关联服务号 (新医生只需要绑定一个服务号!!!)</span>
            <div class="mt10">
                <?php if(count($notBind_wxshops) > 0) {  ?>
                    <form action="/doctorwxshoprefmgr/addpost">
                    <input type="hidden" name="doctorid" value="<?=$doctor->id ?>">
                        <?=HtmlCtr::getCheckboxCtrImp(CtrHelper::toWxShopCtrArray($notBind_wxshops, false, true), 'wxshopids[]');?>
                        <br />
                    <input class="btn btn-success" type="submit" title="提交" />
                </form> <?php }else{ echo "已全部绑定完毕!"; }?>
                </div>
        </div>
        <div class="border1 p10 mt10">
            <span><?=$doctor->name ?></span>
            <span class="f16 blue"> 已关联疾病: </span>
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
            <a class="btn btn-success" href="/doctorDiseaseRefMgr/list?doctorid=<?= $doctor->id ?>">修改关联疾病</a>
        </div>
            <?php
            }
            ?>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
