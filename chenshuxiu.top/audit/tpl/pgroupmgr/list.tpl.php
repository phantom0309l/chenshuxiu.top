<?php
$pagetitle = "分组列表 Pgroup";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.ml20{ margin-left: 20px;}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-6">
            <div class="searchBar">
                <a class="btn btn-success" href="/pgroupmgr/add">新建</a>
            </div>
            <div class="searchBar">
                <select class="typestr">
                    <option value="" <?= $typestr=="" ? 'selected' : ''?>>全部</option>
                    <?php
                        $typestrArr = Pgroup::getTypestrDescArr();
                        foreach ($typestrArr as $k => $v ) {
                    ?>
                        <option value="<?= $k ?>" <?= $typestr==$k ? 'selected' : ''?>><?= $v ?></option>
                    <?php } ?>
                </select>
                <span>不显示运营和患者均不可见组</span>
                <input type="checkbox" class="hide_all_closed" <?= $hide_all_closed==1 ? 'checked' : ''?>/>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>name</td>
                        <td>分组类型</td>
                        <td>患者可见</td>
                        <td>运营可见</td>
                        <td>修改基本组信息</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($pgroups as $i => $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->name ?></td>
                        <td><?= $a->getTypestrDesc() ?></td>
                        <td>
                            <?= $a->showinwx == 1 ? "开启" : "关闭"?>
                        </td>
                        <td>
                            <?= $a->showinaudit == 1 ? "开启" : "关闭"?>
                        </td>
                        <td class="text-right">
                            <a href="/pgroupmgr/modifyinfo?pgroupid=<?=$a->id?>" class="btn btn-success" target="_blank">修改</a>
                        </td>
                        <td class="text-right">
                            <a href="#" href="javaScript:" class="showDetail" data-pgroupid="<?= $a->id ?>">查看</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            </div>
        </section>
        <section class="col-md-6 content-right">

        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
        $(function(){
            var app = {
                init : function(){
                    var self = this;
                    self.handleShowDetail();
                    self.modifyShowInWx();
                    self.modifyShowInAudit();
                    self.modifyLevel();
                    self.typestrSelectChange();
                    self.hideAllClosedChange();
                },
                typestrSelectChange : function(){
                    var self = this;
                    $(".typestr").on("change", function(){
                        var params = self.getParamsStr();
                        window.location.href = "/pgroupmgr/list" + params;
                    })
                },
                hideAllClosedChange : function(){
                    var self = this;
                    $(".hide_all_closed").on("change", function(){
                        var params = self.getParamsStr();
                        window.location.href = "/pgroupmgr/list" + params;
                    })
                },
                handleShowDetail : function(){
                    var self = this;
                    $(document).on("click", ".showDetail", function(){
                        var me = $(this);
                        var pgroupid = me.data("pgroupid");
                        self.renderDetailHtml( pgroupid );
                    })
                },
        		renderDetailHtml : function(pgroupid) {
        			$.ajax({
        				"type" : "get",
        				"data" : {
        					pgroupid : pgroupid
        				},
        				"dataType" : "html",
        				"url" : "/pgroupmgr/detailHtml",
        				"success" : function(data) {
        					$(".content-right").html(data);
        				}
        			});
        		},
                modifyShowInWx : function(){
                    var self = this;
                    $(document).on("click", ".showInWxBtn", function(){
                        var me = $(this);
                        var value = me.data("value");
                        var pgroupid = self.getPgroupid();
            			$.ajax({
            				"type" : "post",
            				"data" : {
            					value : value,
                                pgroupid : pgroupid
            				},
            				"dataType" : "text",
            				"url" : "/pgroupmgr/modifyShowInWxJson",
            				"success" : function(data) {
            					me.addClass('btn-primary').siblings().removeClass('btn-primary');
            				}
            			});
                    })
                },
                modifyShowInAudit : function(){
                    var self = this;
                    $(document).on("click", ".showInAuditBtn", function(){
                        var me = $(this);
                        var value = me.data("value");
                        var pgroupid = self.getPgroupid();
            			$.ajax({
            				"type" : "post",
            				"data" : {
            					value : value,
                                pgroupid : pgroupid
            				},
            				"dataType" : "text",
            				"url" : "/pgroupmgr/modifyShowInAuditJson",
            				"success" : function(data) {
            					me.addClass('btn-primary').siblings().removeClass('btn-primary');
            				}
            			});
                    })
                },
                modifyLevel : function(){
                    var self = this;
                    $(document).on("click", ".level", function(){
                        var me = $(this);
                        var value = me.data("value");
                        var pgroupid = self.getPgroupid();
                        $.ajax({
                            "type" : "post",
                            "data" : {
                                value : value,
                                pgroupid : pgroupid
                            },
                            "dataType" : "text",
                            "url" : "/pgroupmgr/modifyLevelJson",
                            "success" : function(data) {
                                me.addClass('btn-primary').siblings().removeClass('btn-primary');
                            }
                        });
                    })
                },
                getPgroupid : function(){
                    return $("#pgroupid").val();
                },
                getParamsStr : function(){
                    var typestr = $(".typestr").val();
                    var hide_all_closed = 0;
                    if( $(".hide_all_closed").is(":checked") ){
                        var hide_all_closed = 1;
                    }
                    return "?typestr=" + typestr + "&hide_all_closed=" + hide_all_closed;
                }
            };
            app.init();
        })
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
