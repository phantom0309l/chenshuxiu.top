<?php
$pagetitle = "修改 wxgroups";
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
            <form action="/wxgroupmgr/modifypost" method="post">
                <input type="hidden" name="wxgroupid" value="<?= $wxgroup->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <td width=140>id</td>
                        <td><?= $wxgroup->id?></td>
                    </tr>
                    <tr>
                        <td>创建时间</td>
                        <td><?= $wxgroup->createtime ?></td>
                    </tr>
                    <tr>
                        <td>wxshopid</td>
                        <td>
                            <input id="wxshopid" type="text" name="wxshopid" style="width: 50%;" value="<?= $wxgroup->wxshopid ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>groupid</td>
                        <td>
                            <input id="groupid" type="text" name="groupid" style="width: 50%;" value="<?= $wxgroup->groupid ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>ename</td>
                        <td>
                            <input id="ename" type="text" name="ename" style="width: 50%;" value="<?= $wxgroup->ename ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>
                            <input id="name" type="text" name="name" style="width: 50%;" value="<?= $wxgroup->name ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th>content</th>
                        <td>
                            <textarea id="content" name="content" cols="100" rows="8"><?= $wxgroup->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" value="修改" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>