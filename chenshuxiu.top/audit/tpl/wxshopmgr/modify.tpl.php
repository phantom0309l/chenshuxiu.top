<?php
$pagetitle = "服务号修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-10">
            <form action="/wxshopmgr/modifypost" method="post">
                <input type="hidden" name="wxshopid" value="<?= $wxshop->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width="160">wxshopid</th>
                        <td><?= $wxshop->id ?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?= $wxshop->createtime ?></td>
                    </tr>
                    <tr>
                        <th>名称</th>
                        <td>
                            <input type="text" name="name" value="<?= $wxshop->name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>短名称</th>
                        <td>
                            <input type="text" name="shortname" value="<?= $wxshop->shortname ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>类型</th>
                        <td><?= HtmlCtr::getRadioCtrImp(WxShop::typeDescs(), 'type', $wxshop->type,' ') ?></td>
                    </tr>
                    <tr>
                        <?php
                        $diseases = DiseaseDao::getListAll();
                        $arr = array();
                        foreach ($diseases as $a) {
                            if ($a->id == 1) {
                                $arr[$a->id] = 'ADHD';
                                continue;
                            }
                            $arr[$a->id] = $a->name;
                        }
                        $arr[0] = '未知';
                        ?>
                        <th>主关联疾病</th>
                        <td><?= HtmlCtr::getRadioCtrImp($arr, 'diseaseid', $wxshop->diseaseid,' ') ?></td>
                    </tr>
                    <tr>
                        <th>gh</th>
                        <td><?= $wxshop->gh ?></td>
                    </tr>
                    <tr>
                        <th>token</th>
                        <td>保密</td>
                    </tr>
                    <tr>
                        <th>appid</th>
                        <td><?= $wxshop->appid ?></td>
                    </tr>
                    <tr>
                        <th>secret</th>
                        <td><?= $wxshop->secret ?></td>
                    </tr>
                    <tr>
                        <th>encodingaeskey</th>
                        <td>保密</td>
                    </tr>
                    <tr>
                        <th>mchid</th>
                        <td><?= $wxshop->mchid ?></td>
                    </tr>
                    <tr>
                        <th>mkey</th>
                        <td>保密</td>
                    </tr>
                    <tr>
                        <th>access_token</th>
                        <td><?= $wxshop->access_token ?></td>
                    </tr>
                    <tr>
                        <th>access_in</th>
                        <td><?= $wxshop->access_in ?></td>
                    </tr>
                    <tr>
                        <th>expires_in</th>
                        <td><?= $wxshop->expires_in ?></td>
                    </tr>
                    <tr>
                        <th>服务号后台登录邮箱</th>
                        <td>
                            <input type="text" name="wx_email" value="<?= $wxshop->wx_email ?>" style="width: 300px;" />
                        </td>
                    </tr>
                    <tr>
                        <th>服务号下次认证日期</th>
                        <td>
                            <input type="text" class="calendar" name="next_cert_date" value="<?= $wxshop->next_cert_date ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>服务号注册运营者</th>
                        <td>
                            <input type="text" name="reg_oper_name" value="<?= $wxshop->reg_oper_name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>服务号管理员</th>
                        <td>
                            <input type="text" name="admin_name" value="<?= $wxshop->admin_name ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>服务号运营者,可登录</th>
                        <td>
                            <input type="text" name="oper_names" value="<?= $wxshop->oper_names ?>" style="width: 300px;" />
                        </td>
                    </tr>
                    <tr>
                        <th>关联开发平台账号</th>
                        <td>
                            <input type="text" name="open_email" value="<?= $wxshop->open_email ?>" style="width: 300px;" />
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
