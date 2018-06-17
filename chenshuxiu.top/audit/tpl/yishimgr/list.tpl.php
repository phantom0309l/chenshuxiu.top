<?php
$pagetitle = "医师列表 Yishis";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <a target="_blank" href="/yishimgr/add?yishiid=<?= $a->id ?>">添加医师</a>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>id</td>
                <td>角色类型</td>
                <td>姓名</td>
                <td>手机号</td>
                <td>密码</td>
                <td>医院</td>
                <td>科室</td>
                <td>最后登录时间</td>
                <td>操作</td>
            </tr>
            </thead>

            <tbody>
            <?php
            if (empty($yishis)) {
                echo "没有医师";
            }
            foreach ($yishis as $a) {
                ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?php if($a->type == 0){
                            echo "未知";
                        }else if($a->type == 1){
                            echo "医师";
                        }else if($a->type == 2){
                            echo "审核药师";
                        }else if($a->type == 3){
                            echo "配药药师";
                        }else if($a->type == 9){
                            echo "管理员";
                        }?></td>
                    <td><?= $a->name?></td>
                    <td><?= $a->mobile ?></td>
                    <td><?= $a->password ?></td>
                    <td><?= $a->hospital_name ?></td>
                    <td><?= $a->department_name ?></td>
                    <td><?= $a->last_login_time ?></td>
                    <td>
                        <a target="_blank" href="/yishimgr/modify?yishiid=<?= $a->id ?>">修改</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>