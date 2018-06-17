<?php
$pagetitle = "药品修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/medicinemgr/modifypost" method="post">
                <input type="hidden" name="medicineid" value="<?= $medicine->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width='140'>medicineid</th>
                        <td><?= $medicine->id ?></td>
                        <td>说明</td>
                        <td>是否定制</td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $medicine->createtime ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>商品名</th>
                        <td>
                            <input type="text" name="name" value="<?= $medicine->name ?>" />
                            请慎重修改
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>学名</th>
                        <td>
                            <input type="text" name="scientificname" value="<?= $medicine->scientificname ?>" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>分组</th>
                        <td>
                            <input type="text" name="groupstr" class="groupstr-input" value="<?= $medicine->groupstr ?>" />
                            <span class="btn btn-primary groupstr-panel-on">现有分组</span>
                            <div class="groupstr-panel none">
                                <?php
                                $groupstrs = Medicine::getGroupstrArr(0);
                                foreach ($groupstrs as $groupstr) {
                                    ?>
                                    <div class="btn btn-default groupstr-btn" data-groupstr="<?=$groupstr?>"><?=$groupstr?></div>
                                    <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>单位</th>
                        <td>
                            <input type="text" name="unit" value="<?= $medicine->unit ?>" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>给药途径</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_way_arr" value="<?=$medicine->drug_way_arr?>" />
                        </td>
                        <td>竖线分隔</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>用药时机</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_timespan_arr" value="<?=$medicine->drug_timespan_arr?>" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>标准用法</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_std_dosage_arr" value="<?=$medicine->drug_std_dosage_arr?>" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制,方案定制</td>
                    </tr>
                    <tr>
                        <th>剂量</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_dose_arr" value="<?=$medicine->drug_dose_arr?>" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>频率</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_frequency_arr" value="<?=$medicine->drug_frequency_arr?>" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>调药规则</th>
                        <td>
                            <textarea name="drug_change_arr" style="width: 80%; height: 100px;"><?=$medicine->drug_change_arr?></textarea>
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>是否是中药</th>
                        <td>
                            <?= HtmlCtr::getRadioCtrImp(array('0'=>'不是','1'=>'是'), 'ischinese', $medicine->ischinese , '')?>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>药材成分<br/>（中药才需要填写）</th>
                        <td>
                            <textarea name="herbjson" style="width: 80%; height: 100px;"><?= $medicine->herbjson?></textarea>
                        </td>
                        <td>填写格式  药材名1=用量1|药材名2=用量2</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>用药注意事项</th>
                        <td>
                            <textarea name="doctorremark" style="width: 80%; height: 100px;"><?=$medicine->doctorremark?></textarea>
                        </td>
                        <td></td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>图片</th>
                        <td>
                        <?php
                        $picWidth = 150;
                        $picHeight = 150;
                        $pictureInputName = "pictureid";
                        $isCut = false;
                        $picture = $medicine->picture;
                        $objtype = "Auditor";
                        $objid = $myauditor->id;
                        $objsubtype = "WxPicMsg";
                        require_once ("$dtpl/picture.ctr.php");
                        ?>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>简介</th>
                        <td>
                            <textarea name="content" style="width: 80%; height: 200px;"><?= $medicine->content ?></textarea>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>官网首页展示</th>
                        <td>
                            <input type="radio" name="isshow" value="1" id="radio-show-yes" <?php if ($medicine->isShowOnWww()){ ?> checked <?php } ?>>
                            <label for="radio-show-yes">是</label>
                            <input type="radio" name="isshow" value="0" id="radio-show-yes" <?php if (!$medicine->isShowOnWww()){ ?> checked <?php } ?>>
                            <label for="radio-show-yes">否</label>
                            <span style="color: #999; padding-left: 20px;">注意：选择"是"就会在官网展示，请编辑无误后再次选择</span>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
