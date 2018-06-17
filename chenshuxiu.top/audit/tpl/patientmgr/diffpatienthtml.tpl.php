<?php
$patientbases = array();
$patientbases['姓名'] = 'name';
$patientbases['身份证'] = 'prcrid';
$patientbases['性别'] = 'sex';
$patientbases['生日'] = 'birthday';
$patientbases['血型'] = 'blood_type';
$patientbases['民族'] = 'nation';
$patientbases['婚姻状况'] = 'marry_status';
$patientbases['教育'] = 'education';
$patientbases['职业'] = 'career';
$patientbases['收入'] = 'income';
$patientbases['邮编'] = 'postcode';
$patientbases['自身免疫病'] = 'autoimmune_illness';
$patientbases['其他疾病'] = 'other_illness';
$patientbases['既往病史'] = 'past_main_history';
$patientbases['其他疾病'] = 'past_other_history';
$patientbases['传染病史'] = 'infect_history';
$patientbases['外伤史'] = 'trauma_history';
$patientbases['饮酒史'] = 'drink_history';
$patientbases['特殊接触史'] = 'special_contact_history';
$patientbases['家族病史'] = 'family_history';
$patientbases['吸烟史'] = 'smoke_history';
$patientbases['月经史'] = 'menstruation_history';
$patientbases['生育史'] = 'childbearing_history';
$patientbases['过敏史'] = 'allergy_history';
$patientbases['普通病史'] = 'general_history';
$patientbases['最后一次用户行为时间'] = 'lastpipe_createtime';
$patientbases['上次活跃日期'] = 'lastactivitydate';
$patientbases['下次活跃日期'] = 'nextactivitydate';
$patientbases['活跃状态'] = 'isactivity';
$patientbases['得分趋势'] = 'paper_score_trend';
$patientbases['clone源'] = 'clone_by_patientid';
$patientbases['电话'] = 'mobile';
$patientbases['备用联系人'] = 'other_contacts';
$patientbases['邮箱'] = 'email';
$patientbases['备注'] = 'remark';
$patientbases['运营对patient的备注'] = 'opsremark';

$pcardbases = array();
$pcardbases['患者姓名'] = 'patient_name';
$pcardbases['基于医生的患者分组'] = 'groupstr4doctor';
$pcardbases['建档日期'] = 'create_doc_date';
$pcardbases['院内病历号'] = 'out_case_no';
$pcardbases['院内就诊卡号'] = 'patientcardno';
$pcardbases['院内患者ID'] = 'patientcard_id';
$pcardbases['院内病案号'] = 'bingan_no';
$pcardbases['费用类型'] = 'fee_type';
$pcardbases['诊断'] = 'complication';
$pcardbases['首发时间'] = 'first_happen_date';
$pcardbases['首次就诊时间'] = 'first_visit_date';
$pcardbases['上次发病日期'] = 'last_incidence_date';
$pcardbases['有更新'] = 'has_update';
$pcardbases['最后一次用户行为时间'] = 'lastpipe_createtime';
$pcardbases['患者核对用药情况'] = 'send_pmsheet_status';
$pcardbases['就诊卡状态'] = 'status';
$pcardbases['审核备注'] = 'auditremark';
$pcardbases['审核通过时间'] = 'audittime';
$pcardbases['医生备注'] = 'remark_doctor';

$textarea_keys = array(
    'autoimmune_illness',
    'other_illness',
    'past_main_history',
    'past_other_history',
    'infect_history',
    'trauma_history',
    'drink_history',
    'special_contact_history',
    'family_history',
    'smoke_history',
    'menstruation_history',
    'childbearing_history',
    'allergy_history',
    'general_history',
    'other_contacts',
    'remark',
    'opsremark',
    'complication',
    'auditremark',
    'remark_doctor');

$colorA = '';
if ($patientA->createuser->patientid == $patientA->id) {
    $colorA = 'red';
}

