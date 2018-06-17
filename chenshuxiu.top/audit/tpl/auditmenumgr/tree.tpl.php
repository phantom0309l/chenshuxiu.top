<?php
$pagetitle = "后台菜单列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .tree{
        border: 1px solid #d0d9e3;
        background: #ebf1f7;
        margin: 20px 0px;
        padding: 20px;
    }
    .subTree{
        display: inline-block;
        float: left;
        border: 1px solid #bae2ca;
        background: #d4e6df;
        margin: 10px;
        padding: 10px;
        text-align: center;
        box-shadow: #333 0px 0px 20px;
    }
    .leaf{
        display: inline-block;
        float: left;
        border: 1px solid #eeb4db;
        background: #f5d3e6;
        margin: 10px;
        padding: 10px;
        text-align: center;
        box-shadow: #333 0px 0px 20px;

    }
    .noAllow{
        box-shadow: #333 0px 0px 0px inset;
        opacity: 0.7;
    }
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a class="btn btn-success" href="/auditmenumgr/add">菜单新建</a>
                <a class="btn btn-primary" href="/auditmenumgr/list">列表展示</a>
            </div>
            <div class="searchBar">
                <form action="/auditmenumgr/tree" method="get" class="pr">
                    <?php
                    $auditroledescarr = AuditRole::getDescArr();
                    echo HtmlCtr::getCheckboxCtrImp($auditroledescarr,'auditroleids[]',$auditroleidarr,''); ?>
                    <br/>
                    <input class='btn btn-success' type="submit" value="提交" />
                </form>
            </div>
            <div>
                <?php foreach ($auditmenutree as $auditmenutree_one) {
                    if( false == $auditmenutree_one['self'] instanceof AuditMenu ){
                        continue;
                    }
                    ?>
                    <div class="tree">
                        <p style="font-weight: bold;">
                            <?= $auditmenutree_one['self']->title?>
                            <br/>
                            <?= $auditmenutree_one['self']->url?>
                            <br/>
                            <?= $auditmenutree_one['self']->auditresource->getAuditRolesStr()?>
                        </p>
                        <?php
                        if( false == empty($auditmenutree_one['subs'])){
                            foreach ($auditmenutree_one['subs'] as $auditmenu_sub) {
                                $auditresources = $auditmenu_sub->getAuditResourceList();
                                $subClassFix = '';
                                if( false == empty($auditroleidarr)){
                                    if(empty(array_intersect( $auditroleidarr, $auditmenu_sub->auditresource->getAuditRoleIdArr()))){
                                        $subClassFix = 'noAllow';
                                    }
                                }
                                ?>
                                <div class="subTree <?= $subClassFix ?>">
                                    <?= $auditmenu_sub->title?>
                                    <br/>
                                    <?= $auditmenu_sub->url?>
                                    <br/>
                                    <?php if( $auditmenu_sub->auditresource instanceof AuditResource ){?>
                                        <?= $auditmenu_sub->auditresource->getAuditRolesStr()?>
                                    <?php }?>
                                </div>
                                <?php foreach ($auditresources as $auditresource) {
                                    $subClassFix = '';
                                    if( false == empty($auditroleidarr)){
                                        if(empty(array_intersect( $auditroleidarr, $auditresource->getAuditRoleIdArr()))){
                                            $subClassFix = 'noAllow';
                                        }
                                    }?>
                                    <div class="leaf <?= $subClassFix ?>">
                                        <?= $auditresource->title?>
                                        <br/>
                                        <?= $auditresource->action?>/
                                        <?= $auditresource->method?>
                                        <br/>
                                        <?= $auditresource->getAuditRolesStr()?>
                                    </div>
                                <?php }?>
                                <div class="clearfix"></div>

                        <?php }
                        }?>
                    </div>
                <?php }?>
            </div>
         </section>
     </div>
     <div class="clear"></div>

     <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
