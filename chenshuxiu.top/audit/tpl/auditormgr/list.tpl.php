<?php
$pagetitle = "员工列表 Auditor";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
            <?php
            $auditroles = AuditRole::getDescArr();
            ?>
        <div class="searchBar">
            <a class="btn btn-success" href="/auditormgr/add">员工新建</a>
            <a class="btn btn-primary" href="/auditormgr/leaveofficelist">离职员工</a>
        </div>
        <div class="searchBar">
            <form action="/auditormgr/list" method="get" class="pr">
            类型: <?= HtmlCtr::getRadioCtrImp4OneUi(CtrHelper::getAuditorTypeCtrArray(true),'type', $type, 'css-radio-success')?>
            <br />角色: <?php
            $auditroledescarr = AuditRole::getDescArr(true);
            echo HtmlCtr::getRadioCtrImp($auditroledescarr, 'auditroleid', $auditroleid, '');
            ?>
            <br /> <input type="submit" class="btn btn-success" value="筛选">
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2">序号</th>
                        <th rowspan="2">
                            id
                            <br />
                            创建日期
                            <br />
                            头像
                        </th>
                        <th rowspan="2" style="width: 200px">
                            姓名
                            <br />
                            user:username
                            <br />
                            兼职
                            <br />
                            错误次数/最后一次登陆时间
                            <br />
                            患者
                        </th>
                        <th rowspan="2">
                            是否接受消息推送
                            <br />
                            微信号
                        </th>
                        <th colspan="<?= count($auditroles) ?>">角色</th>
                        <th rowspan="2">操作</th>
                    </tr>
                    <tr>
                        <?php
                        foreach ($auditroles as $k => $v) {
                            echo "<th>$v</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($auditors as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $a->id ?>
                            <br />
                            <span class="gray"><?= $a->getCreateDay() ?></span>
                            <br />
                            <?php if ($a->picture instanceof Picture) { ?>
                            <a target="_blank" href="<?= $a->picture->getSrc(0, 0, false) ?>">
                                <div style="width: 80px; padding: 1px; border: 1px solid #ccc;">
                                    <img class="img-responsive" src="<?= $a->picture->getSrc(160, 160, false) ?>" alt="">
                                </div>
                            </a>
                            <?php } ?>
                        </td>
                        <td>
                            <span class="fb"> <?= $a->name ?></span>
                            <br />
                            <span class="blue"><?= $a->user->username ?></span>
                            <br />
                            <span class="gray"><?= $a->getTypeDesc() ?>
                            <?php if($a->prevauditor instanceof Auditor){ ?>
                            <br />
                            <?= $a->prevauditor->name ?>
                            <?php } ?>
                            <br />
                            <?= $a->controlxprovince->name ?></span>
                            <br />
                    <?php
                    if ($a->user instanceof User) {
                        $logintime = $a->user->last_login_time == '0000-00-00 00:00:00' ? $a->user->createtime : $a->user->last_login_time;
                        echo "[" . $a->user->login_fail_cnt . "]  [" . $logintime . "]";
                    }
                    ?>
                            <br />
                            患者:
                            <span class="blue">
                    <?php
                    if ($a->user->patient instanceof Patient) {
                        echo $a->user->patient->getMaskName();
                    }
                    ?>      </span>
                        </td>
                        <td>
                            <div style="border-bottom: 1px dashed #999; margin-bottom: 10px;">
                                <div data-auditorid="<?=$a->id?>" class="open_send_msg_btn btn btn-xs mb10 <?= $a->can_send_msg==1 ? 'btn-primary':'btn-default gray' ?>">开启推送</div>
                                <div data-auditorid="<?=$a->id?>" class="close_send_msg_btn btn btn-xs mb10 <?= $a->can_send_msg==0 ? 'btn-primary':'btn-default gray' ?>">关闭推送</div>
                            </div>
                            <?php
                    if ($a->user instanceof User) {
                        ?>
                            <div class="open_close_ops btn btn-xs btn-success" data-ops="hide" data-id="<?=$a->id?>">显示/折叠</div>
                    <?php } ?>
                            <div id="ops-<?=$a->id?>" class="ops">
                                <?php
                    if ($a->user instanceof User) {
                        foreach ($a->user->getWxUsers() as $b) {
                            $openfix = 'btn-primary';
                            $closefix = 'btn-default gray';
                            if (! $b->is_ops) {
                                $openfix = 'btn-default gray';
                                $closefix = 'btn-primary';
                            }
                            ?>
                                            <div>
                                                [<?=$b->wxshop->shortname?>]
                                                <?=$b->openid?>
                                            </div>
                                <div>
                                    <div data-wxuserid="<?=$b->id?>" class="open_ops_btn btn btn-xs mb10 <?=$openfix?>">开启监控</div>
                                    <div data-wxuserid="<?=$b->id?>" class="close_ops_btn btn btn-xs mb10 <?=$closefix?>">关闭监控</div>
                                </div>
                    <?php
                        }
                    }
                    ?>
                            </div>
                        </td>
                        <?php
                    foreach ($auditroles as $k => $v) {
                        if (in_array($k, $a->getAuditRoleIdArr())) {
                            echo "<td><span style='color:green;'>$v</span></td>";
                        } else {
                            echo "<td><span style='color:red;'>×</span></td>";
                        }
                    }
                    ?>
                        <td>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditormgr/modify?auditorid=<?= $a->id ?>">改[auditor]</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/usermgr/modify?userid=<?= $a->userid ?>">改[user]</a>
                            <?php if ($a->user->patient instanceof Patient) { ?>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/patientmgr/modify?patientid=<?= $a->user->patient->id ?>">改[patient]</a>
                            <?php } ?>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditormgr/cleardata?userid=<?= $a->userid ?>">清理测试数据</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditormgr/qrcode?auditorid=<?= $a->id ?>">二维码</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditorpgrouprefmgr/bindpgroup?auditorid=<?= $a->id ?>">绑定分组</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/optasktplauditorrefmgr/bindoptasktpl?auditorid=<?= $a->id ?>">绑定任务类型</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditorpushmsgtplrefmgr/bindauditorpushmsgtpl?auditorid=<?= $a->id ?>">绑定监控消息类型</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditordiseaserefmgr/binddisease?auditorid=<?= $a->id ?>">绑定疾病</a>
                            <br />
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditormgr/oneformoveauditormarket?auditorid_market=<?= $a->id ?>">变更市场负责人</a>
                            <a target="_blank" class="btn btn-xs btn-primary mb5" href="/auditorgroupmgr/list?auditorid=<?= $a->id ?>">去添加到员工组</a>
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
$(function () {
    init();

    function init () {
        $(".ops").hide();
    }

    $(".open_close_ops").on("click", function () {
        var type = $(this).data("ops");
        var id = $(this).data("id");

        if (type == 'hide') {
            $("#ops-" + id).show();
            $(this).data("ops", "show");
        } else if (type == 'show') {
        	$("#ops-" + id).hide();
        	$(this).data("ops", "hide");
        }
    });
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
    $(".open_send_msg_btn").on("click", function () {
        var me = $(this);
        var auditorid = me.data('auditorid');

        $.ajax({
            type: "post",
            url: "/auditormgr/opensendmsg",
            data:{
                "auditorid" : auditorid
            },
            dataType: "text",
            success : function(data){
                if(data == 'suc'){
                    me.removeClass('btn-default');
                    me.removeClass('gray');
                    me.addClass('btn-primary');

                    var near = me.siblings(".close_send_msg_btn");
                    near.removeClass('btn-primary');
                    near.addClass('gray');
                    near.addClass('btn-default');
                }else{
                    alert(data);
                }
            }
        });
    });

    $(".close_send_msg_btn").on("click", function () {
        var me = $(this);
        var auditorid = me.data('auditorid');

        $.ajax({
            type: "post",
            url: "/auditormgr/closesendmsg",
            data:{
                "auditorid" : auditorid
            },
            dataType: "text",
            success : function(data){
                if(data == 'suc'){
                    me.removeClass('btn-default');
                    me.removeClass('gray');
                    me.addClass('btn-primary');

                    var near = me.siblings(".open_send_msg_btn");
                    near.removeClass('btn-primary');
                    near.addClass('gray');
                    near.addClass('btn-default');
                }else{
                    alert(data);
                }
            }
        });
    });
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
