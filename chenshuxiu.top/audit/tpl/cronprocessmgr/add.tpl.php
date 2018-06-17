<?php
$pagetitle = "定时任务新增 ";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="contentBoxTitle">
                <h4>定时任务基本信息</h4>
            </div>
            <form action="/cronprocessmgr/addpost">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <input type="hidden" name="cronprocesstplid" value="<?= $cronprocesstpl->id ?>">
                    <tr>
                        <th width=140>定时任务标题</th>
                        <td>
                            <?= $cronprocesstpl->title?>
                        </td>
                    </tr>
                    <tr>
                        <th>疾病</th>
                        <td>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(true),"diseaseid",0); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>定时任务标题</th>
                        <td>
                            <input type="text" name="title" />
                        </td>
                    </tr>
                    <tr>
                        <th>序列号</th>
                        <td>
                            <input type="text" name="pos" />
                        </td>
                    </tr>
                    <tr>
                        <th>说明</th>
                        <td>
                            <textarea name="remark" cols="60" rows="5"></textarea>
                        </td>
                    </tr>
                </table>
                </div>
                <div class="contentBoxTitle">
                    <h4>任务推送时间设置</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width: 30%">
                    <thead>
                        <th>秒</th>
                        <th>分钟</th>
                        <th>小时</th>
                        <th>第几天of月</th>
                        <th>月份</th>
                        <th>第几天of周</th>
                    </thead>
                    <tbody>
                        <td>
                            <input style="width: 30px" type="text" name="s" />
                        </td>
                        <td>
                            <input style="width: 30px" type="text" name="m" />
                        </td>
                        <td>
                            <input style="width: 30px" type="text" name="h" />
                        </td>
                        <td>
                            <input style="width: 30px" type="text" name="dom" />
                        </td>
                        <td>
                            <input style="width: 30px" type="text" name="mon" />
                        </td>
                        <td>
                            <input style="width: 30px" type="text" name="dow" />
                        </td>
                    </tbody>
                </table>
                </div>
                <div class="contentBoxTitle">
                    <h4>定时任务变量配置</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                        <th>变量名</th>
                        <th>变量值</th>
                        <th>单位</th>
                        <th>变量说明</th>
                        <th>备注</th>
                    </thead>
                    <tbody>
                    <?php foreach ($cronprocesstplvars as $cronprocesstplvar) { ?>
                        <tr>
                            <td><?= $cronprocesstplvar->code ?></td>
                            <td>
                                <input type="text" name="var_<?=$cronprocesstplvar->code?>" />
                            </td>
                            <td><?= $cronprocesstplvar->unit ?></td>
                            <td><?= $cronprocesstplvar->name ?></td>
                            <td><?= $cronprocesstplvar->remark ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
                <input type="submit" value="提交" />
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
