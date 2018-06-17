<?php
$pagetitle = "99健康网——新建医院";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>\
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/jkw_hospitalmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th>全称:</th>
                        <td>
                            <input type="text" name="name" value="" style="width: 60%" />
                        </td>
                    </tr>
                    <tr>
                        <th>简称:</th>
                        <td>
                            <input type="text" name="shortname" value="" />
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
                            $picture = null;
                            include ("$dtpl/picture.ctr.php");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>性质:</th>
                        <td>
                            <input type="text" name="type" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>等级:</th>
                        <td>
                            <input type="text" name="levelstr" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>电话:</th>
                        <td>
                            <input type="text" name="mobile" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>省/市/区</th>
                        <td>
                            <div class="col-xs-6">
                                <?php echo HtmlCtr::getAddressCtr4New("hospital_place"); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>详细地址:</th>
                        <td>
                            <textarea id="content" name="content" cols=50 rows=3></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>院长姓名:</th>
                        <td>
                            <input type="text" name="president_name" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>建院年份:</th>
                        <td>
                            <input type="text" name="found_year" value="" />（例如：2001）
                        </td>
                    </tr>
                    <tr>
                        <th>科室数量:</th>
                        <td>
                            <input type="text" name="department_cnt" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>医护人数:</th>
                        <td>
                            <input type="text" name="employee_cnt" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>病床数量:</th>
                        <td>
                            <input type="text" name="bed_cnt" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>是否医保:</th>
                        <td>
                            <label class="css-input css-radio css-radio-warning push-10-r">
                            <input type="radio" name="is_yibao" value="1" ><span></span> 医保
                            </label>
                            <label class="css-input css-radio css-radio-warning">
                            <input type="radio" name="is_yibao" value="2" ><span></span> 非医保
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>网址:</th>
                        <td>
                            <input type="text" name="website" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>邮政编码:</th>
                        <td>
                            <input type="text" name="postalcode" value="" />
                        </td>
                    </tr>
                    <tr>
                        <th>简介:</th>
                        <td>
                            <textarea id="brief" name="brief" cols=50 rows=3></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>公交路线:</th>
                        <td>
                            <textarea id="bus_route" name="bus_route" cols=50 rows=3></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>抓取信息来源的网址:</th>
                        <td>
                            <input type="text" name="from_url" value="" />
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
