<?php
$pagetitle = "同名患者[{$thePatient->name}] , <span class='red'>目标 patientid:" . $thePatient->id . "</span> , 切换目标请点击相应 patientid";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
.table-wxuser {
	color: #fff;
	background-color: #449644;
	border-color: 398439;
}

.table-user {
	color: #fff;
	background-color: #028E9B;
	border-color: 398439;
}

.table-patient {
	color: #fff;
	background-color: #A56E47;
	border-color: 398439;
}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
            <?php
            $ids = array();
            foreach ($patients as $a) {
                $ids[] = $a->id;
            }

            $patientids = implode(",", $ids);
            ?>
            <div class="searchBar">
            <a target="_blank" class="btn btn-success" href="mergealready?patientids=<?=$patientids?>">患者信息比对</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="table-wxuser" colspan="1">WxUser</td>
                        <td class="table-user" colspan="4">User</td>
                        <td class="table-patient" colspan="10">Patient</td>
                    </tr>
                    <tr>
                        <td>wxuserid [wxshopid] 微信昵称 关注日期</td>
                        <td>userid</td>
                        <td>与患者关系</td>
                        <td>报到电话</td>
                        <td>操作</td>
                        <td>patientid</td>
                        <td>历次门诊时间</td>
                        <td>create_patientid,doctor,疾病,病历号,就诊卡,患者ID,病案号</td>
                        <td>first_doctor</td>
                        <td>报到日期</td>
                        <td>报到姓名</td>
                        <td>生日</td>
                        <td>上下线</td>
                        <td>审核</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($patients as $a) {
                        $users = $a->getUsers();
                        $cnt = count($users);
                        if (empty($users)) {
                            $u = null; // 重置u
                            ?>
                    <tr>
                            <?php
                            include $tpl . "/patientmgr/_list4bind_wxuser_user.php";
                            include $tpl . "/patientmgr/_list4bind_patient.php";
                            ?>
                    </tr>
                        <?php
                        } else {
                            $i = 0;
                            foreach ($users as $u) {
                                ?>
                    <tr>
                            <?php
                                include $tpl . "/patientmgr/_list4bind_wxuser_user.php";
                                if ($i == 0) {
                                    include $tpl . "/patientmgr/_list4bind_patient.php";
                                    $i ++;
                                }
                                ?>
                    </tr>
                    <?php
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
