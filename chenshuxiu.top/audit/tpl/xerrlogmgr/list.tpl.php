<?php
$pagetitle = "错误日志列表 Xerrlog";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<script type="text/javascript">
$(function(){
    var app = {
        init : function(){
        	var self = this;

            // 忽略按钮挂事件
        	self.handleIgnoreBtn();
        },

        handleIgnoreBtn : function(){
            var self = this;

            // 点击忽略按钮
            $(document).on("click", ".ignoreBtn", function(){
                var me = $(this);
                var xerrlogid = me.data('xerrlogid');

                if( !confirm("直接忽略本条?") ){
                    return;
                }

                $.ajax({
                    url: '/xerrlogmgr/ignoreJson',
                    type: 'GET',
                    dataType: 'json',
                    data: {xerrlogid : xerrlogid},
                    success: function(json){
                        alert(json.data);
                        if(json.data=='success'){
                            // 删除tr
                            $('#tr_'+xerrlogid).remove();
                        }
                    }
                });
            });
        },
    };
	app.init();
});
</script>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/xerrlogmgr/list" method="get">
                <div class="mt10">
                    <label>错误类型: </label>
                        <?= HtmlCtr::getRadioCtrImp(Xerrlog::getLevels(), 'level', $level, ' '); ?>
                    <br />
                    <label>状态筛选: </label>
                        <?= HtmlCtr::getRadioCtrImp(Xerrlog::getStatuss(), 'status', $status, ' '); ?>
                    <br />
                    <input type="submit" class="btn btn-success" value='筛选' />
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            id
                            <br />
                            <span class="gray">createtime</span>
                            <br />
                            xunitofworkid
                        </th>
                        <th>
                            level
                            <br />
                            status
                            <br />
                            auditorid
                        </th>
                        <th>操作</th>
                        <th>
                            content
                            <br />
                            remark
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($xerrlogs as $i => $a) {
                        ?>
                    <tr id="tr_<?= $a->id ?>">
                        <td><?= $pagelink->getFirstNoOfThePage() + $i; ?></td>
                        <td><?= $a->id ?><br />
                            <span class="gray"><?= $a->createtime ?></span>
                            <br />
                            <a target="_blank" href="/xobjlogmgr/list?xunitofworkid=<?= $a->xunitofworkid ?>"><?= $a->xunitofworkid ?></a>
                            <br />
                            <a target="_blank" href="http://tool.fangcunhulian.cn/?logid=<?= $a->xunitofworkid ?>&env=prod&user=www&date=<?= $a->getDate() ?>&verbose=1">明细 [fangcun fangcun]</a>
                        </td>
                        <td>
                            <?= $a->level; ?>
                            <br />
                            <span id="desc_<?= $a->id ?>"><?= $a->getStatusDesc(); ?></span>
                            <br />
                            <?= $a->auditor->name?>
                        </td>
                        <td>
                            <?php if($a->isNew()){ ?>
                            <span class="btn btn-default ignoreBtn" data-xerrlogid='<?= $a->id ?>'>忽略</span>
                            <a class="btn btn-default mt10" target="_blank" href="/xerrlogmgr/one?xerrlogid=<?= $a->id ?>">处理</a>
                            <?php }else{ ?>
                            <a class="btn btn-default mt10" target="_blank" href="/xerrlogmgr/one?xerrlogid=<?= $a->id ?>">修改</a>
                            <?php }?>
                        </td>
                        <td>
                            <pre><?= $a->content; ?></pre>
                            <span class="f16 blue">工程师备注:</span>
                            <pre><?= $a->remark; ?></pre>
                        </td>
                    </tr>
                <?php
                    }
                    ?>
                    <tr>
                        <td colspan="4" class="pagelink" style="text-align: left"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
