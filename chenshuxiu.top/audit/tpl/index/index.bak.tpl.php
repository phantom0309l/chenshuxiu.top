<!DOCTYPE html>
<html>
    <head>
        <title>运营系统首页</title>
        <?php include $tpl . "/_head.php"; ?>
        <style>
            .tree {
                border: 1px solid #d0d9e3;
                background: #ebf1f7;
                margin: 20px 0px;
                padding: 20px;
            }

            .subTree {
                display: inline-block;
                float: left;
                border: 1px solid #bae2ca;
                background: #d4e6df;
                margin: 10px;
                padding: 10px;
                text-align: center;
                font-weight: bold;
                box-shadow: #333 0px 0px 20px;
            }

            .leaf {
                display: inline-block;
                float: left;
                border: 1px solid #edf6dd;
                background: #f3fae7;
                margin: 10px 0px;
                padding: 10px;
                text-align: center;
                box-shadow: #333 0px 0px 20px;
            }

            .noAllow {
                box-shadow: #333 0px 0px 0px inset;
                opacity: 0.7;
            }
        </style>
    </head>
    <body>
        <?php include $tpl . "/_nav.php"; ?>
        <div class="col-md-12">
            <section class="col-md-10">
                <?php
                $pagetitle = "运营系统首页";
                include $tpl . "/_pagetitle.php";
                ?>
                <h3>网站地图(绿色可点击)</h3>

                <?php
                foreach ($auditmenutree as $auditmenutree_one) {
                    if (false == $auditmenutree_one['self'] instanceof AuditMenu) {
                        continue;
                    }
                    ?>
                    <div class="tree">
                        <p style="font-weight: bold;">
                            <?= $auditmenutree_one['self']->title ?>
                        </p>
                        <?php
                        if (false == empty($auditmenutree_one['subs'])) {
                            foreach ($auditmenutree_one['subs'] as $auditmenu_sub) {
                                $auditresources = $auditmenu_sub->getAuditResourceList();
                                $subClassFix = '';
                                if (false == empty($auditroleidarr)) {
                                    if (empty(array_intersect($auditroleidarr, $auditmenu_sub->auditresource->getAuditRoleIdArr()))) {
                                        $subClassFix = 'noAllow';
                                    }
                                }
                                ?>
                                <a href="<?= $auditmenu_sub->url ?>">
                                    <div class="subTree <?= $subClassFix ?>">
                                        <?= $auditmenu_sub->title ?>
                                    </div>
                                </a>

                                <?php
                                foreach ($auditresources as $auditresource) {
                                    $subClassFix = '';
                                    if (false == empty($auditroleidarr)) {
                                        if (empty(array_intersect($auditroleidarr, $auditresource->getAuditRoleIdArr()))) {
                                            $subClassFix = 'noAllow';
                                        }
                                    }
                                    ?>
                                    <div class="leaf <?= $subClassFix ?>">
                                        <?= $auditresource->title ?>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>

                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php } ?>
            </section>
            <aside class="col-md-2">
                <?php include $tpl . "/_sub_nav.php"; ?>
            </aside>
        </div>
        <div class="clear"></div>
        <?php include $tpl . "/_footer.php"; ?>
    </body>
</html>
