<?php
$pagetitle = "后台资源列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/auditresourcemgr/add">资源新建</a>
            </div>
            <div class="searchBar">
                <form action="/auditresourcemgr/list" method="get" class="pr">
                角色：( <?= HtmlCtr::getRadioCtrImp(array(0=>'正选',1=>'反选'), 'auditroleInvert',$auditroleInvert,''); ?> )
                <?php
                $auditroledescarr = AuditRole::getDescArr();
                echo HtmlCtr::getCheckboxCtrImp($auditroledescarr, 'auditroleids[]', $auditroleids, '');
                ?>
                <br />
                类型：( <?= HtmlCtr::getRadioCtrImp(array(0=>'正选',1=>'反选'), 'typeInvert',$typeInvert,''); ?> )
                <?php
                $arr = array(
                    'page' => 'page',
                    'post' => 'post',
                    'json' => 'json',
                    'jsonHtml' => 'jsonHtml');
                echo HtmlCtr::getCheckboxCtrImp($arr, 'types[]', $types, '');
                ?>
                <br />
                菜单关联: <?= HtmlCtr::getRadioCtrImp(array('all'=>'全部','yes'=>'已关联','no'=>'未关联'), 'bindMenu',$bindMenu,''); ?>
                <br />
                    <input type="submit" class="btn btn-success" value="筛选">
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>序号</td>
                        <td>id</td>
                        <td>类型</td>
                        <td>页面名称</td>
                        <td>action/method</td>
                        <td>对应菜单</td>
                        <td>拥有权限</td>
                        <td>责任人</td>
                        <td>content</td>
                        <td>remark</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            <?php

            foreach ($auditresources as $k => $a) {
                $isblue = '';
                if ($a->type == 'page' && false == $a->auditmenu instanceof AuditMenu) {
                    $isblue = 'background:#a1b2e6';
                }
                ?>
                    <form class="" action="/auditresourcemgr/fastmodifypost" method="post">
                        <input type="hidden" name="auditresourceid" value="<?= $a->id ?>" />
                        <tr style="<?=$isblue?>">
                            <td><?= $k ?></td>
                            <td><?= $a->id; ?></td>
                            <td><?= $a->type; ?></td>
                            <td>
                                <a target="_blank" href="/<?=$a->action?>/<?=$a->method?>"><?= mb_substr($a->title, 0,10); ?><?= (mb_strlen($a->title)>10)?'...':'' ?></a>
                            </td>
                            <td class="f12"><?= $a->action; ?>/<?= $a->method; ?></td>
                            <td class="f12">
                <?php
                if ($a->type != 'page') {
                    // 不需要菜单
                } elseif ($isblue) {
                    ?>
                            <input type="text" name="auditmenuid" value="" />
                                <input type="submit" value="关联已有的" />
                                <a class="btn btn-success" href="/auditresourcemgr/addmenupost?auditresourceid=<?= $a->id ?>">生成新的menu</a>
                                <?php }else{?>
                                    <?= $a->auditmenuid ?> <?= $a->auditmenu->title; ?>
                                <?php }?>
                        </td>
                            <td class="f12"><?= $a->getAuditRolesStr('<br/>'); ?></td>
                            <td><?= $a->owner_auditor->name; ?></td>
                            <td><?= $a->content?'+++':'' ?></td>
                            <td><?= $a->remark?'...':'' ?></td>
                            <td>
                                <a target="_blank" class="btn btn-primary" href="/auditresourcemgr/modify?auditresourceid=<?= $a->id ?>">修改</a>
                            </td>
                        </tr>
                    </form>

            <?php }  ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
