<?php
$pagetitle = "医生备忘录 DoctorMemos";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>创建时间</td>
                        <td>备注日期</td>
                        <td>医生</td>
                        <td>患者姓名</td>
                        <td>备注内容</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($doctormemos as $a) {
                ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->createtime ?></td>
                        <td><?= $a->thedate ?></td>
                        <td><?= $a->doctor->name ?></td>
                        <td><a target="_blank" href="/optaskmgr/listnew?patient_name=<?= $a->patient->name ?>"><?= $a->patient->name ?> </a></td>
                        <td><?= $a->content ?></td>
                        <td>
                            <?php if( $a->status ){?>
                                <a class="btn btn-primary" href="/doctormemomgr/changestatuspost?doctormemoid=<?= $a->id ?>">进行中</a>
                            <?php }else{?>
                                <a class="btn btn-default" href="/doctormemomgr/changestatuspost?doctormemoid=<?= $a->id ?>">已关闭</a>
                            <?php }?>
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