$colorB = '';
if ($patientB->createuser->patientid == $patientB->id) {
    $colorB = 'red';
}
?>
<div class="searchBar">
    <div class="table-responsive">
        <table class="table table-bordered">
        <input type="hidden" id="patientAid" value="<?=$patientA->id?>">
        <input type="hidden" id="patientBid" value="<?=$patientB->id?>">
        <tr>
            <td colspan="5">Patient</td>
        </tr>
        <tr>
            <td>微信昵称</td>
            <td style="text-align: right">
                <span><?=$patientA->createuser->createwxuser->nickname?></span>
            </td>
            <td>
                <span><?=$patientB->createuser->createwxuser->nickname?></span>
            </td>
        </tr>
        <tr>
            <td>createuserid + 姓名</td>
            <td style="text-align: right">
                <span style="color:<?=$colorA?>">[<?=$patientA->createuser->id?>] + [<?=$patientA->createuser->name?>]</span>
            </td>
            <td>
                <span style="color:<?=$colorB?>">[<?=$patientB->createuser->id?>] + [<?=$patientB->createuser->name?>]</span>
            </td>
        </tr>
        <tr>
            <td>与患者关系</td>
            <td style="text-align: right">
                <span><?=$patientA->createuser->shipstr?></span>
            </td>
            <td>
                <span><?=$patientB->createuser->shipstr?></span>
            </td>
        </tr>
        <tr>
            <td>报到时间</td>
            <td style="text-align: right">
                <span><?=$patientA->createtime?></span>
            </td>
            <td>
                <span><?=$patientB->createtime?></span>
            </td>
        </tr>
        <tr>
            <td>报到手机</td>
            <td style="text-align: right">
                <span>
                    <?php
                    if ($patientA->createuser instanceof User) {
                        echo $patientA->createuser->getMaskMobile();
                    }
                    ?>
                </span>
            </td>
            <td>
                <span>
                    <?php
                    if ($patientB->createuser instanceof User) {
                        echo $patientB->createuser->getMaskMobile();
                    }
                    ?>
                </span>
            </td>
        </tr>
        <tr>
            <td>patientid + 姓名</td>
            <td style="text-align: right">
                <span>[<?=$patientA->id?>] + [<?=$patientA->name?>]</span>
            </td>
            <td>
                <span>[<?=$patientB->id?>] + [<?=$patientB->name?>]</span>
            </td>
        </tr>
        <tr>
            <td>所属医生 + 疾病</td>
            <td style="text-align: right">
                <span>[<?=$patientA->doctor->name?>] + [<?=$patientA->disease->name?>]</span>
            </td>
            <td>
                <span>[<?=$patientB->doctor->name?>] + [<?=$patientB->disease->name?>]</span>
            </td>
        </tr>
        <tr>
            <?php
            $statusarr = array();
            $statusarr[0] = '无效';
            $statusarr[1] = '有效';
            $statusarr[2] = '取消关注';
            $statusarr[3] = '未报到';
            $statusarr[4] = '死亡';
            ?>
            <!-- 状态  0:无效  1:有效  2:取消关注  3:未报到（徐雁老数据库） 4:死亡 -->
            <td>患者状态</td>
            <td style="text-align: right">
                <span><?=$statusarr[$patientA->status]?></span>
            </td>
            <td>
                <span><?=$statusarr[$patientB->status]?></span>
            </td>
        </tr>
        <tr>
            <td>患者审核状态</td>
            <td style="text-align: right">
                <span><?=XConst::auditStatus($patientA->auditstatus)?></span>
            </td>
            <td>
                <span><?=XConst::auditStatus($patientB->auditstatus)?></span>
            </td>
        </tr>
        <?php
        foreach ($patientbases as $key => $value) {
            if ($patientA->$value != $patientB->$value) {
                if (in_array($value, $textarea_keys)) {
                ?>
        <tr>
            <td>
                <?=$key?>
            </td>
            <td class='col-md-5 col-sm-5 col-xs-5' style="text-align: right">
                <textarea cols=80 name="patientA_<?=$key?>" class="patientA" data-key="<?=$value?>"><?=$patientA->$value?></textarea>
            </td>
            <td class='col-md-5 col-sm-5 col-xs-5'>
                <textarea cols=80 name="patientB_<?=$key?>" class="patientB" data-key="<?=$value?>"><?=$patientB->$value?></textarea>
            </td>
        </tr>
                <?php
                } else {
                    ?>
        <tr>
            <td>
                <?=$key?>
            </td>
            <td class='col-md-5 col-sm-5 col-xs-5' style="text-align: right">
                <input name="patientA_<?=$key?>" class="patientA" data-key="<?=$value?>" value="<?=$patientA->$value?>">
            </td>
            <td class='col-md-5 col-sm-5 col-xs-5'>
                <input name="patientB_<?=$key?>" class="patientB" data-key="<?=$value?>" value="<?=$patientB->$value?>">
            </td>
        </tr>

            <?php
                }
            } elseif ($patientA->$value || $patientB->$value) {
                ?>
        <tr>
            <td>
                <?=$key?>
            </td>
            <td class='col-md-5 col-sm-5 col-xs-5' style="text-align: right">
                <?=$patientA->$value?>
            </td>
            <td class='col-md-5 col-sm-5 col-xs-5'>
                <?=$patientB->$value?>
            </td>
        </tr>
                <?php
            }
        }
        ?>
        <tr>
            <td></td>
            <td style="text-align: right">
                <input class="modifypatientA btn btn-success" value="修改">
            </td>
            <td>
                <input class="modifypatientB btn btn-success" value="修改">
            </td>
        </tr>
    </table>
    </div>
