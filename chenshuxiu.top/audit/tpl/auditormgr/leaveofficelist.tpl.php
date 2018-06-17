<?php
$pagetitle = "离职员工列表 Auditor";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <?php
            $auditroles = AuditRole::getDescArr();
            ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">序号</th>
                        <th rowspan="2">id</th>
                        <th rowspan="2" width=95>入职日期 <br/> <span class="gray">更新日期</span></th>
                        <th rowspan="2" width=60>姓名</th>
                        <th rowspan="2">username</th>
                        <th rowspan="2">患者</th>
                        <th colspan="<?= count($auditroles) ?>">角色</th>
                        <th rowspan="2">操作</th>
                    </tr>
                    <tr>
                        <?php
                        foreach ($auditroles as $k => $v) {
                            echo "<th width=20>$v</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($auditors as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?> <br/><span class="gray"><?= substr($a->updatetime, 0, 10) ?></span></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->user->username ?></td>
                        <td><?=$a->user->patient->name ; ?></td>
                        <?php
                        foreach ( $auditroles as $k => $v ) {
                            if (in_array( $k, $a->getAuditRoleIdArr() )) {
                                echo "<td><span style='color:green;'>√</span></td>";
                            } else {
                                echo "<td><span style='color:red;'>×</span></td>";
                            }
                        }
                        ?>
                        <td>
                            <a target="_blank" href="/auditormgr/modify?auditorid=<?= $a->id ?>">改[auditor]</a>
                            <a target="_blank" href="/auditormgr/cleardata?userid=<?= $a->userid ?>">清理垃圾数据</a>
                            <a target="_blank" href="/auditormgr/qrcode?auditorid=<?= $a->id ?>">二维码</a>
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
$footerScript = <<<XXX
    $(".open_ops_btn").on("click", function () {
        var me = $(this);
        var wxuserid = me.data('wxuserid');

        $.ajax({
            type: "post",
            url: "/auditormgr/openopsjson",
            data:{
                "wxuserid" : wxuserid
            },
            dataType: "text",
            success : function(data){
                if(data == 'suc'){
                    me.removeClass('btn-default');
                    me.removeClass('gray');
                    me.addClass('btn-primary');

                    var near = me.siblings(".close_ops_btn");
                    near.removeClass('btn-primary');
                    near.addClass('gray');
                    near.addClass('btn-default');
                }else{
                    alert(data);
                }
            }
        });
    });

    $(".close_ops_btn").on("click", function () {
        var me = $(this);
        var wxuserid = me.data('wxuserid');

        $.ajax({
            type: "post",
            url: "/auditormgr/closeopsjson",
            data:{
                "wxuserid" : wxuserid
            },
            dataType: "text",
            success : function(data){
                if(data == 'suc'){
                    me.removeClass('btn-default');
                    me.removeClass('gray');
                    me.addClass('btn-primary');

                    var near = me.siblings(".open_ops_btn");
                    near.removeClass('btn-primary');
                    near.addClass('gray');
                    near.addClass('btn-default');
                }else{
                    alert(data);
                }
            }
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
