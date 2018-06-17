<?php
$pagetitle = "微信服务号[{$wxshop->name}]个性化菜单";
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
/*tab*/
.tab-menu {
    height: 30px;
    border-bottom: 1px solid #ddd;
    margin: 0px;
    padding: 0px;
}

li {
    margin: 0px;
    padding: 0px;
}

.tab-menu li {
    list-style: none;
    float: left;
    width: 100px;
    height: 30px;
    border: 1px solid #ddd;
    line-height: 30px;
    text-align: center;
    margin-left: 5px;
    background: #f5f5f5;
    cursor: pointer;
}
.tab-menuAuto li {
    list-style: none;
    float: left;
    width: auto;
    height: 30px;
    border: 1px solid #ddd;
    line-height: 30px;
    text-align: center;
    margin-left: 5px;
    background: #f5f5f5;
    cursor: pointer;
    padding: 0px 12px;
}

.tab-menu li.active {
    border-bottom: 1px solid #fff;
    background: #fff;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
		<input type="hidden" value="<?= $showurlencode ?>" id="showurlencode" />
        <section class="col-md-12">
	        	<div>
	                <p>
	                    <span>gh：<?= $wxshop->gh ?></span>
	                    <span>appid：<?= $wxshop->appid ?></span>
	                </p>
	            </div>
				<?php if( count($result) == 0 ){ ?>
			        <div>
		                <textarea class="menuData"></textarea>
			            <div>
			                <span class="btn btn-default addMenu">修改</span>
			            </div>
			        </div>
				<?php }else{ ?>
					<div class="tab">
					    <ul class="tab-menu clearfix tab-menuAuto">
							<?php foreach( $result as $i => $item){ ?>
						        <li class="<?= $i==0 ? 'active' : ''?>"><?= $item["name"]?>[<?= $item["groupid"] ?>]</li>
							<?php } ?>
					    </ul>
					    <div class="tab-content">
							<?php foreach( $result as $i => $item){ ?>
						        <div class="tab-content-item">
					                <textarea class="menuData"><?= $item["value"] ?></textarea>
						            <div>
						                <span class="btn btn-default modifyMenu">修改</span>
						            </div>
						        </div>
							<?php } ?>
					    </div>
					</div>
				<?php } ?>
		</section>
    </div>
    <div class="clear"></div>
<?php include $tpl . "/_footer.php"; ?>
    <script type="text/javascript">
        $(function(){
			$(document).on("click", ".tab-menu>li", function() {
				var me = $(this);
				var index = me.index();
				var tab = me.parents(".tab");
				var contents = tab.children(".tab-content").children(".tab-content-item");
				me.addClass("active").siblings().removeClass("active");
				contents.eq(index).show().siblings().hide();
			});

			$(".menuData").each(function(){
				var me = $(this);
	            var txt = me.val();
				if(txt){
		            me.val(JSON.stringify(JSON.parse(txt), null, "    "));
				}
			});

			//textArea隐藏的时候获取不到val 做个fix 处理
			$(".tab-content-item").hide();
			$(".tab-content-item").eq(0).show();

            $(".modifyMenu").on("click", function(){
				var me = $(this);
				var menuDataNode = me.parents(".tab-content-item").find(".menuData");
                if( confirm("检查完毕了？") ){
                    var txt = $.trim( menuDataNode.val() );
                    if( txt.length == 0 ){
                        alert("内容不能为空");
                        return;
                    }
                    var data = {
                        'wxshopid' : <?= $wxshop->id ?>,
                        'menuArrStr' : txt,
						'showurlencode' : $("#showurlencode").val()
                    };
                    $.ajax({
                        "type" : "post",
                        "data" : data,
                        "dataType" : "json",
                        "url" : "/wxshopmgr/wxConditionalcreatejson",
                        "success" : function(json){
                            if( json.menuid ){
                                alert("修改成功！");
                                window.location.href = window.location.href;
                            }else{
                                alert( json.errmsg );
                            }
                        }
                    })
                }
            });

            $(".addMenu").on("click", function(){
				var me = $(this);
				var menuDataNode = $(".menuData");
                if( confirm("检查完毕了？") ){
                    var txt = $.trim( menuDataNode.val() );
                    if( txt.length == 0 ){
                        alert("内容不能为空");
                        return;
                    }
                    var data = {
                        'wxshopid' : <?= $wxshop->id ?>,
                        'menuArrStr' : txt,
						'showurlencode' : $("#showurlencode").val()
                    };
                    $.ajax({
                        "type" : "post",
                        "data" : data,
                        "dataType" : "json",
                        "url" : "/wxshopmgr/wxConditionalcreatejson",
                        "success" : function(json){
                            if( json.menuid ){
                                alert("修改成功！");
                                window.location.href = window.location.href;
                            }else{
                                alert( json.errmsg );
                            }
                        }
                    })
                }
            });

        })
    </script>
</body>
</html>