</div>

<?php if ($pcardA instanceof Pcard && $pcardB instanceof Pcard) { ?>
    <div class="searchBar">
        <div class="table-responsive">
            <table class="table table-bordered">
            <input type="hidden" id="pcardAid" value="<?=$pcardA->id?>">
            <input type="hidden" id="pcardBid" value="<?=$pcardB->id?>">
            <tr>
                <td colspan="6">Pcard</td>
            </tr>
            <tr>
                <td>pcardid</td>
                <td style="text-align: right">
                    <span><?=$pcardA->id?></span>
                </td>
                <td>
                    <span><?=$pcardB->id?></span>
                </td>
            </tr>
            <tr>
                <td>patientid＋姓名</td>
                <td style="text-align: right">
                    <span style="color:<?=$colorA?>">[<?=$pcardA->patientid?>] + [<?=$pcardA->patient->name?>]</span>
                </td>
                <td>
                    <span style="color:<?=$colorB?>">[<?=$pcardB->patientid?>] + [<?=$pcardA->patient->name?>]</span>
                </td>
            </tr>
            <tr>
                <td>所属医生 + 疾病</td>
                <td style="text-align: right">
                    <span>[<?=$pcardA->doctor->name?>] + [<?=$pcardA->disease->name?>]</span>
                </td>
                <td>
                    <span>[<?=$pcardB->doctor->name?>] + [<?=$pcardB->disease->name?>]</span>
                </td>
            </tr>
            <?php
            foreach ($pcardbases as $key => $value) {
                $field_valueA = $pcardA->getValueFix($value);
                $field_valueB = $pcardB->getValueFix($value);
                if ($field_valueA != $field_valueB) {
                    if (in_array($value, $textarea_keys)) {
                    ?>
                        <tr>
                            <td>
                                <?=$key?>
                            </td>
                            <td class='col-md-5 col-sm-5 col-xs-5' style="text-align: right">
                                <textarea cols=80 name="pcardA_<?=$key?>" class="pcardA" data-key="<?=$value?>"><?=$field_valueA?></textarea>
                            </td>
                            <td class='col-md-5 col-sm-5 col-xs-5'>
                                <textarea cols=80 name="pcardB_<?=$key?>" class="pcardB" data-key="<?=$value?>"><?=$field_valueB?></textarea>
                            </td>
                        </tr>
                    <?php
                    } else {
                        ?>
                            <tr>
                                <td>
                                    <?=$key?>
                                </td>
                                <td class='col-md-5 col-sm-5 col-xs-5' style="text-align: right">
                                    <input name="pcardA_<?=$key?>" class="pcardA" data-key="<?=$value?>" value="<?=$field_valueA?>">
                                </td>
                                <td class='col-md-5 col-sm-5 col-xs-5'>
                                    <input name="pcardB_<?=$key?>" class="pcardB" data-key="<?=$value?>" value="<?=$field_valueB?>">
                                </td>
                            </tr>
                        <?php
                    }
                } elseif ($field_valueA || $field_valueB) {
                    ?>
                        <tr>
                            <td>
                                <?=$key?>
                            </td>
                            <td class='col-md-5 col-sm-5 col-xs-5' style="text-align: right">
                                <?=$field_valueA?>
                            </td>
                            <td class='col-md-5 col-sm-5 col-xs-5'>
                                <?=$field_valueB?>
                            </td>
                        </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td></td>
                <td style="text-align: right">
                    <input class="modifypcardA btn btn-success" value="修改">
                </td>
                <td>
                    <input class="modifypcardB btn btn-success" value="修改">
                </td>
            </tr>
        </table>
        </div>
    </div>
<?php }?>