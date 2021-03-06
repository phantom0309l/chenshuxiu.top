<?php
$pagetitle = "新建医院";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/hospitalmgr/addpost" method="post">
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
                        <th>
                            名片logo
                            <br />
                            用于生成名片
                            <br />
                            242*242
                        </th>
                        <td>
                            <?php
                            $picWidth = 150;
                            $picHeight = 150;
                            $pictureInputName = "qr_logo_pictureid";
                            $isCut = false;
                            $picture = null;
                            include ("$dtpl/picture.ctr.php");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>省/市/区</th>
                        <td>
                            <div class="col-xs-6">
                                <?php echo HtmlCtr::getAddressCtr4New("hospital_place");?>
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
                        <th>等级:</th>
                        <td>
                            <input type="text" name="levelstr" value="" />
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
