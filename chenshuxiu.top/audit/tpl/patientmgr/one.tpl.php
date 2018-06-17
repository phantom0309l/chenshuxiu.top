<?php
$pagetitle = '患者信息页';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/plugin/echarts/echarts.js",
    $img_uri . "/v5/common/setMedicineBreakDate.js?v=20171019",
]; //填写完整地址
$pageStyle = <<<STYLE
.not-point{
    color:#666;
}
.white{
    color:white;
}
.btn-rounded{
    margin:3px 0px;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
        <div class="content-div">
            <section class="col-md-12 remove-padding">
            <input type="hidden" id="patientid" value="<?= $patient->id ?>" />
            <div class="col-md-12">
                <!-- 信息 -->
                <table class="table patientOne">
                    <thead>
                        <tr>
                            <td style="font-size:20px;">姓名：<?=$patient->name?>[<?= $patient->id ?>]</td>
                            <td>年龄：<?=$patient->getAgeStr()?></td>
                            <td>性别：<?=$patient->getSexStr()?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan='3'>
                                <div class="push-10">
                                    联系方式：
                                    <?php
                                        $linkmans = $patient->getLinkmans();

                                        foreach ($linkmans as $linkman) {
                                            ?>
                                                <span class="push-5">
                                                    <?= $linkman->shipstr ? $linkman->shipstr . ':' : '' ?> <?= $linkman->mobile;?>
                                                </span>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>当前医生：<?=$patient->doctor->name?></td>
                            <td>首次扫码医生：<?=$patient->first_doctor->name?></td>
                            <td>当前疾病：<?=$patient->disease->name?></br>(<a target="_blank" href="/patientmgr/modifydisease?pcardid=<?=$pcard->id?>">修改疾病<i class="fa fa-pencil"></i></a>)</td>
                        </tr>
                        <tr class="not-point">
                            <td>身份证：<?=$patient->prcrid?></td>
                            <td>血型：<?=$patient->blood_type?></td>
                            <td>生日：<?=$patient->birthday?></td>
                        </tr>
                        <tr class="not-point">
                            <td>民族：<?=$patient->nation?></td>
                            <td>婚姻状况：<?=$patient->marry_status?></td>
                            <td>文化程度：<?=$patient->education?></td>
                        </tr>
                        <tr class="not-point">
                            <td>职业：<?=$patient->career?></td>
                            <td>家庭收入：<?=$patient->income?></td>
                            <td>邮编：<?=$patient->postcode?></td>
                        </tr>
                        <tr class="not-point">
                            <td>子女：<?=$patient->children?></td>
                            <td>自身免疫病：<?=$patient->autoimmune_illness?></td>
                            <td>其他疾病：<?=$patient->other_illness?></td>
                        </tr>

                        <!-- 各种病史 -->
                        <?php if ($patient->diseaseid != 1) { ?>
                        <tr>
                            <td>自身免疫病：<?=$patient->past_main_history?></td>
                            <td>其他疾病：<?=$patient->past_other_history?></td>
                            <td>传染病史：<?=$patient->infect_history?></td>
                        </tr>
                        <tr class="not-point">
                            <td>外伤史：<?=$patient->trauma_history?></td>
                            <td>饮酒史：<?=$patient->drink_history?></td>
                            <td>特殊接触史：<?=$patient->special_contact_history?></td>
                        </tr>
                        <tr class="not-point">
                            <td>家族病史：<?=$patient->family_history?></td>
                            <td>吸烟史：<?=$patient->smoke_history?></td>
                            <td>月经史：<?=$patient->menstruation_history?></td>
                        </tr>
                        <tr class="not-point">
                            <td>生育史：<?=$patient->childbearing_history?></td>
                            <td>过敏史：<?=$patient->allergy_history?></td>
                            <td>普通病史：<?=$patient->general_history?></td>
                        </tr>
                        <?php } ?>

                        <tr>
                            <td colspan='3' style="text-align:center;">
                                <a class="btn btn-primary" target="_blank" href="/patientmgr/modify?patientid=<?=$patient->id?>">更新基本信息<i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- <button class="btn btn-minw btn-rounded btn-info" type="button">用药：<?=$patient->drugStatusStr()?></button> -->

                <div>
                    <?php $tagids = $patient->getBindTagIds(); ?>
                    <?php if(count($tagids) > 0){ ?>
                        <h6 style="margin:10px 0px;">标签:</h6>
                    <?php } ?>
                    <?php foreach ($tagids as $k => $tagid) { ?>
                        <?php $tag = Tag::getById($tagid); ?>
                        <button class="btn btn-minw btn-rounded btn-default" type="button"><?=$tag->name?></button>
                    <?php } ?>
                </div>

                <!-- 任务 -->
                <div class="block block-rounded">
                    <div class="block-header bg-gray-light" style="padding:10px 15px;">
                        <ul class="block-options">
                            <!-- <li>
                                <a target="_blank" href="/optaskmgr/listnew?patient_name=<?=$patient->id?>" data-toggle="tooltip" title="" data-original-title="处理"><i class="fa fa-search"></i></a>
                            </li> -->
                        </ul>
                    <h3 class="block-title"><p style="margin:0px;">当前任务：<?=count($optasks)?>条</p></h3>
                    </div>
                    <div class="block-content" style="padding-top:0px;">
                        <?php if(count($optasks)>0){ ?>
                            <?php foreach($optasks as $optask){ ?>
                                <div style="border-top: 1px solid #ddd; padding:5px 0px;">
                                <?= $optask->getFixPlantime() ?>
                                <?= $optask->optasktpl->title ?>
                                <?php if( $optask->opnode instanceof OpNode ){ ?>
                                    <span class="text-warning" style="color: green">[<?= $optask->opnode->title ?>]</span>
                                <?php } ?>
                                <?php if( false && $optask->getOwnerNames() ){ ?>
                                    <span>[<?= $optask->getOwnerNames() ?>]</span>
                                <?php } ?>
                                <?php if($optask->level > 2){ ?>
                                    <span class="red">[L<?= $optask->level ?>]</span>
                                <?php } ?>
                                </div>
                            <?php } ?>
                        <?php }else { ?>
                            <span>无</span>
                        <?php } ?>
                    </div>
                </div>

                <!-- 用药 -->
                <?php
                    if ($patient->diseaseid == 1) {
                        include_once $tpl . "/_patient_medicine.php";
                    } else {
                        include_once $tpl . "/_patient_notadhd_medicine.php";
                    }
                ?>

            </div>
            </section>
        </div>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
