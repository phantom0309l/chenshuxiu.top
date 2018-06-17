<style>
    .block_width{
        display: inline-block !important;
        width: 80% !important;
    }
    table.scheduletpl-right th, table.scheduletpl-right label {
        font-weight: 500;
    }
    table.scheduletpl-right label {
        margin-right:10px;
    }
</style>
<form action="/scheduletplmgr/addPost" method="post">
    <input type="hidden" name="doctorid" value="<?= $doctor->id ?>"/>

    <div class="table-responsive">
        <table class="table table-bordered scheduletpl-right">
        <thead>
            <tr>
                <th colspan="10">
                    <h5 class="font-w400">新增一条出诊模板信息</h5>
                </th>
            </tr>
            <tr>
                <th class="col-xs-8">
                    选项
                </th>
                <th>
                    患者微信端
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div>
                        <label>疾病类型</label>
                        <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDiseaseCtrArray($doctor->getDiseases(),true), 'diseaseid', '', 'form-control'); ?>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[diseaseid]", $is_show_p_wx['diseaseid'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <label>门诊周期</label>
                        <?php echo HtmlCtr::getSelectCtrImp(ScheduleTpl::get_op_hzArray(), 'op_hz', '', 'form-control '); ?>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[op_hz]", $is_show_p_wx['op_hz'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <label>门诊时段</label>
                        <?php echo HtmlCtr::getSelectCtrImp(ScheduleTpl::get_day_partArray(), 'day_part', '', 'form-control '); ?>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[day_part]", $is_show_p_wx['day_part'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label class="col-xs-12 remove-padding">时间段</label>
                        <div class="col-xs-12 remove-padding">
                            <input type="text" class="form-control" name="begin_hour_str[begin]" placeholder="开始时间" />
                        </div>
                        <div class="col-xs-12 remove-padding push-10-t">
                            <input type="text" class="form-control" name="begin_hour_str[end]" placeholder="结束时间"/>
                        </div>
                </td>
                <td>
                    <?php
                    $arr = [
                        '1' => '显示',
                        '0' => '不显示'
                    ];
                    echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[begin_hour_str]", $is_show_p_wx['begin_hour_str'] ?? 0);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <label>门诊类型</label>
                        <?php echo HtmlCtr::getSelectCtrImp(ScheduleTpl::get_op_typeArray(), 'op_type', '', 'form-control '); ?>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[op_type]", $is_show_p_wx['op_type'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <label>门诊电话</label>
                        <input type="text" class="form-control " name="scheduletpl_mobile" placeholder="区号-电话"/>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[scheduletpl_mobile]", $is_show_p_wx['scheduletpl_mobile'] ?? 0);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>门诊费用</label>
                        <div class="input-group">
                        <input type="text" class="form-control" name="scheduletpl_cost" />
                        <span class="input-group-addon">元/人</span>
                        </div>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[scheduletpl_cost]", $is_show_p_wx['scheduletpl_cost'] ?? 0);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                    <label class="col-xs-12 remove-padding">选择最近的门诊出诊时间</label>
                    <div class="col-xs-12 remove-padding">
                    <input type="text" class="form-control calendar" name="op_date" />
                    </div>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[op_date]", $is_show_p_wx['op_date'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                    <label class="col-xs-12 remove-padding">最大大预约次数（0为不限次数）</label>
                    <div class="col-xs-12 remove-padding">
                    <input type="text" class="form-control" name="maxcnt" />
                    </div>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[maxcnt]", $is_show_p_wx['maxcnt'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <div>门诊地址</div>
                        <?php echo HtmlCtr::getAddressCtr4New('scheduletpl'); ?>
                        <textarea class="form-control" name="address" rows="4" cols="80" placeholder="详细地址，例：长安街道通港大厦708"></textarea>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[address]", $is_show_p_wx['address'] ?? 1);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <label>备注（会显示在患者端）</label>
                        <textarea class="form-control" name="tip" rows="4" cols="80"><?= $scheduletpl->tip?></textarea>
                    </div>
                </td>
                <td>
                    <?php
                        $arr = [
                            '1' => '显示',
                            '0' => '不显示'
                        ];
                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[tip]", $is_show_p_wx['tip'] ?? 0);
                    ?>
                </td>
            </tr>
<!--            <tr>-->
<!--                <td>-->
<!--                    <div>-->
<!--                        <label>公告</label>-->
<!--                        <textarea class="form-control" name="bulletin" rows="4" cols="80"></textarea>-->
<!--                    </div>-->
<!--                </td>-->
<!--                <td>-->
<!--                    --><?php
//                        $arr = [
//                            '1' => '显示',
//                            '0' => '不显示'
//                        ];
//                        echo HtmlCtr::getRadioCtrImp4OneUi($arr, "is_show_p_wx[bulletin]", $is_show_p_wx['bulletin'] ?? 1, 'css-radio-warning');
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
                            // 默认为无标签患者
                            echo HtmlCtr::getCheckboxCtrImp4OneUi($arr, "see_patienttagtplids[]", [], '', 'css-checkbox-success');
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
    </div>
</form>
<script type="text/javascript">
    $(function(){
        init_checkbox();
    });

    function init_checkbox () {
        $("input[name='see_patienttagtplids[]']").each(function(index, el) {
            $(this).prop('checked',true);
        });;
    }
</script>
