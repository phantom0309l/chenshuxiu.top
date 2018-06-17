<?php
$pagetitle = "99健康网——修改医院信息";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/jkw_hospitalmgr/modifypost" method="post">
                <input type="hidden" name="jkw_hospitalid" value="<?= $jkw_hospital->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>id</th>
                        <td><?= $jkw_hospital->id ?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $jkw_hospital->createtime ?></td>
                    </tr>
                    <tr>
                        <th>医院全称</th>
                        <td>
                            <input type="text" name="name" value="<?= $jkw_hospital->name ?>" style="width: 300px" />
                        </td>
                    </tr>
                    <tr>
                        <th>医院简称</th>
                        <td>
                            <input type="text" name="shortname" value="<?= $jkw_hospital->shortname ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            logo
                            <br />
                            用于打印页
                        </th>
                        <td>
                            <?php
                            $picWidth = 150;
                            $picHeight = 150;
                            $pictureInputName = "logo_pictureid";
                            $isCut = false;
                            $picture = $jkw_hospital->logo_picture;
                            include ("$dtpl/picture.ctr.php");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>性质</th>
                        <td>
                            <input type="text" name="type" value="<?= $jkw_hospital->levelstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>等级</th>
                        <td>
                            <input type="text" name="levelstr" value="<?= $jkw_hospital->levelstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>电话:</th>
                        <td>
                            <input type="text" name="mobile" value="<?= $jkw_hospital->mobile ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>省/市/区</th>
                        <td>
                            <div class="col-xs-6">
                                <?php echo HtmlCtr::getAddressCtr4New("hospital_place",$jkw_hospital->xprovinceid, $jkw_hospital->xcityid, $jkw_hospital->xcountyid); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>详细地址</th>
                        <td>
                            <textarea id="content" name="content" cols=50 rows=3><?= $jkw_hospital->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>院长姓名:</th>
                        <td>
                            <input type="text" name="president_name" value="<?= $jkw_hospital->president_name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>建院年份:</th>
                        <td>
                            <input type="text" name="found_year" value="<?= $jkw_hospital->found_year ?>" />（例如：2001）
                        </td>
                    </tr>
                    <tr>
                        <th>科室数量:</th>
                        <td>
                            <input type="text" name="department_cnt" value="<?= $jkw_hospital->department_cnt ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>医护人数:</th>
                        <td>
                            <input type="text" name="employee_cnt" value="<?= $jkw_hospital->employee_cnt ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>病床数量:</th>
                        <td>
                            <input type="text" name="bed_cnt" value="<?= $jkw_hospital->bed_cnt ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>是否医保:</th>
                        <td>
                            <label class="css-input css-radio css-radio-warning push-10-r">
                            <input type="radio" name="is_yibao" value="1" <?php if ($jkw_hospital->is_yibao == 1) {?> checked="" <?php }?>><span></span> 医保
                            </label>
                            <label class="css-input css-radio css-radio-warning">
                            <input type="radio" name="is_yibao" value="2" <?php if ($jkw_hospital->is_yibao == 2) {?> checked="" <?php }?>><span></span> 非医保
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>网址:</th>
                        <td>
                            <input type="text" name="website" value="<?= $jkw_hospital->website ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>邮政编码:</th>
                        <td>
                            <input type="text" name="postalcode" value="<?= $jkw_hospital->postalcode ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>简介:</th>
                        <td>
                            <textarea id="brief" name="brief" cols=50 rows=3><?= $jkw_hospital->brief ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>公交路线:</th>
                        <td>
                            <textarea id="bus_route" name="bus_route" cols=50 rows=3><?= $jkw_hospital->bus_route ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>抓取信息来源的网址:</th>
                        <td>
                            <input type="text" name="from_url" value="<?= $jkw_hospital->from_url ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
