<?php
$pagetitle = "合作医生列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.title {
	font-size: 18px;
	color: #337ab7;
}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="title">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        <span>合作库医生</span>
                    </div>
                </div>
                <div class="panel-body">
                    <p><?= $doctor_hezuo->name ?></p>
                    <p><?= $doctor_hezuo->hospital_name ?> <span class="gray"><?= $doctor_hezuo->hospital_name_2 ?></span></p>
                    <p><?= $doctor_hezuo->department ?> <?= $doctor_hezuo->title1 ?></p>
                    <p>doctorid=<?= $doctor_hezuo->doctorid ?></p>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="title">
                        <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                        <span>方寸库医生</span>
                    </div>
                </div>
                <div class="panel-body">
                  <?php if( count($doctors) > 0 ){ ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>id</td>
                                <td>医生详情</td>
                                <td>状态</td>
                                <td>操作</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doctors as $a) { ?>
                                <tr>
                                <td><?= $a->id ?></td>
                                <td>
                                    <p><?= $a->name ?> <span class="blue"><?= $a->user->username ?></span></p>
                                    <p><?= $a->hospital->name ?> <span class="gray"><?= $a->hospital->shortname ?></span></p>
                                    <p><?= $a->department ?> <?= $a->title ?></p>
                                </td>
                                <td>
                                        <?php if($doctor_hezuo->doctorid == $a->id){ ?>
                                            <span class="green">当前关联</span>
                                        <?php }else{ ?>
                                            <span class="red">未关联</span>
                                        <?php } ?>
                                    </td>
                                <td>
                                    <p>
                                        <?php if($doctor_hezuo->doctorid == $a->id){ ?>
                                            <span class="relationBtn btn btn-default" data-doctorhezuoid="<?= $doctor_hezuo->id ?>" data-doctorid="0">解绑</span>
                                        <?php }else{ ?>
                                            <span class="relationBtn btn btn-default" data-doctorhezuoid="<?= $doctor_hezuo->id ?>" data-doctorid="<?= $a->id ?>">关联</span>
                                        <?php } ?>

                                        </p>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                    </table>
                    </div>
                  <?php } ?>

                  <?php if($doctor_hezuo->doctorid < 1) { ?>
                    <p>
                        <a href="/doctor_hezuomgr/createDoctor?doctor_hezuoid=<?=$doctor_hezuo->id ?>" class="btn btn-primary">自动生成新医生</a>
                        <a href="/doctormgr/list" class="btn btn-primary">手工添加医生</a>
                    </p>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
        var app = {
            init : function(){
                var self = this;
                self.handleRelation();
            },
            handleRelation : function(){
                $(document).on("click", ".relationBtn", function(){
                    var me = $(this);
                    var doctor_hezuoid = me.data("doctorhezuoid");
                    var doctorid = me.data("doctorid");
                    $.ajax({
                        url: '/doctor_hezuomgr/relationJson',
                        type: 'post',
                        dataType: 'text',
                        data: {doctor_hezuoid: doctor_hezuoid, doctorid: doctorid}
                    })
                    .done(function() {
                        me.addClass('btn-primary').text("已关联或已解绑,请刷新(待小乔改进)");
                    })
                    .fail(function() {
                    })
                    .always(function() {
                    });

                })
            }
        }
        app.init();
    })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>