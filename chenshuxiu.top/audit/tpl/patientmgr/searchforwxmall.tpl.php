<?php
$pagetitle = 'patientmgr/searchforwxmall';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12 contentShell">
        <section class="col-md-12 content-left">
            <div class="searchBar">
                <form action="/patientmgr/searchforwxmall" method="get" class="pr">
                    <label for="">按手机/微信昵称搜索：</label>
                    <input type="text" name="keyword" value="<?= $keyword ?>" />
                    <input type="submit" value="搜索" />
                </form>
            </div>
            <div class="table-responsive">
                <table class="table border-top-blue patientList" style="text-align:center;">
                <thead>
                    <tr>
                        <td>患者姓名</td>
                        <td>报到时间</td>
                        <td>所属医生</td>
                        <td>查看流</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($patients as $a) {
                ?>
                <tr>
                        <td class="pr patientName">
                            <span><?= $a->getMaskName() ?></span>
                            <?php if( 1 != $a->status){?>
                                <br />
                            <span style="color: red;">
                                    <?=$a->getStatusStr()?>
                                </span>
                            <?php }?>
                            <div class="pa showRemarkBox none"><?= $a->opsremark; ?></div>
                        </td>
                        <td><?= $a->createtime ?></td>
                        <td><?= $a->doctor->name ?></td>
                        <td>
                            <a href="/patientmgr/list?keyword=<?= $a->name ?>" target="_blank">查看流</a>
                        </td>
                    </tr>
            <?php } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
