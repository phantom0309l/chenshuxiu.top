<?php
$pagetitle = "微信推送文章评论审核列表 Comment";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.ganwucontent {
	font-size: 14px;
	color: #336699;
	line-height: 24px;
	letter-spacing: 1px;
	margin-top: 5px;
	font-weight: lighter;
	border: 1px solid #0000ee;
	background-color: #fff9f9;
	padding: 10px;
}

.content {
	font-size: 14px;
	color: #336699;
	line-height: 24px;
	letter-spacing: 1px;
	margin-top: 5px;
	font-weight: lighter;
	border: 0px solid #cccc33;
	background-color: #fff9f9;
	padding: 10px;
}

.ganwuffid {
	font-size: 16px;
	color: #336699;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-10">
        <div class="col-md-3 ">
            <div class="block block-bordered p10">
            <div class="block-content">
            <?php foreach( $lessons as $a ) {?>
                <div class="date-item">
                    <?= $a->title?>
                </div>
            <?php }?>
            </div>
            </div>
         </div>
            <div class="col-md-9">
            <div class="block block-bordered">
                <div class="block-content">
                <?php foreach( $comments as $a ) {?>
                <div class="content-item-sub">
                    <?php foreach( $a as $key => $b ) {?>
                    <?php if ($key == 0) {?>
                    <div style="border-bottom: dashed;">
                    <?php } else {?>
                    <div style="margin: 30px; border-bottom: dashed;">
                    <?php } ?>
                        <img src="<?=$img_uri ?>/static/img/ready/circle.png" style="height: 15px; width: 16px; vertical-align: middle; margin-top: -3px" />
                        <span class="ganwuffid"><?= $b->createtime ?> </span>
                        <p class="span-blue"><?= $b->user->name?></p>
                        <p class="content"><?= $b->content?>;</p>
                        <form action="/articlecommentmgr/auditreplyPost" method="post">
                            <input type="hidden" name="commentid" value="<?= $b->id ?>" />
                            <textarea class="ganwucontent" name="replycontent" style="width: 90%; height: 70%"><?= $b->replycontent?></textarea>
                            <input type="submit" value="回复" />
                        </form>
                        <?php if( $b->status == 1 ) {?>
                        <div>< 审核通过 ></div>
                        <?php }elseif( $b->status == 2 ){?>
                        <div>< 审核拒绝 ></div>
                        <?php }else{?>
                        <a style="color: green" href="/articlecommentmgr/auditpassPost?commentid=<?=$b->id?>">< 通过 ></a>
                        <a style="color: red" href="/articlecommentmgr/auditrefusePost?commentid=<?=$b->id?>">< 拒绝 ></a>
                        <?php }?>
                    </div>
                        <?php }?>
                </div>
                <?php }?>
                </div>
            </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(document).ready(function(){
        var cTags = $(".date-item");
        var cItems = $(".content-item-sub");
        $(".date-item").on("click", function(){
            var me = $(this);
            cTags.css("color","cadetblue");
            me.css("color","black");
            var index = me.index();
            cItems.hide();
            cItems.eq(index).show();
        });
    });
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
