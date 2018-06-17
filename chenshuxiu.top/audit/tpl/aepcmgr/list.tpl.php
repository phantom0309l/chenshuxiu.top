<?php
$pagetitle = "AEPC列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.qsheet_nav_one {
	width: 200px;
	line-height: 200%
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
			<?php if ($thepatient instanceof Patient) { ?>
	            <div class="searchBar">
	                <span>患者姓名：<?= $thepatient->name ?></span>
	                <span>所属医生：<?= $thepatient->doctor->name ?></span>
	            </div>
	            <div class="searchBar">
					<a href="/aepcmgr/add?papertplid=275143816&thepatientid=<?= $thepatient->id ?>" class="btn btn-default">添加AE</a>
					<a href="/aepcmgr/add?papertplid=275209326&thepatientid=<?= $thepatient->id ?>" class="btn btn-default">添加PC</a>
					<a href="/aepcmgr/add?papertplid=312586776&thepatientid=<?= $thepatient->id ?>" class="btn btn-default">添加AEPC</a>
	            </div>
			<?php } ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>创建时间</td>
						<td>患者</td>
                        <td>标题</td>
                        <td>填写人</td>
                        <td>事件编号</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($papers as $a) { ?>
                    <tr>
                        <td><?= $a->getCreateDayHi() ?></td>
                        <td><?= $a->patient->name ?> </td>
                        <td><?= $a->papertpl->title ?></td>
                        <td><?= $a->writer ?></td>
						<td>
							<?= AepcService::getContent($a->xanswersheetid, 275148346) == "" ? $a->xanswersheetid : AepcService::getContent($a->xanswersheetid, 275148346)?>
						</td>
                        <td>
							<?php if("AE" == $a->ename || "AEPC" == $a->ename){ ?>
	                            <a target="_blank" href="/aepcmgr/aePrint?xanswersheetid=<?= $a->xanswersheetid ?>">查看</a>
	                            <a target="_blank" href="/aepcmgr/modify?xanswersheetid=<?= $a->xanswersheetid ?>">修改</a>
	                            <a target="_blank" href="/aepcmgr/outputPDF?paperid=<?= $a->id ?>&type=ae">导出PDF</a>
								<button class="btn btn-default deleteAEPC" data-paperid=<?= $a->id ?>>删除</button>
	                            <div class="push-10-t">
									<a class="btn btn-success" target="_blank" href="/aepcmgr/addAEPCPost?papertplid=275143816&thepatientid=<?= $thepatient->id ?>&paperid=<?= $a->id ?>">复制--AE</a>
									<a class="btn btn-success" target="_blank" href="/aepcmgr/addAEPCPost?papertplid=312586776&thepatientid=<?= $thepatient->id ?>&paperid=<?= $a->id ?>">复制--AEPC</a>
								</div>
							<?php }else{ ?>
	                            <a target="_blank" href="/aepcmgr/pcPrint?xanswersheetid=<?= $a->xanswersheetid ?>">查看</a>
	                            <a target="_blank" href="/aepcmgr/modify?xanswersheetid=<?= $a->xanswersheetid ?>">修改</a>
	                            <a target="_blank" href="/aepcmgr/outputPDF?paperid=<?= $a->id ?>&type=pc">导出PDF</a>
								 <button class="btn btn-default deleteAEPC" data-paperid=<?= $a->id ?>>删除</button>
							<?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=6>
                            <?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
	</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
	var app = {
		  canClick : true,
		  init : function(){
			  var self = this;
			  self.handleRemove();
		  },
		  handleRemove : function(){
			  var self = this;
			  $(".deleteAEPC").on("click", function(){
				  var me = $(this);
				  if( !self.canClick ){
					  return;
				  }
				  if(false == confirm("您确定？这条数据敢删吗？你敢吗？")){
					  return;
				  }
				  self.canClick = false;
				  var paperid = me.data("paperid");
				  $.ajax({
					  url: '/aepcmgr/deleteJson',
					  type: 'post',
					  dataType: 'text',
					  data: {paperid: paperid}
				  })
				  .done(function(str) {
					  self.canClick = true;
					  if(str=="ok"){
						  me.parents("tr").hide();
						  alert("数据已清除");
					  }else{
						  alert('清除数据失败');
					  }
				  })
			  })
		  }
	  };

	  app.init();
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
