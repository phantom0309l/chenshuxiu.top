<?php
$pagetitle = "患者列表 listforletter";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
#chartShell {
	width: 100%;
	/* height: 800px; */
	overflow: auto;
}

.trOnSeleted {
	background-color: #e6e6fa;
}

.trOnMouseOver {
	background-color: #e6e6fa;
}

#goTop {
	position: fixed;
	bottom: 20px;
	width: 40px;
	height: 40px;
	background: #ddd;
	padding: 5px 10px;
}

.pipeeventBox {
	position: absolute;
	width: 400px;
	padding: 20px;
	border: 1px solid #ddd;
	right: 0px;
	z-index: 10;
	background: #fff;
}

#pipeShell {
	padding-bottom: 30px;
}

#answersheet {
	z-index: 100
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

.tab-menu li.active {
	border-bottom: 1px solid #fff;
	background: #fff;
}

.mt10 {
	margin-top: 10px;
}

.mb10 {
	margin-bottom: 10px;
}

.ml10 {
	margin-left: 10px;
}

.remarkEventBox {
	margin-bottom: 10px;
}

.remarkEventBox-ta {
	width: 80%;
	border: 1px solid #ccc;
	padding: 5px;
	background: #fff;
	margin-bottom: 5px;
}

.contentBoxTitle {
	border-top: 1px solid #5c90d2;
	padding: 5px 10px 8px 10px;
	background: #eeeeee;
	margin-top: 10px;
}

.contentBoxBox1 {
	border-left: 1px solid #ddd;
	border-right: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
	padding: 10px;
}

.colorBox {
	margin: 5px 0px;
	padding: 10px;
	background: #E6E6FA;
}

.pgroupBox {
	margin: 5px 0px;
	padding: 10px 10px 5px;
	background: #E6E6FA;
}

.pgroupBox button {
	margin: 0px 5px 5px 0px;
}

.grayBgColorBox {
	margin: 5px 0px;
	padding: 10px;
	background: #f9f9f9;
}

@media ( max-width : 600px) {
	.col-md-12,.col-md-6 {
		padding-left: 3px;
		padding-right: 3px;
	}
	.pipeeventTrigger {
		display: none;
	}
}

.remarkBox {
	margin-bottom: 10px;
}

.remarkBox-ta {
	width: 80%;
	border: 1px solid #ccc;
	padding: 5px;
	background: #f5f5f5;
	margin-bottom: 5px;
}

.showRemarkBox {
	background: #fff;
	padding: 8px 10px;
	border: 1px solid blue;
	z-index: 10;
	width: 400px;
	border-radius: 3px;
	left: 55px;
	top: 3px;
}

.amrbtn {
	padding: 6px 10px 6px 30px;
	background: url(<?=$img_uri ?>/v3/img/voice.png) no-repeat 5px center;
	background-size: 20px;
}

.imgBrief img {
	width: 100%;
}

.pgroupid {
	width: 140px;
	border: 1px solid #ddd;
	height: 30px;
}

