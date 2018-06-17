<?php
$pagetitle = "药品新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v5/page/audit/diseasemedicinerefmgr/add/add.js"
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/medicinemgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width='140'>商品名</th>
                        <td>
                            <input type="text" name="name" value="" />
                            <span class="gray">主要用这个</span>
                        </td>
                        <td>说明</td>
                        <td>是否定制</td>
                    </tr>
                    <tr>
                        <th>学名</th>
                        <td>
                            <input type="text" name="scientificname" value="" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>分组</th>
                        <td>
                            <input type="text" name="groupstr" class="groupstr-input" />
                            <span class="btn btn-primary groupstr-panel-on">现有分组</span>
                            <div class="groupstr-panel none">
                                <?php
                                $groupstrs = Medicine::getGroupstrArr(0);
                                foreach( $groupstrs as $groupstr ){
                                    ?>
                                    <div class="btn btn-default groupstr-btn" data-groupstr="<?=$groupstr?>"><?=$groupstr?></div>
                                    <?php
                                }?>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>单位</th>
                        <td>
                            <input type="text" name="unit" value="" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>给药途径</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_way_arr" value="" />
                        </td>
                        <td>竖线分隔</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>用药时机</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_timespan_arr" value="" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>标准用法</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_std_dosage_arr" value="" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制,方案定制</td>
                    </tr>
                    <tr>
                        <th>剂量</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_dose_arr" value="" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>频率</th>
                        <td>
                            <input type="text" style="width: 80%;" name="drug_frequency_arr" value="" />
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>调药规则</th>
                        <td>
                            <textarea name="drug_change_arr" style="width: 80%; height: 100px;"></textarea>
                        </td>
                        <td>竖线分隔</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>是否是中药</th>
                        <td>
                            <?= HtmlCtr::getRadioCtrImp(array('0'=>'不是','1'=>'是'), 'ischinese', 0 , '')?>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>药材成分<br/>（中药才需要填写）</th>
                        <td>
                            <textarea name="herbjson" style="width: 80%; height: 100px;"></textarea>
                        </td>
                        <td>填写格式  药材名1=用量1|药材名2=用量2</td>
                        <td>疾病定制,医生定制</td>
                    </tr>
                    <tr>
                        <th>用药注意事项</th>
                        <td>
                            <textarea name="doctorremark" style="width: 80%; height: 100px;"></textarea>
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
                        $picture = null;
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
                            <textarea name="content" style="width: 80%; height: 200px;"></textarea>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交" />
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