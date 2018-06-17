<?php
$pagetitle = "方寸课堂用户二维码生成";
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
            <form action="/wxqrcodemgr/addpost" method="post">
                <table class="table table-bordered">
                    <tr>
                        <th width=140>wxshopid</th>
                        <td>
                            <input id="wxshopid" type="text" name="wxshopid" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>pcode(例如Share[DY])</th>
                        <td>
                            <input id="pcode" type="text" name="pcode" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>wxuserid</th>
                        <td>
                            <input id="wxuserid" type="text" name="wxuserid" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>scene_str前缀</th>
                        <td>
                            <input id="scene_pre" type="text" name="scene_pre" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="生成二维码" />
                        </td>
                    </tr>
                </table>
            </form>
<?php

$pagetitle = "方寸课堂医生二维码生成";
include $tpl . "/_pagetitle.php";
?>
            <form action="/wxqrcodemgr/adddoctorpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>wxshopid</th>
                        <td>
                            <input id="wxshopid" type="text" name="wxshopid" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>pcode</th>
                        <td>
                            <input id="pcode" type="text" name="pcode" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>doctorid</th>
                        <td>
                            <input id="doctorid" type="text" name="doctorid" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th>scene_str</th>
                        <td>
                            <input id="scene_str" type="text" name="scene_str" style="width: 50%;" />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" value="生成二维码" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
        <p>
            <img src="<?= $qrcodeurl ?>" />
        </p>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
