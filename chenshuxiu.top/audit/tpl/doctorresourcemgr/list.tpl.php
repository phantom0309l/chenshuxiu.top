<?php
$pagetitle = "医生后台权限列表";
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
            <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
                <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                    <a class="btn btn-sm btn-primary" target="_blank" href="/doctorresourcemgr/add">
                        <i class="fa fa-plus push-5-r"></i>添加资源
                    </a>
                </div>

                <div class="col-sm-11 col-xs-9">
                    <div class="col-sm-3" style="float: right; padding-right: 0px;">
                        <form class="form-horizontal push-5-t" action="/doctorresourcemgr/list" method="get">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索资源名" name="word" class="input-search form-inline form-control" value="<?=$word?>">
                                    <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                        <button type="submit" class="btn btn-primary">
                                            <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search">
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clear">

                </div>
            </div>

            <div class="col-md-12">
                <div class="table-responsive">
                <table class="table  table-bordered">
                <thead>
                    <tr>
                        <td width=140>ID</td>
                        <td width=140>创建日期</td>
                        <td width=140>资源名</td>
                        <td width=140>资源描述</td>
                        <td width=140>action</td>
                        <td width=140>method</td>
                        <td width=140>操作</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($doctorResources as $a) {
                            ?>
                            <tr>
                                <td><?= $a->id ?></td>
                                <td><?= $a->createtime ?></td>
                                <td><?= $a->name ?></td>
                                <td><?= $a->content ?></td>
                                <td><?= $a->action ?></td>
                                <td><?= $a->method ?></td>
                                <td>
                                     <a style="margin-right:20px" href="/doctorresourcemgr/modify?doctorresourceid=<?=$a->id?>">修改</a>
                                     <a class="a-delete" data-id="<?=$a->id?>">删除</a>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    <tr>
                        <td colspan=100 class="pagelink">
                            <?php include $dtpl."/pagelink.ctr.php";  ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
        $(document).on('click', '.a-delete', function(){
            var ret = confirm('确定要删除吗？');
            if (!ret) {
                return false;
            }
            var doctorresourceid = $(this).data('id');
            $.ajax({
                url: '/doctorresourcemgr/deletejson',
                type: 'POST',
                dataType: 'html',
                data: {
                    doctorresourceid: doctorresourceid
                },
                'success': function(d) {
                    console.log('--------', d);
                    if (d == 'ok') {
                        alert('删除成功');
                        location.reload();
                    }
                }
            });
        });
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
