<?php
$pagetitle = "微信服务号[{$wxshop->name}]菜单";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.menuData {
	width: 90%;
	border: 1px solid #ddd;
	padding: 10px;
	font-size: 14px;
	height: 800px;
	overflow: auto;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
	<input type="hidden" value="<?= $showurlencode ?>" id="showurlencode" />
	<input type="hidden" value="<?=$wxshop->id?>" id="wxshopid" />
    <section class="col-md-12">
            <div>
                <p>
                    <span>gh：<?= $wxshop->gh ?></span>
                    <span>appid：<?= $wxshop->appid ?></span>
                    <a href="/wxshopmgr/wxconditional?wxshopid=<?= $wxshop->id ?>">个性化菜单</a>
                </p>
                <textarea class="menuData"><?= $menuData ?></textarea>
            </div>
            <div>
                <span class="btn btn-default" id="modifyMenu">修改</span>
            </div>
	</section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
	$(function(){
		var txt = $(".menuData").val();
		$('.menuData').val(JSON.stringify(JSON.parse(txt), null, "    "));

		$("#modifyMenu").on("click", function(){
			if( confirm("检查完毕了？") ){
				var txt = $.trim( $(".menuData").val() );
				if( txt.length == 0 ){
					alert("内容不能为空");
					return;
				}
				var wxshopid = $("#wxshopid").val();
				var data = {
					'wxshopid' : wxshopid,
					'menuArrStr' : txt,
					'showurlencode' : $("#showurlencode").val()
				};
				$.ajax({
					"type" : "post",
					"data" : data,
					"dataType" : "json",
					"url" : "/wxshopmgr/wxmenucreatejson",
					"success" : function(json){
						if( json.errmsg == 'ok' ){
							alert("修改成功！");
							window.location.href = window.location.href;
						}else{
							alert( json.errmsg );
						}
					}
				})
			}
		})
	});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
