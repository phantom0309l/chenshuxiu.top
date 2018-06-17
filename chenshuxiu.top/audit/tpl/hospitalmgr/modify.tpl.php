<?php
$pagetitle = "修改医院信息";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/hospitalmgr/modifypost" method="post">
                <input type="hidden" name="hospitalid" value="<?= $hospital->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>id</th>
                        <td><?= $hospital->id ?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $hospital->createtime ?></td>
                    </tr>
                    <tr>
                        <th>医院全称</th>
                        <td>
                            <input type="text" name="name" value="<?= $hospital->name ?>" style="width: 300px" />
                        </td>
                    </tr>
                    <tr>
                        <th>医院简称</th>
                        <td>
                            <input type="text" name="shortname" value="<?= $hospital->shortname ?>" />
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
                            $picture = $hospital->logo_picture;
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
                            $picture = $hospital->qr_logo_picture;
                            include ("$dtpl/picture.ctr.php");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>省/市/区</th>
                        <td>
                            <div class="col-xs-5">
                                <?php echo HtmlCtr::getAddressCtr4New('hospital', $hospital->xprovinceid, $hospital->xcityid, $hospital->xcountyid); ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>详细地址</th>
                        <td>
                            <textarea id="address" name="address" cols=50 rows=3><?= $hospital->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>医院等级</th>
                        <td>
                            <input type="text" name="levelstr" value="<?= $hospital->levelstr ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>能否配置正丁</th>
                        <td>
                            <p class="text-danger"><span>正丁是小儿多动症药品</span></p>
                            <?= HtmlCtr::getRadioCtrImp(CtrHelper::getCan_public_zhengdingCtrArray(), 'can_public_zhengding', $hospital->can_public_zhengding, ''); ?>
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
