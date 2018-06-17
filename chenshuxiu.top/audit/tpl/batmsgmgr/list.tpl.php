<?php
$pagetitle = "医生群发消息列表 BatMsg";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
#content {
	width: 500px;
	height: 160px;
	border: 1px solid #ddd;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-6 content-left">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>医生</td>
                        <td>所属医院</td>
                        <td>信息类型</td>
                        <td>提交时间</td>
                        <td>审核</td>
                    </tr>
                </thead>
                <tbody>
                    <?php

					foreach ($batmsgs as $a) {
                        $doctor = $a->user->getDoctor();
                        ?>
                    <tr>
                        <td><?= $doctor->name?></td>
                        <td><?= $doctor->hospital->name?></td>
                        <td><?= $a->getType() ?></td>
                        <td><?= $a->createtime?></td>
                        <td>
                            <?php if($a->auditstatus==0){?>
                                <a href="#goAudit" class="goDetail" data-id=<?= $a->id ?>>审核</a>
                            <?php }else{ ?>
                                <a href="#goAudit" class="goDetail" data-id=<?= $a->id ?>>查看</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
        </section>

		<section class="col-md-6 content-right">
			<div class="col-md-6 content-right" id="goAudit"></div>
		</section>
		<div class="clear"></div>
		<?php
		$footerScript = <<<XXX
                $(function(){
                    $(".goDetail").on("click", function(e){
                        var node = $(this);
                        var id = node.data("id");
                        $.ajax({
                            "type" : "get",
                            "data" : {id : id},
                            "dataType" : "html",
                            "url" : "/batmsgmgr/onehtml",
                            "success" : function(data){
                                $(".content-right").html(data);
                                $(".content-right").show();
                            }
                        });

                    });

                    var getNeedData = function(){
                        var node = $("#content");
                        return {"id" : node.data("id"), "content" : node.val()};
                    };

                    //保存文本
                    $(document).on("click",".onlySave", function(){
                        var data = getNeedData();
                        if( !confirm("确定执行此操作？") ){
                            return;
                        }

                        $.ajax({
                            "type" : "post",
                            "data" : data,
                            "dataType" : "text",
                            "url" : "/batmsgmgr/modifyJson",
                            "success" : function(data){
                                if(data=="ok"){
                                    alert("保存成功");
                                }
                            }
                        });
                    });

                    //审核通过
                    $(document).on("click",".saveAndSend", function(){
                        var data = getNeedData();
                        if( !confirm("确定执行此操作？") ){
                            return;
                        }

                        $.ajax({
                            "type" : "post",
                            "data" : data,
                            "dataType" : "text",
                            "url" : "/batmsgmgr/passJson",
                            "success" : function(data){
                                if(data=="ok"){
                                    alert("审核成功");
                                }
                            }
                        });
                    });

                    //拒绝
                    $(document).on("click", ".refuse", function(){
                        var data = getNeedData();
                        if( !confirm("确定执行此操作？") ){
                            return;
                        }
                        $.ajax({
                            "type" : "post",
                            "data" : data,
                            "dataType" : "text",
                            "url" : "/batmsgmgr/refuseJson",
                            "success" : function(data){
                                if(data=="ok"){
                                    alert("已拒绝");
                                }
                            }
                        });
                    });
                });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
