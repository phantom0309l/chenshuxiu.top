<?php
$pagetitle = "没有user的patient";
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
            <div class="searchBar">
                <a class="refuse btn btn-danger ml30" href="/patientmgr/TestPatientmvToPatientHistoryPost">将测试用户迁移到历史表</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>wxuserid 微信昵称 关注日期</td>
                        <td>userid</td>
                        <td>与患者关系</td>
                        <td>报到电话</td>
                        <td>patientid</td>
                        <td>院内识别ID</td>
                        <td>所属医生</td>
                        <td>报到日期</td>
                        <td>报到姓名</td>
                        <td>生日</td>
                        <td>上下线</td>
                        <td>审核</td>
                        <td>组合状态</td>
                        <td>同名</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
<?php

foreach ($patients as $a) {
    $users = $a->getUsers();
    if (empty($users)) {
        $u = $a->createuser;
        include $tpl . "/patientmgr/_list4bind_row.php";
    } else {
        foreach ($users as $u) {
            include $tpl . "/patientmgr/_list4bind_row.php";
        }
    }
}
?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
