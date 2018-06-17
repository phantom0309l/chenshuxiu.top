<style>
    table.scheduletpl-right th, table.scheduletpl-right label {
        font-weight: 500;
    }
    table.scheduletpl-right label {
        margin-right:10px;
    }
</style>
<form action="/scheduletplmgr/modifySimplePost" method="post">
    <input type="hidden" name="scheduletplid" value="<?=$scheduletpl->id ?>" />
    <table class="table table-bordered scheduletpl-right">
        <thead>
            <tr>
                <th colspan="10">
                    <div class="">
                        <h5 class="font-w400 "><?= $scheduletpl->toMoreStr(); ?></h5>
                    </div>
                    <div class="alert alert-warning alert-dismissable push-10-t">
                        <h5 class="font-w400">小修一条出诊模板信息</h5>
                    </div>
                </th>
            </tr>
            <tr>
                <th class="col-md-8">
                    选项
                </th>
                <th>
                    患者微信端
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                $is_show_p_wx = json_decode($scheduletpl->is_show_p_wx_json, true);

                $show_arr = [
                    '1' => '显示',
                    '0' => '不显示'
                ];
            ?>
            <tr>
                <td>
                    <div>
                        <label>疾病类型</label>
                        <?= HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($doctor->getDiseases(),true), 'diseaseid', $scheduletpl->diseaseid, 'form-control  '); ?>
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[diseaseid]", $is_show_p_wx['diseaseid'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="mt10">
                        <label>门诊类型</label>
                        <?php echo HtmlCtr::getSelectCtrImp(ScheduleTpl::get_op_typeArray(), 'op_type', $scheduletpl->op_type,'form-control '); ?>
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[op_type]", $is_show_p_wx['op_type'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <label>门诊电话</label>
                        <input type="text" class="form-control " name="scheduletpl_mobile" value="<?= $scheduletpl->scheduletpl_mobile ?>" placeholder="区号-电话" />
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[scheduletpl_mobile]", $is_show_p_wx['scheduletpl_mobile'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>门诊费用</label>
                        <div class="input-group">
                        <input type="text" class="form-control" name="scheduletpl_cost" value="<?= $scheduletpl->scheduletpl_cost ?>" />
                        <span class="input-group-addon">元/人</span>
                        </div>
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[scheduletpl_cost]", $is_show_p_wx['scheduletpl_cost'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                        <?php
                            $begin_time = json_decode($scheduletpl->begin_hour_str, true);
                            // if (false == strpos($scheduletpl->begin_hour_str, '-')) {
                            //     $begin_time[0] = $scheduletpl->begin_hour_str;
                            // } else {
                            //     $begin_time = explode('-', $scheduletpl->begin_hour_str);
                            // }

                        ?>
                    <div class="form-group">
                    <label class="col-xs-12 remove-padding">时间段</label>
                    <div class="col-xs-12 remove-padding">
                    <input type="text" class="form-control" name="begin_hour_str[begin]" placeholder="开始时间" value="<?= $begin_time['begin'] ?? '' ?>" />
                    </div>
                    <div class="col-xs-12 remove-padding push-10-t">
                    <input type="text" class="form-control" name="begin_hour_str[end]" placeholder="结束时间" value="<?= $begin_time['end'] ?? '' ?>"/>
                    </div>
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[begin_hour_str]", $is_show_p_wx['begin_hour_str'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="mt10">
                        <label>最大预约次数（0为不限次数）</label>
                        <input type="text" class="form-control " value="<?= $scheduletpl->maxcnt?>" name="maxcnt" />
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[maxcnt]", $is_show_p_wx['maxcnt'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="mt10">
                        <div>门诊地址</div>
                        <?php echo HtmlCtr::getAddressCtr4New('scheduletpl', $scheduletpl->xprovinceid, $scheduletpl->xcityid, $scheduletpl->xcountyid); ?>
                        <textarea class="form-control" name="address" rows="4" cols="80"><?= $scheduletpl->content ?></textarea>
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[address]", $is_show_p_wx['address'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="mt10">
                        <label>备注（会显示在患者端）</label>
                        <textarea class="form-control" name="tip" rows="4" cols="80"><?=$scheduletpl->tip?></textarea>
                    </div>
                </td>
                <td>
                    <?php
                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_show_p_wx[tip]", $is_show_p_wx['tip'] ?? 1);
                    ?>
                </td>
            </tr>
<!--            <tr>-->
<!--                <td>-->
<!--                    <div>-->
<!--                        <label>公告(该医生下所有门诊公用)</label>-->
<!--                        <textarea class="form-control" name="bulletin" rows="4" cols="80">--><?//=$doctor->bulletin?><!--</textarea>-->
<!--                    </div>-->
<!--                </td>-->
<!--                <td>-->
<!--                    --><?php
//                        echo HtmlCtr::getRadioCtrImp4OneUi($show_arr, "is_bulletin_show", $doctor->is_bulletin_show);
//                    ?>
<!--                </td>-->
<!--            </tr>-->
            <tr>
                <td colspan="10">
                    <div class="">
                        <h5 class="font-w400" style="border-bottom:1px solid #e9e9e9;padding-bottom: 10px;">
                            门诊权限
                        </h5>
                        <p class="push-10-t">
                            以下标签的患者可见
                        </p>
                    </div>
                    <div class="">
                        <?php
                            $arr = JsonPatientTagTpl::jsonArrayForAudit($doctor);
                            $select_ids = explode(',', $scheduletpl->see_patienttagtplids);

                            echo HtmlCtr::getCheckboxCtrImp4OneUi($arr, "see_patienttagtplids[]", $select_ids, '', 'css-checkbox-success');
                        ?>
                    </div>

                </td>
            </tr>
            <tr>
                <td colspan="10">
                    <input class="btn btn-primary btn-minw" type="submit" value="提交"/>
                </td>
            </tr>
        </tbody>
    </table>

</form>