.flow-item{ margin-top: 10px;}
.flow-item-title{ height: 40px; line-height: 40px; background: #f7f7f7; font-size: 16px; color: #666; padding-left: 10px;}
.flow-item-content{ padding:10px;}
STYLE;
$pageScript = <<<SCRIPT
        function over(tr) {
            $(tr).addClass('trOnMouseOver');
        }
        function out(tr) {
            $(tr).removeClass('trOnMouseOver');
        }
        $(function () {
            $("#checkDoctor").on("change", function () {
                var val = parseInt($(this).val());
                var url = val == 0 ? location.pathname : location.pathname + '?doctorid=' + val;
                window.location.href = url;
            });
            $(".showPatientOneHtml").on("click", function () {
                $("tr.trOnSeleted").removeClass("trOnSeleted").removeClass("trOnMouseOver");
                $(this).parents("tr").addClass("trOnSeleted");
            });
        });
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12 contentShell">
        <section class="col-md-5 content-left">
            <div class="table-responsive">
                <table class="table border-top-blue patientList">
                <thead>
                    <tr>
                        <td>患者姓名</td>
                        <td>报到时间</td>
                        <td>所属医生</td>
                        <td>查看</td>
                    </tr>
                </thead>
                <tbody>
            <?php

            foreach ($patients as $a) {
                ?>
                <tr>
                        <td class="pr patientName">
                            <span><?= $a->getMaskName() ?></span>
                            <div class="pa showRemarkBox none"><?= $a->opsremark; ?></div>
                        </td>
                        <td><?= $a->getCreateMdHi() ?></td>
                        <td><?= $a->doctor->name ?></td>
                        <td>
                            <a href="#goPatientBase" data-patientname="<?= $a->name ?>" data-patientid="<?= $a->id ?>"  class="showLetterContent patientid-<?= $a->id ?>">查看</a>
                        </td>
                    </tr>
            <?php } ?>

            <tr>
                        <td colspan=10>
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
        <section class="col-md-7 content-right border1 pb10">
        </section>
    </div>
    <div class="clear"></div>
    <div id="goTop" class="none">Top</div>
    <?php include_once( $tpl. "/_thankbox.php" ); ?>
<?php
$footerScript = <<<XXX
    $(function () {
		var app = {
			flag : true,
			patientid : 0,
			pipeid : 0,
			init : function(){
				var self = this;
				self.showLetterContent();
				self.addThankLetterOnClick();
				self.addThankLetterQuickOnClick();
				self.goTopOnClick();
			},
			showLetterContent : function(){
				var self = this;
				$(".showLetterContent").on("click", function(){
					var me = $(this);
		            $('tr').removeClass('trOnMouseOver');
		            me.parents('tr').addClass('trOnMouseOver');
					var patientid = self.patientid = me.data("patientid");
					$.ajax({
						"type" : "get",
						"data" : {
							patientid : patientid
						},
						"dataType" : "html",
						"url" : "/patientmgr/getContentForLetterHtml",
						"success" : function(data) {
							$(".content-right").html(data);
						}
					});
				})
			},
			// 创建感谢留言（感谢信）
			addThankLetterOnClick : function() {
				var self = this;
				$(document).on("click", ".thank-btn", function() {
					var thankBox = $("#thankBox");
					var contentNode = thankBox.find(".thankBox-content");

					var content = $.trim( contentNode.val() );
					var typestr = 'thank';

					if (content.length == 0) {
						alert("请输入内容");
						return;
					}

					if( !self.flag ){
						return;
					}
					self.flag = false;

					$.ajax({
						"type" : "post",
						"data" : {
							"patientid" : self.patientid,
							"pipeid" : self.pipeid,
							"content" : content,
							"typestr" : typestr
						},
						"url" : "/lettermgr/addJson",
						"success" : function(data) {
							self.flag = true;
							var txt = "添加失败";
							if (data == 'ok') {
								txt = "添加成功";
							}
							$(".thankBox-notice").text(txt).fadeIn(0, function() {
								$(this).fadeOut(50, function(){
									contentNode.val("");
									$('#thankBox').modal('hide');
								});
							});
						}
					});
				});
			},
			// 快捷键创建感谢留言（感谢信）
			addThankLetterQuickOnClick : function() {
				var self = this;
				$(document).on("click", ".thankQuick", function() {
					var me = $(this);
					var flowItem = me.parents(".flow-item");
					self.pipeid = flowItem.data("pipeid");
					var content = $.trim(flowItem.find(".flow-item-content").text());
					var thankBox = $("#thankBox");
					thankBox.find(".thankBox-content").val( content );
				});
			},
			goTopOnClick : function() {
				var left = $(".content-left");
				var offset = left.offset();
				var goTop = $("#goTop");
				goTop.css({
					"left" : offset.left + left.width()
				});
				goTop.on("click", function() {
					$(window).scrollTop(0);
				});
				$(window).on("scroll", function() {
					if ($(window).scrollTop() > 800) {
						goTop.show();
					} else {
						goTop.hide();
					}
				});
			}
		};

		app.init();
    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
