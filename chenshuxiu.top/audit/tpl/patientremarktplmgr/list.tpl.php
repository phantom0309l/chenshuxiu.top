<?php
$pagetitle = "{$doctor->name}医生录症状体征分类  PatientRemarkTpls";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <a target="_blank" class="btn btn-success" href="/patientremarktplmgr/add?doctorid=<?=$doctor->id ?>">症状体征新建</a>
                <div class="fr">
                    <form action="/patientremarktplmgr/copypost?todoctorid=<?=$doctor->id ?>" method="post">
                        想要复制的医生id<input type="text" name="fromdoctorid" style="width:200px;" placeholder="去医生列表查看对应医生id" value=""/>
                        <input type="submit" value="复制"/>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
            <form action="/patientremarktplmgr/posmodifypost" method="post">
                <input type="hidden" name="doctorid" value="<?= $doctor->id ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>id</td>
                            <td>医生</td>
                            <td>疾病</td>
                            <td>标题</td>
                            <td style="width: 40px">序号</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
<?php
foreach ($patientremarktpls as $a) {
    ?>
                        <tr>
                            <td><?= $a->id ?></td>
                            <td><?= $a->doctor->name ?></td>
                            <td><?= $a->doctor->getMasterDisease()->name ?></td>
                            <td><?= $a->name ?></td>
                            <td>
                                <input type="text" name='pos[<?=$a->id ?>]' value="<?= $a->pos ?>" style="width: 40px" />
                            </td>
                            <td>
                                <a target="_blank" href="/patientremarktplmgr/modify?patientremarktplid=<?= $a->id ?>">修改</a>
                                <a class="delete_prt" data-patientremarktplid=<?= $a->id ?>>删除</a>
                            </td>
                        </tr>
<?php } ?>
                        <tr>
                            <td colspan=20 align=right>
                                <input type="submit" value="保存序号修改" />
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        $("a.delete_prt").on("click",function(){
            var me = $(this);

            var tr = me.parents("tr");
            var patientremarktplid = me.data("patientremarktplid");

            if(!confirm("确认删除及其所有关联的快速输入的提示词么？")){
                return false;
            }
            var url = "/patientremarktplmgr/deletepost";
            var args = {
                "patientremarktplid":patientremarktplid
            };
            $.post(url,args,function(data){
                    tr.remove();
            });

            return false;
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
